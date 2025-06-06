<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Sensor;
use App\Models\Substation;
use App\Models\SensorLogReport;
use Illuminate\Support\Facades\DB;
use DateTime;

class AnalyticsController extends Controller
{
    public function index(Request $request){}

    public function substation(Request $request)
    {
        $substationId = $request->input('substation_id');
        $year = $request->input('year', now()->year);
        $month = $request->input('month');
        $sensorType = $request->input('sensor_type');
        $statusFilter = $request->input('status');

        $substations = Substation::all();
        $substationNames = $substations->pluck('substation_name')->toArray();
        $sensorTypes = Sensor::distinct()->pluck('type')->filter()->values();

        // Get all sensor log reports for the selected year (and filters)
        $logReports = SensorLogReport::with(['sensor.switchgear.substation'])
            ->whereYear('reported_at', $year)
            ->when($month, fn($q) => $q->whereMonth('reported_at', $month))
            ->when($substationId, fn($q) => $q->where('substation_id', $substationId))
            ->when($sensorType, fn($q) => $q->whereHas('sensor', fn($q2) => $q2->where('type', $sensorType)))
            ->when($statusFilter, fn($q) => $q->where('new_status', $statusFilter))
            ->get();

        // 📌 Latest status per sensor
        // $latestStatuses = $logReports
        //     ->sortByDesc('reported_at')
        //     ->unique('sensor_id');

        // $totalSensors = $latestStatuses->count();
        // $statusCounts = [
        //     'online' => $latestStatuses->where('new_status', 'online')->count(),
        //     'repairing' => $latestStatuses->where('new_status', 'repairing')->count(),
        //     'offline' => $latestStatuses->where('new_status', 'offline')->count(),
        // ];
        // 📌 Latest status per sensor (per substation & filters)
        $latestSensorStatus = Sensor::with(['switchgear.substation'])
            ->when($substationId, fn($q) => $q->whereHas('switchgear', fn($q2) => $q2->where('substation_id', $substationId)))
            ->when($sensorType, fn($q) => $q->where('type', $sensorType))
            ->get();

        // Apply status counts (case-insensitive)
        $onlineCount = $latestSensorStatus->filter(fn($s) => strtolower($s->status) === 'online')->count();
        $repairingCount = $latestSensorStatus->filter(fn($s) => strtolower($s->status) === 'repairing')->count();
        $offlineCount = $latestSensorStatus->filter(fn($s) => strtolower($s->status) === 'offline')->count();

        $totalSensors = $latestSensorStatus->count();

        $statusCounts = [
            'online' => $onlineCount,
            'repairing' => $repairingCount,
            'offline' => $offlineCount,
        ];

        // 📊 Sensor Count by Type Chart
        $sensorTypeChartData = [
            'labels' => $substationNames,
            'datasets' => []
        ];

        foreach ($sensorTypes as $idx => $type) {
            $dataset = [
                'label' => $type,
                'backgroundColor' => 'hsl(' . (($idx * 45) % 360) . ', 70%, 60%)',
                'data' => []
            ];

            foreach ($substations as $sub) {
                $count = $logReports->filter(fn($r) =>
                    optional($r->sensor->switchgear->substation)->id === $sub->id &&
                    ucfirst(trim($r->sensor->type)) === $type
                )->count();
                $dataset['data'][] = $count;
            }

            $sensorTypeChartData['datasets'][] = $dataset;
        }

        // 📈 Uptime % Trend Chart (monthly)
        $trendGrouped = $logReports->groupBy(fn($r) => Carbon::parse($r->reported_at)->format('M'));
        $uptimeTrend = [
            'labels' => $trendGrouped->keys()->toArray(),
            'data' => $trendGrouped->map(function ($group) {
                $total = $group->count();
                $online = $group->where('new_status', 'online')->count();
                return $total > 0 ? round(($online / $total) * 100, 1) : 0;
            })->values()->toArray()
        ];

        // 🚨 Top Problematic Sensors (offline only)
        $topSensors = $logReports->where('new_status', 'offline')
            ->groupBy(fn($r) => optional($r->sensor)->tag . ' (' . optional($r->sensor->switchgear->substation)->substation_name . ')')
            ->map(fn($group) => $group->count())
            ->sortDesc()
            ->take(5);

        $topProblematicSensors = [
            'labels' => $topSensors->keys()->toArray(),
            'datasets' => [[
                'label' => 'Offline Count',
                'data' => $topSensors->values()->toArray(),
                'backgroundColor' => '#e74c3c'
            ]]
        ];

        $statusCounts = [
        'online' => $onlineCount,
        'repairing' => $repairingCount,
        'offline' => $offlineCount,
    ];

    $statusData = collect($statusCounts)->map(function ($count, $status) {
        return [
            'status' => ucfirst($status),
            'count' => $count
        ];
    })->values();


        return view('analytics.substations', compact(
            'logReports',
            'substationNames',
            'statusData',
            'statusCounts',
            'totalSensors',
            'substations',
            'substationId',
            'year',
            'month',
            'uptimeTrend',
            'topProblematicSensors',
            'sensorTypeChartData',
            'sensorTypes',
            'onlineCount',
            'repairingCount',
            'offlineCount',
        ));
    }


}
