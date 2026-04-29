<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\AuthorityRequest;
use App\Models\Authority;
use App\Models\AuthorityBalanceHistory;
use App\Models\WalletTransfer;
use Illuminate\Support\Facades\Http;

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
        return view($this->viewPath . 'create');
    }

    public function store(AuthorityRequest $request)
    {
        $authority = Authority::create($request->all());

        if ($authority) {
            // Automatically register on the blockchain to get the wallet address
            $adminApiUrl = env('ADMIN_API_BASE_URL');
            if ($adminApiUrl) {
                try {
                    $authResponse = Http::timeout(180)->post($adminApiUrl . '/api/v1/admin/authority', [
                        'uuid'            => $authority->id,
                        'naans'           => [],
                        'fund_amount_eth' => 0.05,
                    ]);

                    if ($authResponse->successful()) {
                        $data = $authResponse->json();
                        $authority->wallet_address = $data['wallet_address'] ?? null;
                        $authority->balance = $data['balance_eth'] ?? 0;
                        $authority->status = 'active';
                        $authority->save();
                    }
                } catch (\Throwable $th) {
                    // Fail silently for the user, but the authority remains 'pending'
                    // They can manually click 'Register' on the show page later.
                }
            }

            return redirect()
                ->route('authorities.show', $authority->id)
                ->with(['message' => 'Authority created successfully.', 'code' => 'success']);
        }

        return redirect()
            ->route('authorities.create')
            ->with(['message' => 'Error creating Authority. Try again!', 'code' => 'danger']);
    }

    public function show(Authority $authority)
    {
        $authority->load('institutions', 'account', 'blockchains');
        return view($this->viewPath . 'show', compact('authority'));
    }

    public function edit(Authority $authority)
    {
        return view($this->viewPath . 'edit', compact('authority'));
    }

    public function update(AuthorityRequest $request, Authority $authority)
    {
        $authority->fill($request->all())->save();

        return redirect()
            ->route('authorities.show', $authority->id)
            ->with(['message' => 'Authority updated successfully.', 'code' => 'success']);
    }

    public function destroy(Authority $authority)
    {
        $authority->delete();

        return redirect()
            ->route('authorities.index')
            ->with(['message' => 'Authority deleted successfully.', 'code' => 'success']);
    }

    public function registerAuthority(Authority $authority)
    {
        $adminApiUrl = env('ADMIN_API_BASE_URL');

        if (!$adminApiUrl) {
            return redirect()->back()->with(['message' => 'ADMIN_API_BASE_URL not configured.', 'code' => 'danger']);
        }

        try {
            $authResponse = Http::timeout(180)->post($adminApiUrl . '/api/v1/admin/authority', [
                'uuid'            => $authority->id,
                'naans'           => [],
                'fund_amount_eth' => 0.05,
            ]);

            // 409 = already exists — acceptable
            if (!$authResponse->successful() && $authResponse->status() !== 409) {
                return redirect()->back()->with([
                    'message' => 'Error registering Authority on smart contract: ' . $authResponse->body(),
                    'code'    => 'danger',
                ]);
            }

            if ($authResponse->successful() || $authResponse->status() === 409) {
                $data = $authResponse->json();
                if (isset($data['wallet_address'])) {
                    $authority->wallet_address = $data['wallet_address'];
                    $authority->balance = $data['balance_eth'] ?? 0;
                }
            }

            $authority->status = 'active';
            $authority->save();

            // Log the registration transfer
            WalletTransfer::create([
                'authority_id' => $authority->id,
                'amount' => 0.05,
                'tx_hash' => 'registration-funding'
            ]);

            return redirect()->back()->with([
                'message' => 'Authority successfully registered on the blockchain.',
                'code'    => 'success',
            ]);

        } catch (\Throwable $th) {
            return redirect()->back()->with([
                'message' => 'Error connecting to API: ' . $th->getMessage(),
                'code'    => 'danger',
            ]);
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
        $request->validate([
            'amount' => 'required|numeric|min:0.0001'
        ]);

        $adminApiUrl = env('ADMIN_API_BASE_URL');
        if (!$adminApiUrl) {
            return response()->json(['success' => false, 'message' => 'Admin API not configured.'], 500);
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

                return response()->json([
                    'success' => true, 
                    'message' => 'Successfully injected ' . $request->amount . ' dark.',
                    'tx_hash' => $response->json('transaction_hash')
                ]);
            }

            return response()->json([
                'success' => false, 
                'message' => 'Failed to fund wallet: ' . $response->body()
            ], $response->status());

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false, 
                'message' => 'Error connecting to Admin API: ' . $th->getMessage()
            ], 500);
        }
    }

    public function authorizeNaan(\Illuminate\Http\Request $request, Authority $authority)
    {
        $request->validate([
            'naan' => 'required|string|max:50'
        ]);

        $adminApiUrl = env('ADMIN_API_BASE_URL');
        if (!$adminApiUrl) {
            return response()->json(['success' => false, 'message' => 'Admin API not configured.'], 500);
        }

        try {
            $response = Http::timeout(60)->post($adminApiUrl . '/api/v1/admin/authority/' . $authority->id . '/authorize-naan', [
                'naan' => $request->naan
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'NAAN ' . $request->naan . ' successfully authorized!'
                ]);
            }

            return response()->json([
                'success' => false, 
                'message' => 'Failed to authorize NAAN: ' . $response->body()
            ], $response->status());

        } catch (\Throwable $th) {
            return response()->json([
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
