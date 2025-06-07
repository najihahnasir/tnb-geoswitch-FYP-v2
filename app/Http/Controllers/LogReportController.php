<?php

namespace App\Http\Controllers;
use App\Services\SensorService;
// use Carbon\Carbon;
use App\Models\Sensor;
use App\Models\Substation;
use App\Models\Switchgear;
use App\Models\SensorLogReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class LogReportController extends Controller
{
    public function index(Request $request)
{
    $query = SensorLogReport::with([
        'substation',
        'switchgear',
        'sensor',
        'reporter',
        'statusChanger'
    ]);

    // ✅ Filter by Substation (optional)
    if ($request->filled('substation')) {
        $query->where('substation_id', $request->substation);
    }

    // ✅ Filter by Case Status (open/closed)
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // ✅ Filter by Sensor Status (online/repairing/offline)
    if ($request->filled('new_status')) {
        $query->where('new_status', $request->new_status);
    }

    // ✅ Filter by Month
    if ($request->filled('month')) {
        $query->whereMonth('reported_at', $request->month);
    }

    // ✅ Paginate and sort
    $reports = $query->latest('reported_at')->paginate(10);

    // ✅ Calculate fallback duration
    foreach ($reports as $report) {
        if ($report->reported_at) {
            $endTime = $report->resolved_at ?? now(); // Use now() for OPEN cases
            $diff = $report->reported_at->diff($endTime);

            $label = is_null($report->resolved_at) ? 'LIVE – ' : ''; // Tag open cases
            $report->calculated_duration = $label . $diff->days . 'd ' . $diff->h . 'h ' . $diff->i . 'm';
        } else {
            $report->calculated_duration = '-';
        }
    }


    // ✅ For filter dropdowns
    $substations = Substation::all();
    $statuses = ['open', 'closed'];
    $sensorStatuses = ['online', 'repairing', 'offline'];
    $months = SensorLogReport::selectRaw('MONTH(reported_at) as month')
        ->distinct()
        ->pluck('month');

    return view('log-reports.index', compact(
        'reports',
        'substations',
        'statuses',
        'sensorStatuses',
        'months'
    ));
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
            'status'            => 'open',
            'old_status'        => $sensor->status,
            'new_status'        => $newStatus,
            'status_changed_by' => Auth::id(),
            'status_changed_at' => now(), // ✅ required to prevent SQL error
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
        $logReport->load(['substation', 'switchgear', 'sensor', 'reporter', 'statusChanger']);

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

        // 1. Update the report
        $logReport->update([
            'status'       => 'resolved',
            'resolved_at'  => $logReport->resolved_at ?? now(),            
            'remarks'      => $request->remarks,
        ]);

        // 2. Update the sensor status to "online" and timestamp
        if ($logReport->sensor) {
            $logReport->sensor->update([
                'status' => 'online',
                'status_changed_at' => now()
            ]);
        }

        return redirect()->route('log-reports.index')->with('success', 'Report resolved successfully. Sensor marked as online.');
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
        // dd($report->sensor->status);
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
            return back()->withErrors(['sensor_status' => 'The sensor is already in this status.'])->withInput();
        }

        $oldStatus = $sensor->status;
        $newStatus = $request->sensor_status;

        // Update the sensor itself
        $sensor->status = $newStatus;
        $sensor->status_changed_at = now();
        $sensor->save();

        // Update the log report
        $report->old_status = $oldStatus;
        $report->new_status = $newStatus;
        $report->status_changed_by = auth()->id();
        $report->status_changed_at = now();

        // 🟢 Auto-resolve if new status is ONLINE
        if ($newStatus === 'online') {
            $report->status = 'resolved';
            $report->resolved_at = now();
        }

        // 🟡 Calculate downtime duration if applicable
        // ✅ Always calculate downtime duration when timestamps are available
        // if ($report->reported_at && $sensor->status_changed_at) {
        //     $diff = $report->reported_at->diff($sensor->status_changed_at);
        //     $report->downtime_duration = $diff->days . 'd ' . $diff->h . 'h ' . $diff->i . 'm';
        // }
        if ($report->reported_at && $report->resolved_at) {
            $diff = $report->reported_at->diff($report->resolved_at);
            $report->downtime_duration = "{$diff->d}d {$diff->h}h {$diff->i}m";
        }



        $report->save();
        // $report->refresh(); // reload from DB
        // dd($report->status_changed_by, $report->status_changed_at);

        return redirect()->route('sensors.show', $sensor->id)->with('success', 'Sensor status updated and report resolved.');
    }
}