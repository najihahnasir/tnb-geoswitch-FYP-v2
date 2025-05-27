<?php

namespace App\Services;

use App\Models\Sensor;
use App\Models\SensorLogReport;
use Illuminate\Support\Facades\Auth;

class SensorService
{
    public static function updateSensorStatus(Sensor $sensor, string $newStatus, ?SensorLogReport $report = null)
    {
        $oldStatus = $sensor->status;

        if ($oldStatus === $newStatus) {
            return false; // No update needed
        }

        $sensor->status = $newStatus;
        $sensor->status_changed_at = now();
        $sensor->save();

        if ($report) {
            $report->old_status = $oldStatus;
            $report->new_status = $newStatus;
            $report->status_changed_by = Auth::id();
            $report->status_changed_at = now();
            $report->save();
        }

        return true;
    }
}
