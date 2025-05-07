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
    public function index()
    {
        $sensors = Sensor::with('switchgear.substation')->paginate(20);
        return view('sensors.manage', compact('sensors'));
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
        //
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
