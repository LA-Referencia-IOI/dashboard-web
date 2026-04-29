<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\BlockchainRequest;
use App\Models\User;
use App\Models\Blockchain;
use App\Models\MasterBalanceHistory;
use App\Models\WalletTransfer;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class BlockchainController extends Controller
{
    private $viewPath = 'dashboard.blockchain.';

    public function index(Request $request)
    {
        $blockchains = Blockchain::orderBy('type')->paginate(config('pagination.default'));
        
        // Fetch all wallet transfers for the ledger
        $transfers = WalletTransfer::with('authority')->orderBy('created_at', 'desc')->get();

        return view($this->viewPath . 'index', compact('blockchains', 'transfers'));
    }

    public function masterWalletData()
    {
        $adminApiUrl = env('ADMIN_API_BASE_URL');
        $currentBalance = 0;
        
        if ($adminApiUrl) {
            try {
                $statusResponse = Http::timeout(5)->get($adminApiUrl . '/api/v1/admin/status');
                if ($statusResponse->successful()) {
                    $currentBalance = $statusResponse->json('admin_balance_eth') ?? 0;
                    
                    // Snapshot if changed
                    $lastHistory = MasterBalanceHistory::latest()->first();
                    if (!$lastHistory || $lastHistory->balance != $currentBalance) {
                        MasterBalanceHistory::create(['balance' => $currentBalance]);
                    }
                }
            } catch (\Throwable $th) {
                // Ignore API errors, just return local history if available
            }
        }

        $histories = MasterBalanceHistory::orderBy('created_at', 'asc')->get();
        $labels = [];
        $data = [];

        foreach ($histories as $history) {
            $labels[] = $history->created_at->format('d/m H:i:s');
            $data[] = $history->balance;
        }

        // If no history at all
        if (count($data) === 0) {
            $labels[] = now()->format('d/m H:i:s');
            $data[] = $currentBalance;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'current_balance' => number_format((float)$currentBalance, 4) . ' dark'
        ]);
    }

    public function showLogs()
    {

        return view($this->viewPath .'monitor');
    }

    public function fetchLogs()
    {
        // URL da API que retorna os logs
        $apiUrl = "http://api-01.dark-pid.net/monitor-logs";

        try {
            // Faz a requisição GET à API
            $response = Http::get($apiUrl);
            // Verifica se a requisição foi bem-sucedida
            if ($response->failed()) {
                die("Error fetching logs from API: " . $response->status());
            }

            // Obtém o conteúdo da resposta como texto
            $logs = $response->body();

            // Converte os logs em array de linhas
            $logsArray = explode("\n", trim($logs));
            if (empty($logsArray)) {
                return 'No logs found or empty response from API.';
            }

            // Gera o HTML para exibir os logs
            $logHtml = '';
            foreach ($logsArray as $line) {
                $logHtml .= '<div class="log-line">' . htmlspecialchars($line) . '</div>';
            }

            return $logHtml;

        } catch (\Exception $e) {
            die("Error fetching logs from API: " . $e->getMessage());
        }
    }

    public function backup()
    {
        $apiUrl = "http://api-01.dark-pid.net/backup-bc";
        $pathQuerry = "?path=/home/ubuntu/backup-darkpid"; 

        $endpoint = $apiUrl.$pathQuerry;
        // dd($endpoint);

        try {
    
            $response = Http::get($endpoint);
            // checking the request
            if ($response->failed()) {
                die("Error fetching logs from API: " . $response->status());
            }

            // Obtém o conteúdo da resposta como texto
            $resp = $response->json();

            $totalFiles = $resp["totalFiles"];

            $lastModified = $resp["lastModified"];

            $lastModified = Carbon::parse($lastModified);

            $lastModified = $lastModified->format('F d, Y H:i:s') . ' UTC+0'; 

            $folderSizeMB = $resp["folderSizeMB"];

            $folderSizeGB = $folderSizeMB / 1024;

            $folderSizeMB = number_format($folderSizeGB, 2);
                
        } catch (\Exception $e) {
            // die("Error fetching logs from API: " . $e->getMessage());
            $totalFiles = 'undefined';

            $lastModified = 'undefined';

            $folderSizeMB = 'undefined';
        }

        return view($this->viewPath .'backup', compact('totalFiles', 'lastModified', 'folderSizeMB'));
    }


    public function create()
    {
       
        return view($this->viewPath . 'create');
    }

    public function store(BlockchainRequest $request)
    {
        $blockchain = Blockchain::create($request->all());

        if ($blockchain) {
            return redirect()
                ->route('blockchains.index')
                ->with(['message' => 'Successfully registered.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('blockchain.create')
                ->with(['message' => 'Error when registering. Try again!', 'code' => 'danger']);
        }
    }

    public function edit(Blockchain $blockchain)
    {
       
        return view($this->viewPath . 'edit', ['blockchain' => $blockchain]);
    }

    public function update(BlockchainRequest $request, Blockchain $blockchain)
    {
        $data = [];

        $data = $request->all();

        $blockchain->fill($data)->update();

        if ($blockchain) {
            return redirect()
                ->route('blockchains.index')
                ->with(['message' => 'Editing completed successfully.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('blockchain.create')
                ->with(['message' => 'Error when editing. Try again!', 'code' => 'danger']);
        }
    }

    
    public function destroy(Blockchain $blockchain)
    {

        $blockchain->delete();

        if ($blockchain) {
            return redirect()
                ->route('blockchains.index')
                ->with(['message' => 'Deletion action completed successfully.', 'code' => 'success']);
        } else {
            return redirect()
                ->route('blockchains.index')
                ->with(['message' => 'Error deleting. Try again!', 'code' => 'danger']);
        }
    }

    

    

}
