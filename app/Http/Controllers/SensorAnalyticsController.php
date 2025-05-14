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

        $totalSensors = $filteredSensors->count();

        $offlineSensors = $filteredSensors->where('status', 'offline')->count();

        $activeSubstations = $filteredSensors->pluck('switchgear.substation_id')->unique()->count();

        // 🔹 History Data for Charts
        $sensorHistories = DB::table('sensor_status_histories')
            ->join('sensors', 'sensor_status_histories.sensor_id', '=', 'sensors.id')
            ->join('switchgears', 'sensors.switchgear_id', '=', 'switchgears.id')
            ->join('substations', 'switchgears.substation_id', '=', 'substations.id')
            ->selectRaw('sensors.type, sensors.status, substations.substation_name,
                        COUNT(*) as failures, YEAR(sensor_status_histories.created_at) as year,
                        MONTH(sensor_status_histories.created_at) as month')
            ->when($sensorType, fn($q) => $q->where('sensors.type', $sensorType))
            ->when($substationId, fn($q) => $q->where('switchgears.substation_id', $substationId))
            ->when($year, fn($q) => $q->whereYear('sensor_status_histories.created_at', $year))
            ->when($month, fn($q) => $q->whereMonth('sensor_status_histories.created_at', $month))
            ->groupBy('sensors.type', 'sensors.status', 'substations.substation_name', 'year', 'month')
            ->get();

        // Dropdowns
        $sensorTypes = Sensor::select('type')->distinct()->pluck('type');
        $substations = Substation::all();

        return view('analytics.sensor', compact(
            'sensorHistories',
            'sensorTypes',
            'substations',
            'sensorType',
            'substationId',
            'year',
            'month',
            'totalSensors',
            'offlineSensors',
            'activeSubstations',
            'substationNames'
        ));
    }

}
