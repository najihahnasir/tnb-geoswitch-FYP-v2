<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sensor;
use App\Models\SensorStatusHistory;
use App\Models\Substation;
use Illuminate\Support\Facades\DB;
use DateTime;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', now()->year);
        $substationId = $request->input('substation');
        $sensorType = $request->input('sensor_type');

        // Query filters
        $sensorQuery = Sensor::with('switchgear.substation');
        if ($substationId) {
            $sensorQuery->whereHas('switchgear', fn($q) => $q->where('substation_id', $substationId));
        }
        if ($sensorType) {
            $sensorQuery->where('type', $sensorType);
        }

        // === Sensor Quantity by Type (Pie) ===
        $quantity = Sensor::select('type', DB::raw('count(*) as total'))
            ->when($substationId, fn($q) => $q->whereHas('switchgear', fn($q2) => $q2->where('substation_id', $substationId)))
            ->groupBy('type')
            ->get();

        $quantityData = [
            'labels' => $quantity->pluck('type'),
            'data' => $quantity->pluck('total'),
        ];

        // === Failure Count by Substation (Bar) ===
        $failures = SensorStatusHistory::select('substations.substation_name', DB::raw('count(*) as total'))
            ->join('sensors', 'sensor_status_histories.sensor_id', '=', 'sensors.id')
            ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
            ->join('substations', 'switchgears.substation_id', '=', 'substations.id')
            ->where('sensor_status_histories.new_status', 'offline')
            ->whereYear('sensor_status_histories.created_at', $year)
            ->when($sensorType, fn($q) => $q->where('sensors.type', $sensorType))
            ->when($substationId, fn($q) => $q->where('substations.id', $substationId))
            ->groupBy('substations.substation_name')
            ->orderBy('total', 'desc')
            ->get();

        $failureData = [
            'labels' => $failures->pluck('substation_name'),
            'data' => $failures->pluck('total'),
        ];

        // === Sensor Trend (Line Chart by Month) ===
        $trendRaw = SensorStatusHistory::select(
                DB::raw('MONTH(sensor_status_histories.created_at) as month'),
                DB::raw('count(*) as count')
            )
            ->join('sensors', 'sensor_status_histories.sensor_id', '=', 'sensors.id')
            ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
            ->when($substationId, fn($q) => $q->where('switchgears.substation_id', $substationId))
            ->when($sensorType, fn($q) => $q->where('sensors.type', $sensorType))
            ->whereYear('sensor_status_histories.created_at', $year)
            ->groupBy(DB::raw('MONTH(sensor_status_histories.created_at)'))
            ->orderBy('month')
            ->get();


        $trendData = [
            'labels' => range(1, 12),
            'data' => collect(range(1, 12))->map(function ($m) use ($trendRaw) {
                return $trendRaw->firstWhere('month', $m)->count ?? 0;
            }),
        ];
        

        // === Substations list for filter dropdown ===
        $substations = Substation::select('id', 'substation_name')->get();

        return view('analytics.index', [
            'year' => $year,
            'substationId' => $substationId,
            'sensorType' => $sensorType,
            'failures' => $failureData,
            'trend' => $trendData,
            'quantity' => $quantityData,
            'substations' => $substations
        ]);
    }

    public function update(Request $request, Sensor $sensor)
    {
        $request->validate([
            'status' => 'required|in:online,offline,repairing'
        ]);

        // Resolve previous downtime if status becomes ONLINE
        if ($request->status === 'online') {
            \App\Models\SensorStatusHistory::where('sensor_id', $sensor->id)
                ->whereNull('resolved_at')
                ->whereIn('new_status', ['offline', 'repairing'])
                ->latest()
                ->first()?->update(['resolved_at' => now()]);
        }

        // Create new status history
        \App\Models\SensorStatusHistory::create([
            'sensor_id' => $sensor->id,
            'user_id' => auth()->id(),
            'old_status' => $sensor->status,
            'new_status' => $request->status,
        ]);

        // Update sensor status
        $sensor->status = $request->status;
        $sensor->save();

        return redirect()->back()->with('success', 'Sensor status updated successfully.');
    }

    public function substation(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month');
        $substationId = $request->input('substation');

        $substations = Substation::all();

        // 1. Total Sensors per Substation
        $sensorCountsRaw = DB::table('sensors')
            ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
            ->join('substations', 'switchgears.substation_id', '=', 'substations.id')
            ->select('substations.substation_name', DB::raw('count(sensors.id) as count'))
            ->when($substationId, fn($q) => $q->where('substations.id', $substationId))
            ->groupBy('substations.substation_name')
            ->get();

        $sensorCounts = [
            'labels' => $sensorCountsRaw->pluck('substation_name'),
            'data' => $sensorCountsRaw->pluck('count'),
        ];

        // 2. Failure Trend (Offline per Month)
        $failureTrendRaw = DB::table('sensor_status_histories')
            ->join('sensors', 'sensor_status_histories.sensor_id', '=', 'sensors.id')
            ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
            ->when($substationId, fn($q) => $q->where('switchgears.substation_id', $substationId))
            ->where('sensor_status_histories.new_status', 'offline')
            ->whereYear('sensor_status_histories.created_at', $year)
            ->when($month, fn($q) => $q->whereMonth('sensor_status_histories.created_at', $month))
            ->select(DB::raw('MONTH(sensor_status_histories.created_at) as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $failureTrend = [
            'labels' => collect(range(1, 12))->map(fn($m) => DateTime::createFromFormat('!m', $m)->format('M')),
            'data' => collect(range(1, 12))->map(function ($m) use ($failureTrendRaw) {
                return $failureTrendRaw->firstWhere('month', $m)->total ?? 0;
            }),
        ];

        // 3. Uptime vs Downtime (Total Sensors vs total Offline logs)
        $totalSensors = DB::table('sensors')
            ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
            ->when($substationId, fn($q) => $q->where('switchgears.substation_id', $substationId))
            ->count();

        $totalFailures = DB::table('sensor_status_histories')
            ->join('sensors', 'sensor_status_histories.sensor_id', '=', 'sensors.id')
            ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
            ->where('sensor_status_histories.new_status', 'offline')
            ->whereYear('sensor_status_histories.created_at', $year)
            ->when($month, fn($q) => $q->whereMonth('sensor_status_histories.created_at', $month))
            ->when($substationId, fn($q) => $q->where('switchgears.substation_id', $substationId))
            ->count();

        $uptimeVsDowntime = [
            'uptime' => max(0, $totalSensors - $totalFailures),
            'downtime' => $totalFailures,
        ];

        // 4. Substation Ranking (Lowest Offline Count = Higher Rank)
        $rankingRaw = DB::table('sensor_status_histories')
            ->join('sensors', 'sensor_status_histories.sensor_id', '=', 'sensors.id')
            ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
            ->join('substations', 'switchgears.substation_id', '=', 'substations.id')
            ->where('sensor_status_histories.new_status', 'offline')
            ->whereYear('sensor_status_histories.created_at', $year)
            ->when($month, fn($q) => $q->whereMonth('sensor_status_histories.created_at', $month))
            ->select('substations.substation_name', DB::raw('count(*) as offline_count'))
            ->groupBy('substations.substation_name')
            ->orderBy('offline_count')
            ->get();

        $ranking = [
            'labels' => $rankingRaw->pluck('substation_name'),
            'data' => $rankingRaw->pluck('offline_count')->map(fn($x) => 100 - ($x * 2)), // Example scoring formula
        ];

        // 5. Top Problematic Sensors
        $topProblematicSensorsRaw = DB::table('sensor_status_histories')
            ->join('sensors', 'sensor_status_histories.sensor_id', '=', 'sensors.id')
            ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
            ->join('substations', 'switchgears.substation_id', '=', 'substations.id')
            ->where('sensor_status_histories.new_status', 'offline')
            ->whereYear('sensor_status_histories.created_at', $year)
            ->when($month, fn($q) => $q->whereMonth('sensor_status_histories.created_at', $month))
            ->when($substationId, fn($q) => $q->where('substations.id', $substationId))
            ->select('sensors.tag', DB::raw('count(*) as offline_count'))
            ->groupBy('sensors.tag')
            ->orderByDesc('offline_count')
            ->limit(10)
            ->get();

        $topProblematicSensors = [
            'labels' => $topProblematicSensorsRaw->pluck('tag'),
            'data' => $topProblematicSensorsRaw->pluck('offline_count'),
        ];

        // 6. Average Downtime Duration per Substation
        $avgDowntimeRaw = DB::table('sensor_status_histories')
            ->join('sensors', 'sensor_status_histories.sensor_id', '=', 'sensors.id')
            ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
            ->join('substations', 'switchgears.substation_id', '=', 'substations.id')
            ->whereNotNull('sensor_status_histories.resolved_at')
            ->where('sensor_status_histories.new_status', 'offline')
            ->whereYear('sensor_status_histories.created_at', $year)
            ->when($month, fn($q) => $q->whereMonth('sensor_status_histories.created_at', $month))
            ->when($substationId, fn($q) => $q->where('substations.id', $substationId))
            ->select(
                'substations.substation_name',
                DB::raw('AVG(TIMESTAMPDIFF(MINUTE, sensor_status_histories.created_at, sensor_status_histories.resolved_at)) as avg_minutes')
            )
            ->groupBy('substations.substation_name')
            ->orderByDesc('avg_minutes')
            ->get();

        $avgDowntime = [
            'labels' => $avgDowntimeRaw->pluck('substation_name'),
            'data' => $avgDowntimeRaw->pluck('avg_minutes')->map(fn($min) => round($min, 2)),
        ];



        return view('analytics.substations', compact(
            'year', 'month', 'substationId', 'substations',
            'sensorCounts', 'failureTrend', 'uptimeVsDowntime', 'ranking', 'topProblematicSensors', 'avgDowntime'
        ));
    }


}
