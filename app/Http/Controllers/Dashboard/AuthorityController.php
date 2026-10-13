<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AuthorityRequest;
use App\Models\Authority;
use App\Models\AuthorityBalanceHistory;
use App\Models\WalletTransfer;
use App\Services\AdminAuthorityApi;
use App\Support\Ed25519PublicKey;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class AuthorityController extends Controller
{
    private $viewPath = 'dashboard.authority.';

    public function index()
    {
        $total = Authority::count();
        $authorities = Authority::withCount('institutions')
            ->orderBy('name')
            ->paginate(config('pagination.default'));

        return view($this->viewPath . 'index', compact('authorities', 'total'));
    }

    public function create()
    {
        $oldToken = old('creation_token');
        if (session('errors') && session('errors')->has('creation_token')) {
            $oldToken = null;
        }
        $creationToken = $oldToken ?: Crypt::encryptString(json_encode([
            'user_id' => (string) auth()->id(),
            'nonce' => (string) Str::uuid(),
        ]));
        return view($this->viewPath . 'create', compact('creationToken'));
    }

    public function store(AuthorityRequest $request)
    {
        $data = $request->validated();
        try {
            $token = json_decode(Crypt::decryptString($data['creation_token']), true);
            if (!is_array($token) || ($token['user_id'] ?? null) !== (string) $request->user()->id
                || !Str::isUuid($token['nonce'] ?? '')) {
                throw new \RuntimeException('Invalid creation token.');
            }
        } catch (\Throwable $error) {
            throw ValidationException::withMessages([
                'creation_token' => 'Reload the New authority page before submitting.',
            ]);
        }
        $requestKey = hash('sha256', $token['user_id'] . ':' . $token['nonce']);
        unset($data['creation_token']);
        if (!empty($data['client_public_key'])) {
            $data['client_public_key'] = Ed25519PublicKey::normalize($data['client_public_key']);
        }
        $authority = Authority::where('creation_request_key', $requestKey)->first();
        if ($authority) {
            return $this->existingCreation($authority);
        }
        try {
            $authority = new Authority($data);
            $authority->creation_request_key = $requestKey;
            // The unique key arbitrates concurrent requests before any Admin API call.
            $authority->save();
        } catch (QueryException $error) {
            $authority = Authority::where('creation_request_key', $requestKey)->first();
            if (!$authority) {
                throw $error;
            }
            return $this->existingCreation($authority);
        }
        $result = $this->registerOnChain($authority);
        return redirect()->route('authorities.show', $authority->id)->with($result);
    }

    private function existingCreation(Authority $authority)
    {
        return redirect()->route('authorities.show', $authority->id)->with([
            'message' => 'This creation request was already accepted. No new authority was created. Check the registration status below.',
            'code' => 'info',
        ]);
    }

    private function registerOnChain(Authority $authority): array
    {
        try {
            $api = app(AdminAuthorityApi::class);
            $response = $api->register($authority->id, $authority->client_public_key);
            if ($response->status() === 409) {
                // Existence is not evidence of current active status or key assignment.
                $response = $api->authority($authority->id);
            }
            if (!$response->successful() || !$response->json('wallet_address')) {
                return ['message' => 'Authority saved locally. Blockchain registration could not be confirmed. Retry registration from this page.', 'code' => 'warning'];
            }
            $authority->wallet_address = $response->json('wallet_address');
            $authority->balance = $response->json('balance_eth') ?? 0;
            $authority->status = $response->json('active') ? 'active' : 'disabled';
            $authority->save();
            return ['message' => 'Authority registration confirmed. Check Minter access below for the current public key.', 'code' => 'success'];
        } catch (\Throwable $error) {
            return ['message' => 'Authority saved locally. Admin API could not confirm blockchain registration. Check its status before retrying.', 'code' => 'warning'];
        }
    }

    public function show(Authority $authority)
    {
        $authority->load('institutions', 'account', 'blockchains', 'naans');
        $clientKeyState = ['status' => 'pending', 'public_key' => $authority->client_public_key, 'key_id' => null];
        if ($authority->wallet_address || $authority->isRegistered()) {
            $clientKeyState = ['status' => 'unavailable', 'public_key' => null, 'key_id' => null];
            try {
                $response = app(AdminAuthorityApi::class)->clientKey($authority->id);
                if ($response->successful() && $response->json('uuid') === $authority->id && is_bool($response->json('active'))) {
                    $key = $response->json('client_public_key');
                    $key = $key === null ? null : Ed25519PublicKey::normalize($key);
                    $clientKeyState = [
                        'status' => $response->json('active') ? ($key ? 'registered' : 'missing') : 'inactive',
                        'public_key' => $key,
                        'key_id' => $key ? hash('sha256', base64_decode($key, true)) : null,
                    ];
                }
            } catch (\Throwable $error) {
                // Do not confuse an unavailable registry with an absent key.
            }
        }
        $ownership = null;
        try {
            $response = app(AdminAuthorityApi::class)->ownership($authority->id);
            if ($response->successful()) { $ownership = $response->json(); }
        } catch (\Throwable $error) {}
        return view($this->viewPath . 'show', compact('authority', 'clientKeyState', 'ownership'));
    }

    public function addNaans(Authority $authority)
    {
        $assignedNaanIds = $authority->naans()->pluck('naans.id')->toArray();
        $availableNaans = \App\Models\Naan::whereNotIn('id', $assignedNaanIds)->orderBy('naan')->get();
        return view($this->viewPath . 'add-naans', compact('authority', 'availableNaans'));
    }

    public function syncNaans(\Illuminate\Http\Request $request, Authority $authority)
    {
        $request->validate([
            'naans' => 'required|array|min:1',
            'naans.*' => 'required|integer|distinct|exists:naans,id'
        ]);

        $newNaansIds = $request->naans ?? [];
        
        // Sync local database without removing existing ones
        $authority->naans()->syncWithoutDetaching($newNaansIds);

        // Call admin_api to authorize newly added NAANs on the blockchain
        $adminApiUrl = env('ADMIN_API_BASE_URL');
        $apiErrors = [];

        if ($adminApiUrl && !empty($newNaansIds)) {
            $addedNaans = \App\Models\Naan::whereIn('id', $newNaansIds)->get();
            foreach ($addedNaans as $naanObj) {
                try {
                    $response = Http::timeout(60)->post($adminApiUrl . '/api/v1/admin/authority/' . $authority->id . '/authorize-naan', [
                        'naan' => $naanObj->naan
                    ]);

                    if (!$response->successful()) {
                        $apiErrors[] = "Failed to authorize NAAN {$naanObj->naan}: " . $response->body();
                    }
                } catch (\Throwable $th) {
                    $apiErrors[] = "Error connecting to Admin API for NAAN {$naanObj->naan}: " . $th->getMessage();
                }
            }
        }

        if (count($apiErrors) > 0) {
            return redirect()->route('authorities.show', $authority->id)->with([
                'message' => 'Database assigned, but blockchain errors occurred: ' . implode(' | ', $apiErrors),
                'code' => 'warning'
            ]);
        }

        return redirect()->route('authorities.show', $authority->id)->with(['message' => 'NAANs assigned and authorized successfully.', 'code' => 'success']);
    }

    public function edit(Authority $authority)
    {
        return view($this->viewPath . 'edit', compact('authority'));
    }

    public function update(AuthorityRequest $request, Authority $authority)
    {
        $authority->fill($request->validated())->save();

        return redirect()
            ->route('authorities.show', $authority->id)
            ->with(['message' => 'Authority updated successfully.', 'code' => 'success']);
    }

    public function deactivate(\Illuminate\Http\Request $request, Authority $authority)
    {
        $request->validate(['confirmation' => ['required', 'in:deactivate']]);
        try {
            $response = app(AdminAuthorityApi::class)->deactivate($authority->id);
            if (!$response->successful()) {
                return back()->with(['message' => 'Authority could not be deactivated. Transfer all PIDs still owned on blockchain first. If the inventory is unavailable, restore it before retrying.', 'code' => 'warning']);
            }
            $authority->status = 'disabled';
            $authority->save();
            return back()->with(['message' => 'Authority deactivated. Its record and history have been retained.', 'code' => 'success']);
        } catch (\Throwable $error) {
            return back()->with(['message' => 'Deactivation could not be confirmed. Refresh the authority status before retrying.', 'code' => 'warning']);
        }
    }

    public function pids(\Illuminate\Http\Request $request, Authority $authority)
    {
        $page = max(1, (int) $request->query('page', 1));
        $ownership = null;
        try {
            $response = app(AdminAuthorityApi::class)->pids($authority->id, $page);
            if ($response->successful()) { $ownership = $response->json(); }
        } catch (\Throwable $error) {}
        $targets = Authority::where('id', '!=', $authority->id)->where('status', 'active')->orderBy('name')->get();
        return view($this->viewPath . 'pids', compact('authority', 'ownership', 'targets', 'page'));
    }

    public function transferPids(\Illuminate\Http\Request $request, Authority $authority)
    {
        $data = $request->validate([
            'target_authority_id' => ['nullable', 'uuid', 'required_without:external_target_authority_id'],
            'external_target_authority_id' => ['nullable', 'uuid', 'required_without:target_authority_id'],
            'arks' => ['required', 'array', 'min:1', 'max:50'],
            'arks.*' => ['required', 'string', 'max:255', 'distinct'],
            'reason' => ['required', 'string', 'max:2000'],
        ]);
        if (!empty($data['target_authority_id']) && !empty($data['external_target_authority_id']) && $data['target_authority_id'] !== $data['external_target_authority_id']) {
            throw ValidationException::withMessages(['external_target_authority_id' => 'Choose one destination: a listed authority or an external UUID.']);
        }
        $data['target_authority_id'] = $data['target_authority_id'] ?? $data['external_target_authority_id'];
        unset($data['external_target_authority_id']);
        if ($data['target_authority_id'] === $authority->id) {
            throw ValidationException::withMessages(['target_authority_id' => 'Choose a different authority.']);
        }
        $results = [];
        $complete = true;
        foreach ($data['arks'] as $ark) {
            try {
                $response = app(AdminAuthorityApi::class)->transferOwner([
                    'ark' => $ark, 'source_authority_id' => $authority->id,
                    'target_authority_id' => $data['target_authority_id'], 'reason' => $data['reason'],
                ]);
                $ok = $response->successful() && $response->json('status') === 'complete';
                $results[] = ['ark' => $ark, 'status' => $ok ? 'complete' : 'not confirmed', 'transaction_hash' => $response->json('transaction_hash')];
                if (!$ok) { $complete = false; break; }
            } catch (\Throwable $error) {
                $results[] = ['ark' => $ark, 'status' => 'not confirmed', 'transaction_hash' => null];
                $complete = false;
                break;
            }
        }
        $redirect = redirect()->route('authorities.pids', $authority->id)->with([
            'message' => $complete ? 'Selected PIDs transferred on blockchain. Minter databases were not modified.' : 'Transfer stopped at the first unconfirmed PID. Completed transfers remain applied. Check ownership and retry the same destination to reconcile the remaining operation.',
            'code' => $complete ? 'success' : 'warning', 'transfer_results' => $results,
        ]);
        return $complete ? $redirect : $redirect->withInput($data);
    }

    public function registerAuthority(Authority $authority)
    {
        return redirect()->back()->with($this->registerOnChain($authority));
    }

    public function saveClientKey(\Illuminate\Http\Request $request, Authority $authority)
    {
        $data = $request->validate([
            'client_public_key' => ['required', 'string', 'max:4096', new \App\Rules\Ed25519PublicKey],
        ]);
        $key = Ed25519PublicKey::normalize($data['client_public_key']);
        if (!$authority->wallet_address && $authority->status === 'pending') {
            $authority->client_public_key = $key;
            $authority->save();
            return redirect()->back()->with(['message' => 'Public key saved for the pending authority. Register the authority to apply it on blockchain.', 'code' => 'info']);
        }
        try {
            $response = app(AdminAuthorityApi::class)->replaceClientKey($authority->id, $key);
            if (!$response->successful() || $response->json('status') !== 'success' || !$response->json('transaction_hash')) {
                return redirect()->back()->withInput()->with(['message' => 'Public key change could not be confirmed. Check the current key before retrying.', 'code' => 'danger']);
            }
            // Local copy follows confirmation; blockchain remains the source of truth.
            $authority->client_public_key = $key;
            $authority->save();
            return redirect()->back()->with(['message' => 'Public key registered on blockchain. The authority, wallet and PIDs are unchanged.', 'code' => 'success']);
        } catch (\Throwable $error) {
            return redirect()->back()->withInput()->with(['message' => 'Admin API could not confirm the public key change. Check the current key before retrying.', 'code' => 'danger']);
        }
    }

    public function apiData(Authority $authority)
    {
        $adminApiUrl = env('ADMIN_API_BASE_URL');
        if (!$adminApiUrl) {
            return response()->json(['error' => 'API not configured'], 500);
        }

        try {
            $authResponse = Http::timeout(5)->get($adminApiUrl . '/api/v1/admin/authority/' . $authority->id);
            $balanceResponse = Http::timeout(5)->get($adminApiUrl . '/api/v1/admin/authority/' . $authority->id . '/balance');

            $naansCount = 0;
            $balance = $authority->balance; // fallback to local

            if ($authResponse->successful()) {
                $naansCount = count($authResponse->json('naans') ?? []);
            }
            if ($balanceResponse->successful()) {
                $balance = $balanceResponse->json('balance_eth') ?? $balance;
                
                // Update local balance and wallet address
                $authority->balance = $balance;
                if ($balanceResponse->json('wallet_address')) {
                    $authority->wallet_address = $balanceResponse->json('wallet_address');
                }
                $authority->save();

                // Snapshot for history if changed or first time
                $lastHistory = $authority->balanceHistories()->latest()->first();
                if (!$lastHistory || $lastHistory->balance != $balance) {
                    $authority->balanceHistories()->create(['balance' => $balance]);
                }
            }

            return response()->json([
                'naans_count' => $naansCount,
                'naans' => $authResponse->json('naans') ?? [],
                'balance' => number_format((float)$balance, 4) . ' dark',
                'wallet_address' => $authority->wallet_address,
            ]);

        } catch (\Throwable $th) {
            // Fallback to local DB if API fails
            return response()->json([
                'naans_count' => '?',
                'naans' => [],
                'balance' => number_format((float)$authority->balance, 4) . ' dark',
                'wallet_address' => $authority->wallet_address,
                'error' => 'API Offline'
            ]);
        }
    }

    public function fund(\Illuminate\Http\Request $request, Authority $authority)
    {
        $reply = function (array $payload, int $status = 200) use ($request, $authority) {
            if ($request->expectsJson()) {
                return response()->json($payload, $status);
            }
            return redirect()->route('authorities.show', $authority->id)->with([
                'message' => $payload['message'],
                'code' => $payload['success'] ? 'success' : 'danger',
            ]);
        };
        $request->validate([
            'amount' => 'required|numeric|min:0.0001'
        ]);

        $adminApiUrl = env('ADMIN_API_BASE_URL');
        if (!$adminApiUrl) {
            return $reply(['success' => false, 'message' => 'Admin API not configured.'], 500);
        }

        try {
            $response = Http::timeout(120)->post($adminApiUrl . '/api/v1/admin/authority/' . $authority->id . '/fund', [
                'amount_eth' => (float) $request->amount
            ]);

            if ($response->successful()) {
                // Log the transfer
                WalletTransfer::create([
                    'authority_id' => $authority->id,
                    'amount' => (float) $request->amount,
                    'tx_hash' => $response->json('transaction_hash')
                ]);

                return $reply([
                    'success' => true, 
                    'message' => 'Successfully injected ' . $request->amount . ' dark.',
                    'tx_hash' => $response->json('transaction_hash')
                ]);
            }

            return $reply([
                'success' => false, 
                'message' => 'Failed to fund wallet: ' . $response->body()
            ], $response->status());

        } catch (\Throwable $th) {
            return $reply([
                'success' => false, 
                'message' => 'The transfer could not be confirmed. Check the balance and transfer status before retrying.'
            ], 500);
        }
    }

    public function authorizeNaan(\Illuminate\Http\Request $request, Authority $authority)
    {
        $reply = function (array $payload, int $status = 200) use ($request, $authority) {
            if ($request->expectsJson()) {
                return response()->json($payload, $status);
            }
            return redirect()->route($payload['success'] ? 'authorities.show' : 'authorities.addNaans', $authority->id)
                ->withInput($request->only('naan'))->with([
                    'message' => $payload['message'],
                    'code' => $payload['success'] ? 'success' : 'danger',
                ]);
        };
        $request->validate([
            'naan' => 'required|string|max:50|exists:naans,naan'
        ]);

        $adminApiUrl = env('ADMIN_API_BASE_URL');
        if (!$adminApiUrl) {
            return $reply(['success' => false, 'message' => 'Admin API not configured.'], 500);
        }

        try {
            $response = Http::timeout(60)->post($adminApiUrl . '/api/v1/admin/authority/' . $authority->id . '/authorize-naan', [
                'naan' => $request->naan
            ]);

            if ($response->successful()) {
                return $reply([
                    'success' => true, 
                    'message' => 'NAAN ' . $request->naan . ' successfully authorized!'
                ]);
            }

            return $reply([
                'success' => false, 
                'message' => 'Failed to authorize NAAN: ' . $response->body()
            ], $response->status());

        } catch (\Throwable $th) {
            return $reply([
                'success' => false, 
                'message' => 'Error connecting to Admin API: ' . $th->getMessage()
            ], 500);
        }
    }

    public function balanceHistoryData(Authority $authority)
    {
        $histories = $authority->balanceHistories()
            ->orderBy('created_at', 'asc')
            ->get();

        $labels = [];
        $data = [];

        foreach ($histories as $history) {
            $labels[] = $history->created_at->format('d/m H:i:s');
            $data[] = $history->balance;
        }

        // If no history, show current balance as a single point
        if (count($data) === 0) {
            $labels[] = now()->format('d/m H:i:s');
            $data[] = $authority->balance ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }
}
