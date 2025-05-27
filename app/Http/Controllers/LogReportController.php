<?php

namespace App\Http\Controllers;
use App\Services\SensorService;
use App\Models\Sensor;
use App\Models\Substation;
use App\Models\Switchgear;
use App\Models\SensorLogReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class LogReportController extends Controller
{
    public function index()
    {
        // logger('✅ LogReportController@index called');
        $reports = SensorLogReport::with(['substation', 'switchgear', 'sensor', 'reporter'])
            ->latest('reported_at')
            ->get();

        // dd($reports);

        return view('log-reports.index', compact('reports'));
    }

    public function create()
    {
        $substations = Substation::with('switchgears.sensors')->get();

        return view('log-reports.create', compact('substations'));
    }

    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'substation_id' => 'required|exists:substations,id',
            'switchgear_id' => 'required|exists:switchgears,id',
            'sensor_id'     => 'required|exists:sensors,id',
            'sensor_status' => 'required|in:online,offline,repairing',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
        ]);

        // Find the sensor to get current status
        $sensor = Sensor::findOrFail($request->sensor_id);
        $oldStatus = $sensor->status; //capture old status
        $newStatus = $request->sensor_status; //new status

        // Create new sensor log report with status transition info
        $report = SensorLogReport::create([
            'substation_id'     => $request->substation_id,
            'switchgear_id'     => $request->switchgear_id,
            'sensor_id'         => $request->sensor_id,
            'reported_by'       => Auth::id(),
            'title'             => $request->title,
            'description'       => $request->description,
            'reported_at'       => now(),
            'status'            => 'open',  // Initial report status
            'old_status'        => $oldStatus,
            'new_status'        => $newStatus,
            'status_changed_by' => Auth::id(),
            'status_changed_at' => now(),
        ]);

        // Update sensor status and timestamp
        // $sensor->status = $newStatus;
        // $sensor->status_changed_at = now();
        // $sensor->save();
        SensorService::updateSensorStatus($sensor, $newStatus, $report);

        return redirect()->route('log-reports.index')->with('success', 'Sensor issue reported successfully.');
    }


    public function show(SensorLogReport $logReport)
    {
        return view('log-reports.show', compact('logReport'));
    }

    public function resolve(Request $request, SensorLogReport $logReport)
    {
        if ($logReport->status === 'resolved') {
            return back()->with('warning', 'This report is already resolved.');
        }

        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $logReport->update([
            'status'       => 'resolved',
            'resolved_at'  => now(),
            'remarks'      => $request->remarks,
        ]);

        return redirect()->route('log-reports.index')->with('success', 'Report resolved successfully.');
    }

    public function editStatus($id)
    {
        $report = SensorLogReport::with('sensor')->findOrFail($id);
        $sensor = $report->sensor;
        $downDuration = 'N/A';

        if (in_array($sensor->status, ['offline', 'repairing']) && $sensor->status_changed_at) {
            // Calculate how long the sensor has been down
            $downDuration = $sensor->status_changed_at->diffForHumans();
        }
        dd($report->sensor->status);
        return view('log-reports.edit', compact('report', 'downDuration'));
    }


    public function updateStatus(Request $request, $id)
    {
        $report = SensorLogReport::with('sensor')->findOrFail($id);
        $sensor = $report->sensor;

        $request->validate([
            'sensor_status' => 'required|in:online,offline,repairing',
        ]);

        if ($request->sensor_status === $sensor->status) {
            return back()
                ->withErrors(['sensor_status' => 'The sensor is already in this status.'])
                ->withInput();
        }

        $oldStatus = $sensor->status;
        $newStatus = $request->sensor_status;

        // Update sensor status and timestamp
        // $sensor->status = $newStatus;
        // $sensor->status_changed_at = now();
        // $sensor->save();

        SensorService::updateSensorStatus($sensor, $newStatus, $report);

        // Update the log report with status change info
        // $report->old_status = $oldStatus;
        // $report->new_status = $newStatus;
        // $report->status_changed_by = auth()->id();
        // $report->status_changed_at = now();
        // $report->save();

        SensorService::updateSensorStatus($sensor, $newStatus, $report);

        return redirect()->route('log-reports.show', $report->id)
                        ->with('success', 'Sensor status updated successfully.');
    }

}