<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\SensorStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SensorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $query = Sensor::with('switchgear.substation');
    //     if ($request->has('filter') && in_array($request->filter, ['online', 'offline', 'repairing'])) {
    //         $query->where('status', $request->filter);
    //     }
    //     $sensors = Sensor::with('switchgear.substation')->paginate(20);
    //     return view('sensors.manage', compact('sensors'));
    // }

    // public function index(Request $request)
    // {
    //     $filter = $request->input('filter'); 

    //     $query = Sensor::with('switchgear.substation');

    //     if (in_array($filter, ['online', 'offline', 'repairing'])) {
    //         $query->where('status', $filter);
    //     }

    //     $sensors = $query->paginate(20); 

    //     return view('sensors.manage', compact('sensors', 'filter'));
    // }

    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $substationId = $request->input('substation');
        $sensorTag = $request->input('sensor');
        $sensorType = $request->input('type');

        $query = Sensor::with('switchgear.substation');

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

        $sensors = $query->paginate(10);
        $substations = \App\Models\Substation::all(); // ✅ Add this
        $sensorTypes = Sensor::select('type')->distinct()->pluck('type');

        return view('sensors.manage', compact('sensors', 'filter', 'substations', 'substationId', 'sensorTag', 'sensorTypes'));
    }



    


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Sensor $sensor)
    {
        $sensor->load(['switchgear.substation', 'statusHistory.user']); // ensure this matches your model
        return view('sensors.view', compact('sensor'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sensor $sensor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sensor $sensor)
    {
        $request->validate([
            'status' => 'required|in:online,offline,repairing'
        ]);
    
        // Record the status change
        SensorStatusHistory::create([
            'sensor_id' => $sensor->id,
            'user_id' => Auth::id(),
            'old_status' => $sensor->status,
            'new_status' => $request->status,
        ]);
    
        // Update sensor status
        $sensor->status = $request->status;
        $sensor->save();
    
        return redirect()->back()->with('success', 'Sensor status updated successfully.');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sensor $sensor)
    {
        //
    }
}
