<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Sensor;
use App\Models\SensorLogReport;

class SyncSensorLogs extends Command
{
    protected $signature = 'logs:sync';
    protected $description = 'Sync sensor status with sensor log reports (offline + repairing)';

    public function handle(): void
    {
        $this->info('🔄 Syncing sensor logs with real-time sensor status...');

        $reopenedOffline = 0;
        $resolvedOffline = 0;
        $createdOffline = 0;

        $reopenedRepairing = 0;
        $resolvedRepairing = 0;
        $createdRepairing = 0;

        // ---------------------------------------
        // 🔁 OFFLINE LOGIC
        // ---------------------------------------

        // ✅ Reopen offline logs that were wrongly resolved
        SensorLogReport::where('status', 'resolved')
            ->where('new_status', 'offline')
            ->get()
            ->filter(fn($log) => $log->sensor && $log->sensor->status === 'offline')
            ->each(function ($log) use (&$reopenedOffline) {
                $log->update([
                    'status' => 'open',
                    'resolved_at' => null,
                    'downtime_duration' => null,
                    'remarks' => 'Reopened: Sensor still offline',
                ]);
                $reopenedOffline++;
            });

        // ✅ Resolve open offline logs where sensor has recovered
        SensorLogReport::where('status', 'open')
            ->where('new_status', 'offline')
            ->get()
            ->filter(fn($log) => $log->sensor && $log->sensor->status !== 'offline')
            ->each(function ($log) use (&$resolvedOffline) {
                $resolvedAt = Carbon::now();
                $log->update([
                    'status' => 'resolved',
                    'resolved_at' => $resolvedAt,
                    'downtime_duration' => $log->reported_at
                        ? $log->reported_at->diffInMinutes($resolvedAt) . ' minutes'
                        : 'auto-closed',
                    'remarks' => 'Auto-resolved due to sensor recovery',
                    'resolved_by' => 1,
                ]);
                $resolvedOffline++;
            });

        // ✅ Create missing offline logs
        $caseNumber = SensorLogReport::max(DB::raw('CAST(SUBSTRING(case_id, 10) AS UNSIGNED)')) + 1;

        Sensor::where('status', 'offline')->get()
            ->filter(function ($sensor) {
                return !$sensor->logReports()
                    ->where('status', 'open')
                    ->where('new_status', 'offline')
                    ->exists();
            })
            ->each(function ($sensor) use (&$caseNumber, &$createdOffline) {
                SensorLogReport::create([
                    'case_id'           => 'CASE-2025-' . str_pad($caseNumber++, 4, '0', STR_PAD_LEFT),
                    'sensor_id'         => $sensor->id,
                    'substation_id'     => $sensor->switchgear->substation_id,
                    'switchgear_id'     => $sensor->switchgear_id,
                    'reported_by'       => 1,
                    'title'             => "{$sensor->type} sensor at {$sensor->tag} went offline",
                    'description'       => "Auto-log created for offline sensor with no existing open report.",
                    'status'            => 'open',
                    'old_status'        => 'online',
                    'new_status'        => 'offline',
                    'reported_at'       => $sensor->status_changed_at,
                    'status_changed_at' => $sensor->status_changed_at,
                    'created_at'        => $sensor->status_changed_at,
                    'updated_at'        => $sensor->status_changed_at,
                ]);
                $createdOffline++;
            });

        // ---------------------------------------
        // 🛠 REPAIRING LOGIC
        // ---------------------------------------

        // ✅ Reopen repairing logs that were wrongly resolved
        SensorLogReport::where('status', 'resolved')
            ->where('new_status', 'repairing')
            ->get()
            ->filter(fn($log) => $log->sensor && $log->sensor->status === 'repairing')
            ->each(function ($log) use (&$reopenedRepairing) {
                $log->update([
                    'status' => 'open',
                    'resolved_at' => null,
                    'downtime_duration' => null,
                    'remarks' => 'Reopened: Sensor still under repair',
                ]);
                $reopenedRepairing++;
            });

        // ✅ Resolve repairing logs where sensor has recovered
        SensorLogReport::where('status', 'open')
            ->where('new_status', 'repairing')
            ->get()
            ->filter(fn($log) => $log->sensor && $log->sensor->status !== 'repairing')
            ->each(function ($log) use (&$resolvedRepairing) {
                $resolvedAt = Carbon::now();
                $log->update([
                    'status' => 'resolved',
                    'resolved_at' => $resolvedAt,
                    'downtime_duration' => $log->reported_at
                        ? $log->reported_at->diffInMinutes($resolvedAt) . ' minutes'
                        : 'auto-closed',
                    'remarks' => 'Auto-resolved due to sensor recovery',
                    'resolved_by' => 1,
                ]);
                $resolvedRepairing++;
            });

        // ✅ Create missing repairing logs
        Sensor::where('status', 'repairing')->get()
            ->filter(function ($sensor) {
                return !$sensor->logReports()
                    ->where('status', 'open')
                    ->where('new_status', 'repairing')
                    ->exists();
            })
            ->each(function ($sensor) use (&$caseNumber, &$createdRepairing) {
                SensorLogReport::create([
                    'case_id'           => 'CASE-2025-' . str_pad($caseNumber++, 4, '0', STR_PAD_LEFT),
                    'sensor_id'         => $sensor->id,
                    'substation_id'     => $sensor->switchgear->substation_id,
                    'switchgear_id'     => $sensor->switchgear_id,
                    'reported_by'       => 1,
                    'title'             => "{$sensor->type} sensor at {$sensor->tag} entered repair mode",
                    'description'       => "Auto-log created for repairing sensor with no existing open report.",
                    'status'            => 'open',
                    'old_status'        => 'online',
                    'new_status'        => 'repairing',
                    'reported_at'       => $sensor->status_changed_at,
                    'status_changed_at' => $sensor->status_changed_at,
                    'created_at'        => $sensor->status_changed_at,
                    'updated_at'        => $sensor->status_changed_at,
                ]);
                $createdRepairing++;
            });

        // ---------------------------------------
        // ✅ Summary Output
        // ---------------------------------------

        $this->line('');
        $this->info("✅ Reopened offline logs: {$reopenedOffline}");
        $this->info("✅ Resolved offline logs: {$resolvedOffline}");
        $this->info("✅ Created offline logs: {$createdOffline}");

        $this->info("✅ Reopened repairing logs: {$reopenedRepairing}");
        $this->info("✅ Resolved repairing logs: {$resolvedRepairing}");
        $this->info("✅ Created repairing logs: {$createdRepairing}");
        $this->info('✅ Sensor log sync complete.');
    }
}
