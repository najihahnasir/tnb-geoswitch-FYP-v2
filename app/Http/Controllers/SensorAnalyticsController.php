<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor;
use App\Models\Switchgear;
use App\Models\Substation;
use Illuminate\Support\Facades\DB;

class SensorAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $sensorType = $request->sensor_type;
        $substationId = $request->substation_id;
        $year = $request->year;
        $month = $request->month;

        $query = Sensor::with(['switchgear.substation']);
        $substationNames = Substation::pluck('substation_name');

        if ($sensorType) {
            $query->where('type', $sensorType);
        }

        if ($substationId) {
            $query->whereHas('switchgear', function ($q) use ($substationId) {
                $q->where('substation_id', $substationId);
            });
        }

        // 🔹 Summary Cards Data
        $filteredSensors = $query->get();
        $onlineCount = $filteredSensors->filter(fn($s) => strtolower($s->status) === 'online')->count();
        $repairingCount = $filteredSensors->filter(fn($s) => strtolower($s->status) === 'repairing')->count();
        $offlineCount = $filteredSensors->filter(fn($s) => strtolower($s->status) === 'offline')->count();


        $totalSensors = $filteredSensors->count();
        $activeSubstations = $filteredSensors->pluck('switchgear.substation_id')->unique()->count();

        // 🔹 History Data for Charts - REPLACE THIS SECTION
        $sensorHistories = DB::table('sensor_log_reports')
            ->join('sensors', 'sensor_log_reports.sensor_id', '=', 'sensors.id')
            ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
            ->join('substations', 'switchgears.substation_id', '=', 'substations.id')
            ->selectRaw('
                sensors.type,
                sensor_log_reports.new_status as status,
                substations.substation_name,
                COUNT(*) as failures,
                YEAR(sensor_log_reports.reported_at) as year,
                MONTH(sensor_log_reports.reported_at) as month
            ')
            ->when($sensorType, fn($q) => $q->where('sensors.type', $sensorType))
            ->when($substationId, fn($q) => $q->where('switchgears.substation_id', $substationId))
            ->when($year, fn($q) => $q->whereYear('sensor_log_reports.reported_at', $year))
            ->when($month, fn($q) => $q->whereMonth('sensor_log_reports.reported_at', $month))
            ->groupBy('sensors.type', 'sensor_log_reports.new_status', 'substations.substation_name', 'year', 'month')
            ->get();

        // 🔹 Offline Sensors Count - Add this after the above query
        $offlineSensors = DB::table('sensor_log_reports')
            ->join('sensors', 'sensor_log_reports.sensor_id', '=', 'sensors.id')
            ->where('sensor_log_reports.new_status', 'offline')
            ->when($sensorType, fn($q) => $q->where('sensors.type', $sensorType))
            ->when($substationId, fn($q) => $q->whereHas('switchgear', function($q) use ($substationId) {
                $q->where('substation_id', $substationId);
            }))
            ->count();

        // Rest of your controller code remains the same...
        $sensorTypes = Sensor::select('type')->distinct()->pluck('type');
        $substations = Substation::all();

        // 🔹 Donut Chart Data (Status Distribution)
        $statusData = $filteredSensors->groupBy('status')->map(function ($group, $status) {
            return [
                'status' => ucfirst($status),
                'count' => $group->count(),
            ];
        })->values();

        $statusTimelineData = DB::table('sensor_log_reports')
            ->join('sensors', 'sensor_log_reports.sensor_id', '=', 'sensors.id')
            ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
            ->join('substations', 'switchgears.substation_id', '=', 'substations.id')
            ->select(
                'sensors.tag',
                'sensor_log_reports.old_status',
                'sensor_log_reports.new_status',
                'sensor_log_reports.reported_at as created_at',
                'sensor_log_reports.resolved_at',
                'substations.substation_name',
                'sensor_log_reports.title',
                'sensor_log_reports.description'
            )
            ->when($sensorType, fn($q) => $q->where('sensors.type', $sensorType))
            ->when($substationId, fn($q) => $q->where('substations.id', $substationId))
            ->when($year, fn($q) => $q->whereYear('sensor_log_reports.reported_at', $year))
            ->when($month, fn($q) => $q->whereMonth('sensor_log_reports.reported_at', $month))
            ->orderBy('sensor_log_reports.reported_at')
            ->get();

        return view('analytics.sensor', compact(
            'sensorHistories',
            'sensorTypes',
            'substations',
            'substationId',
            'year',
            'month',
            'totalSensors',
            'offlineSensors',
            'activeSubstations',
            'substationNames',
            'statusData',
            'statusTimelineData',
            'onlineCount',
            'repairingCount',
            'offlineCount',

        ));
    }
}