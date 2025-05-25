<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Sensor;
use App\Models\SensorStatusHistory;
use App\Models\Substation;
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

        $filteredSensors = Sensor::with('switchgear.substation')
            ->when($substationId, fn($q) => $q->whereHas('switchgear', fn($q2) => $q2->where('substation_id', $substationId)))
            ->when($sensorType, fn($q) => $q->where('type', $sensorType))
            ->when($statusFilter, fn($q) => $q->where('status', $statusFilter))
            ->get();

        $totalSensors = $filteredSensors->count();
        $statusCounts = [
            'online' => $filteredSensors->where('status', 'online')->count(),
            'repairing' => $filteredSensors->where('status', 'repairing')->count(),
            'offline' => $filteredSensors->where('status', 'offline')->count(),
        ];

        $statusData = collect($statusCounts)->map(function ($count, $status) {
            return ['status' => ucfirst($status), 'count' => $count];
        })->values();

        $sensorHistories = SensorStatusHistory::with(['sensor.switchgear.substation'])
            ->whereYear('created_at', $year)
            ->when($month, fn($q) => $q->whereMonth('created_at', $month))
            ->when($substationId, fn($q) => $q->whereHas('sensor.switchgear', fn($q2) => $q2->where('substation_id', $substationId)))
            ->when($sensorType, fn($q) => $q->whereHas('sensor', fn($q2) => $q2->where('type', $sensorType)))
            ->when($statusFilter, fn($q) => $q->where('new_status', $statusFilter))
            ->get();

        $sensorTypes = Sensor::distinct()->pluck('type')->filter()->values();

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
                $count = $sensorHistories->filter(fn($h) =>
                    optional($h->sensor->switchgear->substation)->id === $sub->id &&
                    ucfirst(trim($h->sensor->type)) === $type
                )->count();
                $dataset['data'][] = $count;
            }

            $sensorTypeChartData['datasets'][] = $dataset;
        }

        $trendGrouped = $sensorHistories->groupBy(fn($h) => Carbon::parse($h->created_at)->format('M'));
        $uptimeTrend = [
            'labels' => $trendGrouped->keys()->toArray(),
            'data' => $trendGrouped->map(function ($group) {
                $total = $group->count();
                $online = $group->where('new_status', 'online')->count();
                return $total > 0 ? round(($online / $total) * 100, 1) : 0;
            })->values()->toArray()
        ];

        $topSensors = $sensorHistories->where('new_status', 'offline')
            ->groupBy(fn($h) => optional($h->sensor)->tag . ' (' . optional($h->sensor->switchgear->substation)->substation_name . ')')
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

        return view('analytics.substations', compact(
            'sensorHistories',
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
            'sensorTypes'
        ));
    }
}
