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

        $sensors = Sensor::inRandomOrder()->take(30)->get(); // ✅ Spread across substations
        $caseNumber = 1;
        $total = 0;

        foreach ($sensors as $sensor) {
            $statusChain = ['offline', 'repairing', 'online'];
            $baseTime = Carbon::now()->subMonths(rand(2, 11))->startOfMonth()->addDays(rand(1, 5));
            $previousStatus = 'online';

            foreach ($statusChain as $newStatus) {
                $gapHours = rand(12, 72);
                $baseTime->addHours($gapHours);

                $durationHours = rand(8, 48);
                $resolvedAt = (clone $baseTime)->addHours($durationHours);

                $caseId = 'CASE-2025-' . str_pad($caseNumber++, 4, '0', STR_PAD_LEFT);

                SensorLogReport::create([
                    'sensor_id'         => $sensor->id,
                    'substation_id'     => $sensor->switchgear->substation_id,
                    'switchgear_id'     => $sensor->switchgear_id,
                    'reported_by'       => 1,
                    'title'             => "{$sensor->type} sensor at {$sensor->tag} changed to {$newStatus}",
                    'description'       => "System auto-log: status changed to {$newStatus}",
                    'reported_at'       => $baseTime,
                    'resolved_at'       => $resolvedAt,
                    'status'            => ($newStatus === 'online') ? 'resolved' : 'open',
                    'old_status'        => $previousStatus,
                    'new_status'        => $newStatus,
                    'status_changed_by' => 1,
                    'status_changed_at' => $baseTime,
                    'created_at'        => $baseTime,
                    'updated_at'        => $baseTime,
                    'downtime_duration' => "{$durationHours} hours",
                ]);

                // Update sensor to latest status after the last log
                if ($newStatus === 'online') {
                    $sensor->update([
                        'status' => 'online',
                        'status_changed_at' => $resolvedAt,
                    ]);
                }

                $previousStatus = $newStatus;
                $baseTime = $resolvedAt;
                $total++;
            }
        }

        echo "✅ Seeded {$total} logs across multiple substations with realistic status transitions.\n";
    }
}
