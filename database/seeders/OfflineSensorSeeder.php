<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Sensor;
use App\Models\SensorLogReport;

class OfflineSensorSeeder extends Seeder
{
    public function run(): void
    {
        $caseNumber = SensorLogReport::count() + 1;

        // ✅ 4 offline sensors
        $offlineSensors = Sensor::where('status', 'online')
            ->inRandomOrder()
            ->take(4)
            ->get();

        // ✅ 3 repairing sensors
        $repairingSensors = Sensor::where('status', 'online')
            ->whereNotIn('id', $offlineSensors->pluck('id'))
            ->inRandomOrder()
            ->take(3)
            ->get();

        $now = Carbon::now();

        foreach ($offlineSensors as $sensor) {
            $timestamp = $now->copy()->subHours(rand(6, 18)); // e.g. 6–18 hours ago
            $caseId = 'CASE-2025-' . str_pad($caseNumber++, 4, '0', STR_PAD_LEFT);

            SensorLogReport::create([
                'sensor_id'         => $sensor->id,
                'substation_id'     => $sensor->switchgear->substation_id ?? null,
                'switchgear_id'     => $sensor->switchgear_id,
                'reported_by'       => 1,
                'title'             => "{$sensor->type} sensor at {$sensor->tag} is OFFLINE",
                'description'       => "System auto-log: sensor reported offline",
                'reported_at'       => $timestamp,
                'status'            => 'open',
                'old_status'        => $sensor->status,
                'new_status'        => 'offline',
                'status_changed_by' => 1,
                'status_changed_at' => $timestamp,
                'case_id'           => $caseId,
            ]);

            $sensor->update([
                'status' => 'offline',
                'status_changed_at' => $timestamp,
            ]);
        }

        foreach ($repairingSensors as $sensor) {
            $timestamp = $now->copy()->subHours(rand(4, 12)); // e.g. 4–12 hours ago
            $caseId = 'CASE-2025-' . str_pad($caseNumber++, 4, '0', STR_PAD_LEFT);

            SensorLogReport::create([
                'sensor_id'         => $sensor->id,
                'substation_id'     => $sensor->switchgear->substation_id ?? null,
                'switchgear_id'     => $sensor->switchgear_id,
                'reported_by'       => 1,
                'title'             => "{$sensor->type} sensor at {$sensor->tag} is under REPAIR",
                'description'       => "System auto-log: sensor reported as repairing",
                'reported_at'       => $timestamp,
                'status'            => 'open',
                'old_status'        => $sensor->status,
                'new_status'        => 'repairing',
                'status_changed_by' => 1,
                'status_changed_at' => $timestamp,
                'case_id'           => $caseId,
            ]);

            $sensor->update([
                'status' => 'repairing',
                'status_changed_at' => $timestamp,
            ]);
        }

        echo "✅ Seeded 4 offline + 3 repairing sensors.\n";
    }
}
