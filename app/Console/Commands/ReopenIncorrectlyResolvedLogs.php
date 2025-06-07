<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SensorLogReport;

class ReopenIncorrectlyResolvedLogs extends Command
{
    protected $signature = 'fix:repairing-logs';
    protected $description = 'Reopens log reports that were marked resolved but sensor is still not online';

    public function handle()
    {
        $reopened = 0;

        $logs = SensorLogReport::where('status', 'resolved')
            ->whereHas('sensor', function ($query) {
                $query->where('status', '!=', 'online');
            })
            ->with('sensor')
            ->get();

        foreach ($logs as $log) {
            $sensor = $log->sensor;

            if ($sensor && $sensor->status !== 'online') {
                $log->update([
                    'status' => 'open',
                    'resolved_at' => null,
                    'downtime_duration' => null,
                ]);
                $reopened++;
            }
        }

        $this->info("🔄 Reopened $reopened incorrectly resolved log(s).");
    }
}
