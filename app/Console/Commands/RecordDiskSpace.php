<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ServerResourceHistory;

class RecordDiskSpace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:record-disk-space';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Records the current disk space to the history table.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = '/';
        $totalBytes = disk_total_space($path);
        $freeBytes = disk_free_space($path);
        
        $totalGB = $totalBytes / 1024 / 1024 / 1024;
        $freeGB = $freeBytes / 1024 / 1024 / 1024;
        $usedGB = $totalGB - $freeGB;

        ServerResourceHistory::create([
            'disk_total_gb' => round($totalGB, 2),
            'disk_used_gb' => round($usedGB, 2),
            'disk_available_gb' => round($freeGB, 2),
        ]);

        $this->info('Disk space recorded successfully.');
        return 0;
    }
}
