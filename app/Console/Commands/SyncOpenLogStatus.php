<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SensorLogReport;

class SyncOpenLogStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-open-log-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $openLogs = SensorLogReport::where('status', 'open')->get();
        foreach ($openLogs as $log) {
            $sensor = $log->sensor;

            // If sensor status no longer matches the "new_status" in the log, auto-resolve it
            if ($sensor && $sensor->status === 'online') {
                $log->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                    'downtime_duration' => $log->reported_at->diff(now())->format('%dd %hh %im')
                ]);
            }
        }

    }
}
