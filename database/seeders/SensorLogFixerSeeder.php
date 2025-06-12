<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sensor;
use App\Models\SensorLogReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SensorLogFixerSeeder extends Seeder
{
    public function run(): void
    {
        $fixedOpen = 0;
        $fixedMissing = 0;

        // 1. Fix: Sensors that are OFFLINE but have no open logs
        $caseNumber = SensorLogReport::max(DB::raw('CAST(SUBSTRING(case_id, 10) AS UNSIGNED)')) + 1;

        $offlineSensors = Sensor::where('status', 'offline')->get();

        foreach ($offlineSensors as $sensor) {
            $hasOpenLog = SensorLogReport::where('sensor_id', $sensor->id)
                ->where('status', 'open')
                ->exists();

            if (!$hasOpenLog) {
                SensorLogReport::create([
                    'case_id'           => 'CASE-2025-' . str_pad($caseNumber++, 4, '0', STR_PAD_LEFT),
                    'sensor_id'         => $sensor->id,
                    'substation_id'     => $sensor->switchgear->substation_id,
                    'switchgear_id'     => $sensor->switchgear_id,
                    'reported_by'       => 1,
                    'title'             => "{$sensor->type} sensor at {$sensor->tag} is offline",
                    'description'       => "Log auto-generated because sensor is offline without open report.",
                    'reported_at'       => $sensor->status_changed_at,
                    'status'            => 'open',
                    'old_status'        => 'online',
                    'new_status'        => 'offline',
                    'status_changed_by' => 1,
                    'status_changed_at' => $sensor->status_changed_at,
                    'created_at'        => $sensor->status_changed_at,
                    'updated_at'        => $sensor->status_changed_at,
                    'downtime_duration' => null,
                ]);

                $fixedMissing++;
            }
        }

        // 2. Fix: Open logs where sensor is already ONLINE
        $openLogs = SensorLogReport::where('status', 'open')->get();

        foreach ($openLogs as $log) {
            $sensor = $log->sensor;

            if ($sensor && $sensor->status === 'online') {
                $resolvedTime = now();

                $log->update([
                    'status'            => 'resolved',
                    'resolved_at'       => $resolvedTime,
                    'downtime_duration' => $log->reported_at
                        ? $log->reported_at->diffInMinutes($resolvedTime) . ' minutes'
                        : 'auto-close',
                ]);

                $fixedOpen++;
            }
        }

        echo "✅ Fixed {$fixedMissing} missing offline logs.\n";
        echo "✅ Resolved {$fixedOpen} stale open logs.\n";
    }
}
