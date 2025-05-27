<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sensor;

class SyncSensorStatusFromLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:sensor-status';
    protected $description = 'Sync each sensor status based on latest SensorLogReport';

    public function handle()
    {
        $this->info('🔄 Syncing sensor statuses from latest log reports...');

        $sensors = Sensor::with(['logReports' => function($q) {
            $q->latest('status_changed_at');
        }])->get();

        $updated = 0;
        $skipped = 0;

        foreach ($sensors as $sensor) {
            $latestReport = $sensor->logReports->first(); // sorted by status_changed_at DESC

            if (!$latestReport || !$latestReport->new_status || !$latestReport->status_changed_at) {
                $skipped++;
                continue;
            }

            $sensor->status = $latestReport->new_status;
            $sensor->status_changed_at = $latestReport->status_changed_at;
            $sensor->save();
            $updated++;
        }

        $this->info("✅ Sync complete. $updated sensors updated. $skipped had no reports.");
    }
}
