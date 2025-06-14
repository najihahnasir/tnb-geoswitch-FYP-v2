<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Substation;
use App\Models\Sensor;
use App\Models\SensorLogReport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubstationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function summary(Request $request)
    {
        $codePrefix = $request->input('code_prefix');
        $location = $request->input('location');
        $statusFilter = $request->input('status');

        $substations = Substation::with(['switchgears.sensors.latestLogReport'])->get();

        $summaryData = $substations->map(function ($substation) {
            $sensors = $substation->switchgears->flatMap(function ($switchgear) {
                return $switchgear->sensors->map(function ($sensor) {
                    if ($sensor->latestLogReport) {
                        $sensor->status = $sensor->latestLogReport->new_status;
                    }
                    return $sensor;
                });
            });

            $substation->total_sensors = $sensors->count();
            $substation->online = $sensors->where('status', 'online')->count();
            $substation->repairing = $sensors->where('status', 'repairing')->count();
            $substation->offline = $sensors->where('status', 'offline')->count();
            $substation->last_updated = $sensors->pluck('latestLogReport.updated_at')->filter()->sortDesc()->first();

            return $substation;
        });

        // Filter by code prefix (PPU/SSU)
        if ($codePrefix) {
            $summaryData = $summaryData->filter(fn($s) => Str::startsWith($s->code, $codePrefix));
        }

        // Filter by location keyword
        if ($location) {
            $summaryData = $summaryData->filter(fn($s) =>
                str_contains(strtolower($s->substation_location), strtolower($location))
            );
        }

        // (Optional) Future filter by status count
        if (in_array($statusFilter, ['online', 'offline', 'repairing'])) {
            $summaryData = $summaryData->filter(fn($s) => $s->{$statusFilter} > 0);
        }

        return view('substations.summary', [
            'substations' => $summaryData,
            'codePrefix' => $codePrefix,
            'location' => $location,
            'statusFilter' => $statusFilter,
        ]);
    }


    public function exportPdf(Substation $substation)
    {
        $substation->load('switchgears.sensors'); // Load related data

        $pdf = Pdf::loadView('substations.pdf-show', compact('substation'));
        return $pdf->download("substation-summary-{$substation->id}.pdf");
    }

    public function summaryPdf()
    {
        $substations = Substation::with('sensors')->get(); // Include sensors for counts

        $pdf = Pdf::loadView('substations.pdf-summary', compact('substations'));
        return $pdf->download('substation-overview-summary.pdf');
    }

    public function summaryCsv()
    {
        $substations = Substation::with('switchgears.sensors')->get();

        $filename = 'substation-summary-overview.csv';

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($substations) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, ['Substation', 'Switchgear Panel', 'Sensor Tag', 'Sensor Type', 'Sensor Status', 'Updated At']);

            foreach ($substations as $substation) {
                foreach ($substation->switchgears as $switchgear) {
                    foreach ($switchgear->sensors as $sensor) {
                        fputcsv($handle, [
                            $substation->substation_name,
                            $switchgear->panel_name ?? 'Panel',
                            $sensor->tag,
                            ucfirst($sensor->type),
                            ucfirst($sensor->status),
                            $sensor->updated_at->format('Y-m-d H:i'),
                        ]);
                    }
                }
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('substations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $substation_name = strtoupper($request->input('substation_name'));

        // Generate CODE
        $lastWord = strtoupper(Str::slug(Str::afterLast($substation_name, ' '), '_'));
        $prefix = Str::contains($substation_name, 'PMU') ? 'PMU' : 'PPU';
        $code = $prefix . '_' . $lastWord;

        // Make sure the code is unique
        if (Substation::where('code', $code)->exists()) {
            return back()->withErrors(['code' => 'Generated code already exists: ' . $code])->withInput();
        }

        // Validate remaining fields (exclude 'code')
        $validated = $request->validate([
            'substation_name'     => 'required|string|max:255',
            'description'         => 'required|string|max:1000',
            'substation_location' => 'required|string|max:255',
            'latitude'            => 'required|numeric|between:-90,90',
            'longitude'           => 'required|numeric|between:-180,180',
            'status'              => 'required|in:active,inactive',
            'type'                => 'required|in:Main,Backup',
        ]);

        // Inject the generated code
        $validated['code'] = $code;

        // Store
        Substation::create($validated);

        return redirect()->route('dashboard')->with('success', 'Substation registered successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Substation $substation)
    {
        $substation->load('switchgears.sensors');

        $sensorMarkers = $substation->switchgears
            ->flatMap->sensors
            ->map(function ($sensor) {
                return [
                    'tag' => $sensor->tag,
                    'type' => $sensor->type,
                    'status' => $sensor->latestLogReport->new_status ?? $sensor->status,
                    'lat' => $sensor->latitude ?? $sensor->switchgear->substation->latitude,
                    'lng' => $sensor->longitude ?? $sensor->switchgear->substation->longitude,
                    'panel' => $sensor->switchgear->panel_name ?? '-',
                ];
            })
            ->values();

        return view('substations.show', compact('substation', 'sensorMarkers'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Substation $substation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Substation $substation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Substation $substation)
    {
        //
    }
}
