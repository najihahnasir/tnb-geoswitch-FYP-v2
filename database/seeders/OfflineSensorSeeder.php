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

        // Get 7 random sensors that are NOT offline yet
        $extraSensors = Sensor::where('status', '!=', 'offline')
            ->inRandomOrder()
            ->take(7)
            ->get();

        foreach ($extraSensors as $sensor) {
            $timestamp = Carbon::now()->subMinutes(rand(30, 120));
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

        echo "✅ Seeded 7 additional offline sensor logs.\n";
    }
}
