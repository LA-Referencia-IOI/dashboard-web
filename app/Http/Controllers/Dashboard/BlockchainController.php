<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\BlockchainRequest;
use App\Models\User;
use App\Models\Blockchain;

class BlockchainController extends Controller
{
    private $viewPath = 'dashboard.blockchain.';

    public function index(Request $request)
    {
        
        $blockchains = Blockchain::orderBy('type')->paginate(config('pagination.default'));
        return view($this->viewPath . 'index', compact('blockchains'));
    }

    public function showLogs()
    {

        return view($this->viewPath .'monitor');
    }

    public function fetchLogs()
    {
        $logPath = "/var/logs/node1.log";

        // Verifica se o arquivo existe
        if (!file_exists($logPath)) {
            die("Log file does not exist or path is incorrect: $logPath");
        }

        // Verifica as permissões de arquivo
        if (!is_readable($logPath)) {
            die("Log file is not readable: $logPath");
        }

        // Tenta ler o conteúdo do arquivo
        $logs = shell_exec("tail -n 100 $logPath");
        if ($logs === null) {
            die("Error executing tail command for file: $logPath");
        }

        // Se tudo estiver correto, continua processando os logs
        $logsArray = explode("\n", trim($logs));
        if (empty($logsArray)) {
            return 'No logs found or empty log file.';
        }

        $logHtml = '';
        foreach ($logsArray as $line) {
            $logHtml .= '<div class="log-line">' . htmlspecialchars($line) . '</div>';
        }

        return $logHtml;

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
