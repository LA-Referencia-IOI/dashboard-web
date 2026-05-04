<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServerResourceHistory;
use Carbon\Carbon;

class ServerResourceController extends Controller
{
    /**
     * Display the Server Resources view.
     */
    public function index()
    {
        return view('dashboard.server_resources.index');
    }

    /**
     * API to fetch real-time server resources and historical disk space data.
     */
    public function apiData()
    {
        // 1. Get CPU Load
        // `sys_getloadavg()` returns array: [1_min, 5_min, 15_min]
        $cpuLoad = sys_getloadavg();
        $cpuLoadString = false !== $cpuLoad ? $cpuLoad[0] . ' %' : 'N/A';

        // 2. Get RAM usage
        $ramUsage = 'N/A';
        $ramTotal = 'N/A';
        $ramUsedPercent = 0;
        
        $freeCommandOutput = @shell_exec('free -m');
        if ($freeCommandOutput) {
            $free_arr = explode("\n", trim($freeCommandOutput));
            if(isset($free_arr[1])) {
                $mem = explode(" ", preg_replace('/\s+/', ' ', $free_arr[1]));
                if(isset($mem[1]) && isset($mem[2])) {
                    $totalRam = $mem[1];
                    $usedRam = $mem[2];
                    $ramTotal = round($totalRam / 1024, 2) . ' GB';
                    $ramUsage = round($usedRam / 1024, 2) . ' GB';
                    $ramUsedPercent = round(($usedRam / $totalRam) * 100, 2);
                }
            }
        }

        // 3. Get Disk usage (Real time)
        $path = '/';
        $diskTotalBytes = @disk_total_space($path);
        $diskFreeBytes = @disk_free_space($path);
        
        $diskTotalGB = 0;
        $diskFreeGB = 0;
        $diskUsedGB = 0;
        $diskUsedPercent = 0;

        if ($diskTotalBytes !== false && $diskFreeBytes !== false) {
            $diskTotalGB = $diskTotalBytes / 1024 / 1024 / 1024;
            $diskFreeGB = $diskFreeBytes / 1024 / 1024 / 1024;
            $diskUsedGB = $diskTotalGB - $diskFreeGB;
            $diskUsedPercent = round(($diskUsedGB / $diskTotalGB) * 100, 2);
        }

        // 4. Get Historical Disk Space (Last 30 days)
        $history = ServerResourceHistory::orderBy('created_at', 'asc')
            ->limit(30)
            ->get();
            
        $labels = [];
        $dataUsed = [];
        $dataAvailable = [];

        foreach ($history as $record) {
            $labels[] = $record->created_at->format('d/m H:i');
            $dataUsed[] = $record->disk_used_gb;
            $dataAvailable[] = $record->disk_available_gb;
        }

        return response()->json([
            'cpu' => [
                'load' => $cpuLoadString
            ],
            'ram' => [
                'total' => $ramTotal,
                'used' => $ramUsage,
                'percent' => $ramUsedPercent
            ],
            'disk' => [
                'total_gb' => round($diskTotalGB, 2),
                'used_gb' => round($diskUsedGB, 2),
                'available_gb' => round($diskFreeGB, 2),
                'percent' => $diskUsedPercent
            ],
            'chart' => [
                'labels' => $labels,
                'data_used' => $dataUsed,
                'data_available' => $dataAvailable
            ]
        ]);
    }
}
