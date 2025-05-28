<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SensorLogReport;
use App\Models\Sensor;
use Illuminate\Support\Carbon;

class SensorLogReportSeeder extends Seeder
{
    public function run(): void
    {
        // Reset log table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        SensorLogReport::query()->delete();
        DB::statement('ALTER TABLE sensor_log_reports AUTO_INCREMENT = 1;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Take only 10 sensors for test seeding
        $sensors = Sensor::take(10)->get();
        $caseNumber = 1;
        $startDate = Carbon::create(2025, 5, 1, 8, 0);

        foreach ($sensors as $sensor) {
            // 1st log: goes offline
            $reportedAt1 = $startDate->copy()->addHours(rand(0, 5));
            $caseId1 = 'CASE-2025-' . str_pad($caseNumber++, 4, '0', STR_PAD_LEFT);

            SensorLogReport::create([
                'sensor_id'         => $sensor->id,
                'substation_id'     => $sensor->switchgear->substation_id ?? null,
                'switchgear_id'     => $sensor->switchgear_id,
                'reported_by'       => 1,
                'title'             => "{$sensor->type} sensor at {$sensor->tag} is OFFLINE",
                'description'       => "System auto-log: sensor reported offline",
                'reported_at'       => $reportedAt1,
                'status'            => 'open',
                'old_status'        => 'online',
                'new_status'        => 'offline',
                'status_changed_by' => 1,
                'status_changed_at' => $reportedAt1,
                'case_id'           => $caseId1,
            ]);

            // 2nd log: status changed to repairing or online
            $newStatus = collect(['repairing', 'online'])->random();
            $reportedAt2 = $reportedAt1->copy()->addMinutes(rand(30, 120));
            $caseId2 = 'CASE-2025-' . str_pad($caseNumber++, 4, '0', STR_PAD_LEFT);

            SensorLogReport::create([
                'sensor_id'         => $sensor->id,
                'substation_id'     => $sensor->switchgear->substation_id ?? null,
                'switchgear_id'     => $sensor->switchgear_id,
                'reported_by'       => 1,
                'title'             => "{$sensor->type} sensor at {$sensor->tag} updated to " . strtoupper($newStatus),
                'description'       => "System auto-log: status changed to {$newStatus}",
                'reported_at'       => $reportedAt2,
                'status'            => $newStatus === 'online' ? 'resolved' : 'open',
                'old_status'        => 'offline',
                'new_status'        => $newStatus,
                'status_changed_by' => 1,
                'status_changed_at' => $reportedAt2,
                'resolved_at'       => $newStatus === 'online' ? $reportedAt2 : null,
                'downtime_duration' => $reportedAt1->diff($reportedAt2)->format('%dd %hh %im'),
                'case_id'           => $caseId2,
            ]);

            // ✅ Sync latest status to sensors table
            $sensor->update([
                'status' => $newStatus,
                'status_changed_at' => $reportedAt2
            ]);
        }

        echo "Seeded 10 sensors and synced status to sensors table.\n";
    }
}
