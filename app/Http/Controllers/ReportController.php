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
    // public function index(Request $request)
    // {
    //     $substations = Substation::pluck('substation_name', 'id');
    //     $sensorTypes = Sensor::distinct()->pluck('type');
        
    //     return view('reports.index', compact('substations', 'sensorTypes'));
    // }
    public function index(Request $request)
    {
        $substations = Substation::all();
        $sensorTypes = Sensor::distinct()->pluck('type');
        
        $query = Sensor::with(['switchgear.substation', 'latestMeasurement' => function($q) {
                $q->latest();
            }
        ]);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->whereHas('latestMeasurement', function($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereHas('latestMeasurement', function($q) use ($request) {
                $q->whereBetween('created_at', [
                    $request->start_date, 
                    $request->end_date
                ]);
            });
        }

        $sensors = $query->get();

        // return view('reports.index', compact('substations', 'sensorTypes', 'sensors'));
        // return view('reports.index', [
        //     'sensors' => $query->paginate(20), // Added pagination
        //     'substations' => Substation::pluck('substation_name', 'id'),
        //     'sensorTypes' => Sensor::distinct()->pluck('type'),
        //     'filters' => $request->all()
        // ]);
        return view('reports.index', [
            'sensors' => $query->paginate(20),
            'substations' => $substations,
            'sensorTypes' => $sensorTypes,
            'filters' => $request->all(),
        ]);
    }

    public function sensorStatusSummary(Request $request)
    {
        $request->validate([
            'substation_id' => 'nullable|exists:substations,id',
            'sensor_type' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'export' => 'nullable|in:pdf,excel,csv'
        ]);

        $query = Sensor::with(['switchgear.substation', 'latestMeasurement']);
        
        // Apply filters
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
        
        $grouped = $sensors->groupBy(function($sensor) {
            return optional($sensor->switchgear->substation)->substation_name ?? 'Unknown Substation';
        });
        
        $data = $grouped->map(function ($sensorList, $substationName) {
            return [
                'substation' => $substationName,
                'total' => $sensorList->count(),
                'ALARM' => $sensorList->where('latestMeasurement.status', 'ALARM')->count(),
                'AWAIT' => $sensorList->where('latestMeasurement.status', 'AWAIT')->count(),
                'NORMAL' => $sensorList->where('latestMeasurement.status', 'NORMAL')->count(),
            ];
        });
        
        // Handle exports
        if ($request->has('export')) {
            $filters = $request->only(['substation_id', 'sensor_type', 'date_from', 'date_to']);
            
            switch ($request->export) {
                case 'pdf':
                    $pdf = Pdf::loadView('reports.sensor-status-summary', [
                        'data' => $data,
                        'filters' => $filters
                    ]);
                    return $pdf->stream('sensor-report.pdf');
                    
                case 'excel':
                    return Excel::download(
                        new SensorStatusExport($data, $filters), 
                        'sensor-report.xlsx'
                    );
                    
                case 'csv':
                    return Excel::download(
                        new SensorStatusExport($data, $filters), 
                        'sensor-report.csv'
                    );
            }
        }
        
        // return view('reports.sensor-status-summary', [
        //     'data' => $data,
        //     'substations' => Substation::pluck('substation_name', 'id'),
        //     'sensorTypes' => Sensor::distinct()->pluck('type'),
        //     'filters' => $request->all()
        // ]);
        // Regular HTML response
        return view('reports.sensor-status', compact('data', 'filters'));
    }

}
