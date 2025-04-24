<?php

namespace App\Http\Controllers;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Substation;
use App\Models\Sensor;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SensorStatusExport;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    // public function sensorStatusSummary()
    // {
    //     $sensors = Sensor::with(['switchgear.substation', 'latestMeasurement'])->get();

    //     $grouped = $sensors->groupBy(fn($s) => $s->switchgear->substation->substation_name);

    //     $data = $grouped->map(function ($sensorList, $substationName) {
    //         return [
    //             'substation' => $substationName,
    //             'total' => $sensorList->count(),
    //             'ALARM' => $sensorList->where('latestMeasurement.status', 'ALARM')->count(),
    //             'AWAIT' => $sensorList->where('latestMeasurement.status', 'AWAIT')->count(),
    //             'NORMAL' => $sensorList->where('latestMeasurement.status', 'NORMAL')->count(),
    //         ];
    //     });

    //     $pdf = Pdf::loadView('reports.sensor-status-summary', ['data' => $data]);
    //     return $pdf->stream('sensor-status-summary.pdf');
    // }
    public function sensorStatusSummary(Request $request)
    {
        $substations = Substation::pluck('substation_name', 'id');
        $sensorTypes = Sensor::distinct()->pluck('type');
        
        $query = Sensor::with(['switchgear.substation', 'latestMeasurement']);
        
        // Apply filters if provided
        if ($request->filled('substation_id')) {
            $query->whereHas('switchgear.substation', function($q) use ($request) {
                $q->where('id', $request->substation_id);
            });
        }
        
        if ($request->filled('sensor_type')) {
            $query->where('type', $request->sensor_type);
        }
        
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereHas('latestMeasurement', function($q) use ($request) {
                $q->whereBetween('created_at', [
                    $request->date_from, 
                    $request->date_to
                ]);
            });
        }
        
        $sensors = $query->get();
        
        $grouped = $sensors->groupBy(fn($s) => $s->switchgear->substation->substation_name);
        
        $data = $grouped->map(function ($sensorList, $substationName) {
            return [
                'substation' => $substationName,
                'total' => $sensorList->count(),
                'ALARM' => $sensorList->where('latestMeasurement.status', 'ALARM')->count(),
                'AWAIT' => $sensorList->where('latestMeasurement.status', 'AWAIT')->count(),
                'NORMAL' => $sensorList->where('latestMeasurement.status', 'NORMAL')->count(),
            ];
        });
        
        if ($request->has('export')) {
            switch ($request->export) {
                case 'pdf':
                    $pdf = Pdf::loadView('reports.sensor-status-summary', [
                        'data' => $data,
                        'filters' => $request->all()
                    ]);
                    return $pdf->stream('sensor-status-summary.pdf');
                    
                case 'excel':
                    return Excel::download(
                        new SensorStatusExport($data), 
                        'sensor-status-summary.xlsx'
                    );
                    
                case 'csv':
                    return Excel::download(
                        new SensorStatusExport($data), 
                        'sensor-status-summary.csv'
                    );
            }
        }
        
        return view('reports.sensor-status-summary', [
            'data' => $data,
            'substations' => $substations,
            'sensorTypes' => $sensorTypes,
            'filters' => $request->all()
        ]);
    }
}
