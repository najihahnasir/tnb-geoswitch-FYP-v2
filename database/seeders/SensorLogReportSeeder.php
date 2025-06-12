<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Sensor;
use App\Models\SensorLogReport;

class SensorLogReportSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SensorLogReport::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $sensors = Sensor::inRandomOrder()->take(30)->get();
        $caseNumber = 1;

        foreach ($sensors as $sensor) {
            $statusChain = ['offline', 'repairing', 'online'];
            $baseTime = Carbon::create(2025, rand(1, 6), rand(1, 5), rand(6, 20), rand(0, 59));
            $previousStatus = 'online';

            foreach ($statusChain as $newStatus) {
                $gapHours = rand(24, 72);
                $reportedAt = $baseTime->copy()->addHours($gapHours);

                $downtimeMinutes = rand(120, 720);
                $resolvedAt = $reportedAt->copy()->addMinutes($downtimeMinutes);

                SensorLogReport::create([
                    'case_id'           => 'CASE-2025-' . str_pad($caseNumber++, 4, '0', STR_PAD_LEFT),
                    'sensor_id'         => $sensor->id,
                    'substation_id'     => $sensor->switchgear->substation_id,
                    'switchgear_id'     => $sensor->switchgear_id,
                    'reported_by'       => 1,
                    'title'             => "{$sensor->type} sensor at {$sensor->tag} changed to {$newStatus}",
                    'description'       => "System auto-log: status changed to {$newStatus}",
                    'reported_at'       => $reportedAt,
                    'resolved_at'       => $resolvedAt,
                    'status'            => ($newStatus === 'online') ? 'resolved' : 'open',
                    'old_status'        => $previousStatus,
                    'new_status'        => $newStatus,
                    'status_changed_by' => 1,
                    'status_changed_at' => $reportedAt,
                    'created_at'        => $reportedAt,
                    'updated_at'        => $reportedAt,
                    'downtime_duration' => "{$downtimeMinutes} minutes",
                ]);

                if ($newStatus === 'online') {
                    $sensor->update([
                        'status' => 'online',
                        'status_changed_at' => $resolvedAt,
                    ]);
                }

                $baseTime = $resolvedAt->copy(); // ✅ move forward for next loop
                $previousStatus = $newStatus;
            }
        }

        echo "✅ Logs seeded cleanly with valid time ranges.\n";
    }

}
