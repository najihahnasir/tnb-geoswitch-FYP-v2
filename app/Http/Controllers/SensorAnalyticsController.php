<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor;
use App\Models\Switchgear;
use App\Models\Substation;
use App\Models\SensorLogReport;
use Carbon\Carbon;
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

        $latestStatusSummary = $filteredSensors
        ->groupBy(function ($sensor) {
            return optional($sensor->switchgear->substation)->substation_name;
        })
        ->map(function ($group) {
            return collect($group)
                ->groupBy('type')
                ->map(function ($typedGroup) {
                return $typedGroup
                    ->groupBy(fn($s) => ucfirst(strtolower($s->status)))
                    ->map->count();
            });
        });

        $totalSensorsBySub = Sensor::with('switchgear.substation')
        ->get()
        ->groupBy(fn($s) => optional($s->switchgear->substation)->substation_name)
        ->map(fn($group) => $group->count());

        $failureMeta = SensorLogReport::join('sensors', 'sensor_log_reports.sensor_id', '=', 'sensors.id')
        ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
        ->join('substations', 'switchgears.substation_id', '=', 'substations.id')
        ->selectRaw('substations.substation_name, MAX(sensor_log_reports.reported_at) as last_failure')
        ->groupBy('substations.substation_name')
        ->pluck('last_failure', 'substation_name');

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
            ->groupBy(
                'sensors.type',
                'sensor_log_reports.new_status',
                'substations.substation_name',
                DB::raw('YEAR(sensor_log_reports.reported_at)'),
                DB::raw('MONTH(sensor_log_reports.reported_at)')
            )
            ->orderByRaw('YEAR(sensor_log_reports.reported_at), MONTH(sensor_log_reports.reported_at)')
            ->get();
            $range = $request->input('range');

        if ($range === 'rolling') {
            $startDate = now()->subMonths(11)->startOfMonth();
            $endDate = now()->endOfMonth();
            $sensorHistories = $sensorHistories->filter(function ($item) use ($startDate, $endDate) {
                $date = \Carbon\Carbon::createFromDate($item->year, $item->month, 1);
                return $date->between($startDate, $endDate);
            });
        }

        // 🔹 Offline Sensors Count - Add this after the above query
        $offlineSensors = DB::table('sensor_log_reports')
        ->join('sensors', 'sensor_log_reports.sensor_id', '=', 'sensors.id')
        ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
        ->where('sensor_log_reports.new_status', 'offline')
        ->when($sensorType, fn($q) => $q->where('sensors.type', $sensorType))
        ->when($substationId, fn($q) => $q->where('switchgears.substation_id', $substationId)) // ✅ fixed here
        ->count();

        $substations = Substation::with('switchgears.sensors')->get();

        $sensorSummary = $substations->map(function ($sub) {
        $totals = [];

        foreach (['Thermal', 'Partial Discharge'] as $type) {
            $count = $sub->switchgears->flatMap->sensors
                ->filter(fn($s) => $s->type === $type)
                ->count();

            $totals[$type] = $count;
        }

        return [
            'substation' => $sub->substation_name,
            'data' => $totals
        ];
    });



        // $sensorTypes = Sensor::select('type')->distinct()->pluck('type');
        $sensorTypes = Sensor::select('type')->distinct()->pluck('type')->map(function ($type) {
            return match ($type) {
                'thermal' => 'Thermal',
                'partial_discharge' => 'Partial Discharge',
                default => ucfirst($type),
            };
        });


        // 🔹 Donut Chart Data (Status Distribution)
        $statusData = $filteredSensors->groupBy('status')->map(function ($group, $status) {
            return [
                'status' => ucfirst($status),
                'count' => $group->count(),
            ];
        })->values();

        $status = strtolower(request('status'));

        $statusTimelineData = DB::table('sensor_log_reports')
            ->join('sensors', 'sensor_log_reports.sensor_id', '=', 'sensors.id')
            ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
            ->join('substations', 'switchgears.substation_id', '=', 'substations.id')
            ->select(
                'sensors.tag',
                'sensor_log_reports.old_status',
                'sensor_log_reports.new_status',
                'sensor_log_reports.reported_at as created_at',
                DB::raw('IFNULL(sensor_log_reports.resolved_at, NOW()) as resolved_at'),
                'substations.substation_name'
            )
            ->when($status, fn($q) => $q->whereRaw('LOWER(sensor_log_reports.new_status) = ?', [$status]))
            ->whereNotNull('sensor_log_reports.reported_at')
            ->whereNotNull('sensor_log_reports.new_status')
            ->orderBy('sensor_log_reports.reported_at')
            ->get();

        // ✅ STEP 1: Filter out rows with invalid or reversed timestamps
        $statusTimelineData = $statusTimelineData->filter(function ($row) {
            return $row->created_at && $row->resolved_at && $row->resolved_at > $row->created_at;
        });

        // ✅ STEP 2: Apply fallback to ensure resolved_at > created_at
        $statusTimelineData = $statusTimelineData->map(function ($row) {
            $start = Carbon::parse($row->created_at);
            $end = Carbon::parse($row->resolved_at);

            if ($end->lessThanOrEqualTo($start)) {
                $row->resolved_at = $start->copy()->addHour();
            }

            return $row;
        });

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
            'latestStatusSummary',
            'totalSensorsBySub',
            'failureMeta',
            'sensorSummary',
            'substations',
            // 'sensorFailureTable',
            // 'groupedFailureTable'   
        ));
    }
}