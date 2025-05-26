<?php

namespace App\Http\Controllers;
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
        $request->validate([
            'substation_id' => 'required|exists:substations,id',
            'switchgear_id' => 'required|exists:switchgears,id',
            'sensor_id'     => 'required|exists:sensors,id',
            'sensor_status' => 'required|in:offline,repairing',
            'reported_at'   => 'required|date',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
        ]);

        SensorLogReport::create([
            'substation_id' => $request->substation_id,
            'switchgear_id' => $request->switchgear_id,
            'sensor_id'     => $request->sensor_id,
            'reported_by'   => Auth::id(),
            'title'         => $request->title,
            'description'   => $request->description,
            'reported_at'   => now(),
            'status'        => 'open',
        ]);

        // Update sensor status in Sensor table
        $sensor = Sensor::findOrFail($request->sensor_id);
        $sensor->status = $request->sensor_status;
        $sensor->save();

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

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'sensor_status' => 'required|in:online,repairing',
        ]);

        $report = SensorLogReport::findOrFail($id);
        $sensor = $report->sensor;

        $sensor->status = $request->sensor_status;
        $sensor->save();

        return redirect()->back()->with('success', 'Sensor status updated.');
    }

}