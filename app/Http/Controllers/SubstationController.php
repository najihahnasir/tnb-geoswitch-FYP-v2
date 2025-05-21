<?php

namespace App\Http\Controllers;

use App\Models\Substation;
use Illuminate\Http\Request;

class SubstationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function summary()
    {
        $substations = \App\Models\Substation::with(['switchgears.sensors'])->get();

        $summaryData = $substations->map(function ($substation) {
            $sensors = $substation->switchgears->flatMap->sensors;

            $substation->total_sensors = $sensors->count();
            $substation->online = $sensors->where('status', 'online')->count();
            $substation->offline = $sensors->where('status', 'offline')->count();
            $substation->repairing = $sensors->where('status', 'repairing')->count();
            $substation->last_updated = optional($sensors->sortByDesc('updated_at')->first())->updated_at;
            $substation->top_issue = optional(
                $sensors->groupBy('type')->sortByDesc(fn($group) => $group->where('status', 'offline')->count())->first()
            )->first()?->type ?? '-';

            return $substation;
        });

        return view('substations.summary', ['substations' => $summaryData]);
    }

    // public function mapView($substationId)
    // {
    //     $substation = Substation::with(['switchgears.sensors'])->findOrFail($substationId);

    //     $sensors = $substation->switchgears->flatMap->sensors;

    //     $sensorMarkers = $sensors->map(function ($sensor) {
    //         return [
    //             'tag' => $sensor->tag,
    //             'type' => $sensor->type,
    //             'status' => $sensor->status,
    //             'panel' => $sensor->switchgear->panel ?? '-',
    //             'latitude' => $sensor->latitude,
    //             'longitude' => $sensor->longitude,
    //         ];
    //     });

    //     return view('dashboard.map-view', [
    //         'substation' => $substation,
    //         'sensorMarkers' => $sensorMarkers
    //     ]);
    // }


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
        $validated = $request->validate([
            'substation_name' => 'required|string|max:255',
            'code' => 'required|string|unique:substations,code',
            'description' => 'required|string|max:1000',
            'substation_location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'status' => 'required|in:active,inactive',
            'type' => 'required|in:Main,Backup',
        ]);

        Substation::create($validated);

        return redirect()->route('dashboard')->with('success', 'Substation registered successfully!');
    }


    /**
     * Display the specified resource.
     */

    public function show(Substation $substation)
    {
        // Flatten all sensors under this substation
        $sensorMarkers = $substation->switchgears->flatMap->sensors->map(function ($sensor) {
            return [
                'tag' => $sensor->tag,
                'type' => $sensor->type,
                'status' => $sensor->status,
                'panel' => $sensor->switchgear->panel ?? 'Unknown',
                'lat' => $sensor->latitude ?? $sensor->switchgear->latitude,
                'lng' => $sensor->longitude ?? $sensor->switchgear->longitude,
            ];
        });

        return view('substations.show', [
            'substation' => $substation,
            'sensorMarkers' => $sensorMarkers
        ]);
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
