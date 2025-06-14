<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Sensor;
use App\Models\Substation;
use App\Models\SensorLogReport;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request) {}

    public function substation(Request $request)
    {
        $substationId = $request->input('substation_id');
        $year = $request->input('year', now()->year);
        $month = $request->input('month');
        $sensorType = $request->input('sensor_type');
        $statusFilter = $request->input('status');

        $substations = Substation::all();
        $substationNames = $substations->pluck('substation_name')->toArray();

        // Raw sensor types for queries
        $sensorTypesRaw = Sensor::distinct()->pluck('type')->filter()->values();

        // Display version for dropdown
        $sensorTypes = $sensorTypesRaw->mapWithKeys(fn($t) => [$t => match ($t) {
            'thermal' => 'Thermal',
            'partial_discharge' => 'Partial Discharge',
            default => ucfirst($t),
        }]);

        $years = SensorLogReport::selectRaw('YEAR(reported_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // Fetch filtered log reports
        $logReports = SensorLogReport::with(['sensor.switchgear.substation'])
            ->whereYear('reported_at', $year)
            ->when($month, fn($q) => $q->whereMonth('reported_at', $month))
            ->when($substationId, fn($q) => $q->where('substation_id', $substationId))
            ->when($sensorType, fn($q) => $q->whereHas('sensor', fn($q2) => $q2->where('type', $sensorType)))
            ->when($statusFilter, fn($q) => $q->where('new_status', $statusFilter))
            ->get();

        // Latest sensor status snapshot
        $latestSensorStatus = Sensor::with(['switchgear.substation'])
            ->when($substationId, fn($q) => $q->whereHas('switchgear', fn($q2) => $q2->where('substation_id', $substationId)))
            ->when($sensorType, fn($q) => $q->where('type', $sensorType))
            ->get();

        $onlineCount = $latestSensorStatus->filter(fn($s) => strtolower($s->status) === 'online')->count();
        $repairingCount = $latestSensorStatus->filter(fn($s) => strtolower($s->status) === 'repairing')->count();
        $offlineCount = $latestSensorStatus->filter(fn($s) => strtolower($s->status) === 'offline')->count();
        $totalSensors = $latestSensorStatus->count();

        $statusCounts = [
            'online' => $onlineCount,
            'repairing' => $repairingCount,
            'offline' => $offlineCount,
        ];

        $statusData = collect($statusCounts)->map(fn($count, $status) => [
            'status' => ucfirst($status),
            'count' => $count
        ])->values();

        // Sensor Type by Substation Chart
        $filteredSubstations = $substationId
            ? $substations->where('id', $substationId)
            : $substations;

        $sensorTypeChartData = [
            'labels' => $filteredSubstations->pluck('substation_name')->toArray(),
            'datasets' => []
        ];

        foreach ($sensorTypesRaw as $idx => $type) {
            $dataset = [
                'label' => $sensorTypes[$type],
                'backgroundColor' => 'hsl(' . (($idx * 45) % 360) . ', 70%, 60%)',
                'data' => []
            ];

            foreach ($filteredSubstations as $sub) {
                $count = Sensor::where('type', $type)
                    ->whereHas('switchgear', fn($q) => $q->where('substation_id', $sub->id))
                    ->count();

                $dataset['data'][] = $count;
            }

            $sensorTypeChartData['datasets'][] = $dataset;
        }


        // Uptime Trend (by month)
        //  Group by full Year-Month (e.g. 2025-Jun)
        //Uptime Trend (by month)
        $allMonths = collect(range(1, 12))
            ->map(fn($m) => Carbon::create()->month($m)->format('M'))
            ->toArray();

        $trendGrouped = $logReports->groupBy(fn($r) => Carbon::parse($r->reported_at)->format('M'));

        $uptimeTrend = [
            'labels' => $allMonths,
            'data' => collect($allMonths)->map(function ($month) use ($trendGrouped) {
                $group = $trendGrouped->get($month, collect());
                $total = $group->count();
                $online = $group->where('new_status', 'online')->count();
                return $total > 0 ? round(($online / $total) * 100, 1) : null;
            })->toArray()
        ];

        // Top Offline Sensors
        $topSensors = $logReports->where('new_status', 'offline')
            ->groupBy(fn($r) => optional($r->sensor)->tag . ' (' . optional($r->sensor->switchgear->substation)->substation_name . ')')
            ->map->count()
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

        // Downtime Trend
        $limit = $request->input('limit');
        $monthlyDowntime = [];
        $targetYear = 2025;

        foreach ($logReports as $report) {
            if (!$report->resolved_at || !$report->reported_at) continue;

            $start = Carbon::parse($report->reported_at);
            $end = Carbon::parse($report->resolved_at);
            if ($end->lessThanOrEqualTo($start)) continue;

            $minutes = max(0, $start->diffInMinutes($end));
            $monthKey = $start->format('Y-M');
            $substation = optional($report->sensor->switchgear->substation)->substation_name;
            if (!$substation) continue;

            $monthlyDowntime[$substation][$monthKey] = ($monthlyDowntime[$substation][$monthKey] ?? 0) + $minutes;
        }

        $allMonths = collect(range(1, 12))
            ->map(fn($m) => "$targetYear-" . Carbon::create()->month($m)->format('M'))
            ->toArray();

        if (is_numeric($limit) && $limit > 0) {
            $monthlyDowntime = collect($monthlyDowntime)
                ->map(fn($m) => array_sum($m))
                ->sortDesc()
                ->take($limit)
                ->keys()
                ->mapWithKeys(fn($k) => [$k => $monthlyDowntime[$k]])
                ->toArray();
        }

        $downtimeTrend = [];
        foreach ($monthlyDowntime as $substation => $months) {
            $data = [];
            foreach ($allMonths as $monthKey) {
                $data[] = round(($months[$monthKey] ?? 0) / 60, 1);
            }

            $downtimeTrend[] = [
                'label' => $substation,
                'data' => $data,
                'fill' => true,
                'borderColor' => 'hsl(' . rand(0, 360) . ', 70%, 50%)',
                'backgroundColor' => 'hsla(' . rand(0, 360) . ', 70%, 50%, 0.2)',
                'tension' => 0.4,
                'borderWidth' => 2,
            ];
        }

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
            'downtimeTrend',
            'allMonths',
            'years'
        ));
    }

}
