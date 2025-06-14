<?php

namespace App\Http\Controllers;

use App\Services\SensorService;
use App\Models\Sensor;
use App\Models\Substation;
use App\Models\SensorStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;


class SensorController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $substationId = $request->input('substation');
        $sensorTag = $request->input('sensor');
        $sensorType = $request->input('type');

        $query = Sensor::with(['switchgear.substation', 'latestLogReport']);

        if (in_array($filter, ['online', 'offline', 'repairing'])) {
            $query->where('status', $filter);
        }

        if ($substationId) {
            $query->whereHas('switchgear.substation', function ($q) use ($substationId) {
                $q->where('id', $substationId);
            });
        }

        if ($sensorTag) {
            $query->where('tag', 'like', '%' . $sensorTag . '%');
        }

        if ($sensorType) {
            $query->where('type', $sensorType);
        }

        // $sensors = $query->paginate(10);
        $sensors = $query->orderBy('updated_at', 'desc')->paginate(10);

        $substations = \App\Models\Substation::all();
        $sensorTypes = Sensor::select('type')->distinct()->pluck('type');

        $onlineCount = Sensor::where('status', 'online')->count();
        $repairingCount = Sensor::where('status', 'repairing')->count();
        $offlineCount = Sensor::where('status', 'offline')->count();

        return view('sensors.manage', compact(
            'sensors',
            'filter',
            'substations',
            'substationId',
            'sensorTag',
            'sensorTypes',
            'onlineCount',
            'repairingCount',
            'offlineCount'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $substations = Substation::with('switchgears')->get();
        $selectedSubstation = null;
        $selectedSwitchgear = null;

        if (old('substation_id')) {
            $selectedSubstation = Substation::with('switchgears')->find(old('substation_id'));
        }

        if (old('switchgear_id')) {
            $selectedSwitchgear = \App\Models\Switchgear::find(old('switchgear_id'));
        }

        return view('sensors.create', compact('substations', 'selectedSubstation', 'selectedSwitchgear'));
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'switchgear_id' => 'required|exists:switchgears,id',
            'type' => 'required|in:Partial Discharge,Thermal',
            'status' => 'required|in:online,offline,repairing',
            'brand_name' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255',
            'warranty' => 'nullable|string|max:255',
        ]);

        // ✅ Load switchgear with substation
        $switchgear = \App\Models\Switchgear::with('substation')->findOrFail($request->switchgear_id);

        // ✅ Get parts for sensor tag
        $substationCode = strtoupper($switchgear->substation->code ?? 'N/A');
        $sensorTypeCode = $request->type === 'Partial Discharge' ? 'PD' : 'TH';
        $panelCode = strtoupper($switchgear->panel_code ?? 'PNL'); // <== here
        $compartmentCode = strtoupper($switchgear->compartment_code ?? 'CMP'); // <== here

        // ✅ Build the tag prefix
        $prefix = "{$substationCode}_{$sensorTypeCode}_{$panelCode}_{$compartmentCode}_";

        // ✅ Find the last tag used with same prefix
        $latestTag = \App\Models\Sensor::where('tag', 'like', $prefix . '%')
            ->orderBy('tag', 'desc')
            ->pluck('tag')
            ->first();

        $nextNumber = 1;
        if ($latestTag) {
            $lastParts = explode('_', $latestTag);
            $lastIncrement = intval(end($lastParts));
            $nextNumber = $lastIncrement + 1;
        }

        $generatedTag = $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT); // e.g. _001

        // ✅ Store new sensor
        Sensor::create([
            'switchgear_id' => $request->switchgear_id,
            'tag' => $generatedTag,
            'type' => $request->type,
            'status' => $request->status,
            'brand_name' => $request->brand_name,
            'sku' => $request->sku,
            'warranty' => $request->warranty,
            'status_changed_at' => now(),
        ]);

        return redirect()->route('sensors.create')->with('success', "Sensor registered with tag: $generatedTag");
    }

    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $data = array_map('str_getcsv', file($file));
        // $header = array_map('trim', $data[0]);
        $header = array_map(fn($h) => trim(str_replace("\u{FEFF}", '', $h)), $data[0]);
        $requiredHeaders = ['switchgear_id', 'tag', 'type', 'status'];

        foreach ($requiredHeaders as $col) {
            if (!in_array($col, $header)) {
                return redirect()->back()->with('error', "Missing required column: $col");
            }
        }

        $created = 0;
        foreach (array_slice($data, 1) as $row) {
            $rowData = array_combine($header, $row);

            // Skip if required fields are missing
            if (empty($rowData['switchgear_id']) || empty($rowData['tag'])) {
                continue;
            }

            // Prevent duplicates
            if (Sensor::where('tag', $rowData['tag'])->exists()) {
                continue;
            }

            try {
                Sensor::create([
                    'switchgear_id' => $rowData['switchgear_id'],
                    'tag'           => $rowData['tag'],
                    'type'          => $rowData['type'] ?? 'Thermal',
                    'status'        => $rowData['status'] ?? 'online',
                    'brand_name'    => $rowData['brand_name'] ?? null,
                    'sku'           => $rowData['sku'] ?? null,
                    'warranty'      => $rowData['warranty'] ?? null,
                    'status_changed_at' => now(),
                ]);
                $created++;
            } catch (\Exception $e) {
                dd('Failed to insert sensor:', $rowData, $e->getMessage());
            }

        }
        
        // foreach (array_slice($data, 1) as $row) {
        //     $rowData = array_combine($header, $row);
        //     dd($rowData);  // 🧪 Debug right here
        // }

        return redirect()->route('sensors.create')->with('success', "$created sensors uploaded successfully.");
    }


    /**
     * Display the specified resource.
     */
    public function show(Sensor $sensor)
    {
        // Eager load all needed relationships at once
        $sensor->load(['switchgear.substation', 'logReports.reporter']);

        // Return your view — update view name if needed ('sensors.view' or 'sensors.show')
        return view('sensors.view', compact('sensor'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sensor $sensor)
    {
        //
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

        // Update sensor status and timestamp
        $sensor->status = $newStatus;
        $sensor->status_changed_at = now();
        $sensor->save();

        // Update report with status change info
        $report->old_status = $oldStatus;
        $report->new_status = $newStatus;
        $report->status_changed_by = auth()->id();
        $report->status_changed_at = now();
        $report->save();

        return redirect()->route('sensors.show', $report->id)
                        ->with('success', 'Sensor status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sensor $sensor)
    {
        //
    }

    public function export(Request $request, $format)
    {
        $sensors = Sensor::with(['switchgear.substation', 'latestLogReport'])->get();

        if ($format === 'csv') {
            $filename = 'sensor_status_summary.csv';

            $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($sensors) {
                $handle = fopen('php://output', 'w');

                // Header row
                fputcsv($handle, ['Tag', 'Type', 'Substation', 'Panel', 'Status', 'Reported At', 'Updated At']);

                foreach ($sensors as $sensor) {
                    fputcsv($handle, [
                        $sensor->tag,
                        ucfirst($sensor->type),
                        optional($sensor->switchgear->substation)->substation_name ?? '-',
                        $sensor->switchgear->panel_name ?? '-',
                        ucfirst($sensor->status),
                        $sensor->latestLogReport?->reported_at 
                            ? $sensor->latestLogReport->reported_at->format('Y-m-d H:i') 
                            : 'N/A',
                        $sensor->updated_at->format('Y-m-d H:i'),
                    ]);
                }

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

            return $response;
        }

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('sensors.pdf-summary', compact('sensors'));
            return $pdf->download('sensor_status_summary.pdf');
        }

        return redirect()->back()->with('error', 'Invalid export format selected.');
    }

    public function exportLogs($id, $format)
    {
        $sensor = Sensor::with(['switchgear.substation', 'logReports.reporter'])->findOrFail($id);

        if ($format === 'csv') {
            $filename = "sensor_logs_{$sensor->tag}.csv";

            $response = new StreamedResponse(function () use ($sensor) {
                $handle = fopen('php://output', 'w');

                // Header row
                fputcsv($handle, ['Case ID', 'Reported By', 'Status', 'Reported At', 'Resolved At', 'Downtime']);

                foreach ($sensor->logReports as $report) {
                    fputcsv($handle, [
                        $report->case_id,
                        optional($report->reporter)->name ?? '-',
                        ucfirst($report->status),
                        $report->reported_at?->format('Y-m-d H:i') ?? '-',
                        $report->resolved_at?->format('Y-m-d H:i') ?? '-',
                        $report->status === 'open' ? 'In Progress' : ($report->calculated_duration ?? '-'),
                    ]);
                }

                fclose($handle);
            });

            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
            return $response;
        }

        if ($format === 'pdf') {
            return \PDF::loadView('sensors.log-pdf', compact('sensor'))->download("sensor_logs_{$sensor->tag}.pdf");
        }

        return redirect()->back()->with('error', 'Invalid export format.');
    }
}
