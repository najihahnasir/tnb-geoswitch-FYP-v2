<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SensorLogReport;

class FixOpenLogsWithResolvedAt extends Command
{
    protected $signature = 'fix:open-logs-resolved';
    protected $description = 'Resolve all open logs that already have a resolved_at timestamp';

    public function handle()
    {
        $affected = 0;

        $openLogs = SensorLogReport::where('status', 'open')
            ->whereNotNull('resolved_at')
            ->get();

        foreach ($openLogs as $log) {
            $diff = $log->reported_at->diff($log->resolved_at);
            $duration = "{$diff->d}d {$diff->h}h {$diff->i}m";

            $log->update([
                'status' => 'resolved',
                'downtime_duration' => $duration,
            ]);

            $affected++;
        }

        $this->info("✅ Fixed $affected inconsistent open log report(s).");
    }
}
