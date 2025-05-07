<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Substation;
use App\Models\Sensor;
use App\Models\Academician;
use App\Models\ResearchGrant;
use App\Models\Milestone;

class DashboardController extends Controller
{
    
    public function dashboard()
    {   
        $sensors = Sensor::with('switchgear.substation')->get();
        $substations = Substation::all();

        $onlineSensors = $sensors->where('status', 'online');
        $repairingSensors = $sensors->where('status', 'repairing');
        $offlineSensors = $sensors->where('status', 'offline');
        
        // return view('dashboard', compact('sensors', 'substations'));
        return view('dashboard', [
            'sensors' => $sensors,
            'substations' => $substations,
            'onlineSensors' => $onlineSensors,
            'repairingSensors' => $repairingSensors,
            'offlineSensors' => $offlineSensors,
            'onlineCount' => $onlineSensors->count(),
            'repairingCount' => $repairingSensors->count(),
            'offlineCount' => $offlineSensors->count(),
        ]);
    }

    public function index () 
    {
        $substations = [
            [   'name' => 'PPU KOMPLEKS SUKAN',
                'location' => 'Kompleks Sukan',
                'latitude' => 3.051774932,
                'longitude' => 101.6957484,
            ],
            [
                'name' => 'SSU Matrade',
                'location' => 'Matrade',
                'latitude' => 3.17794505,
                'longitude' => 101.6681519,
            ],
            [
                'name' => 'PPU Metroplex',
                'location' => 'Metroplex',
                'latitude' => 3.166338296,
                'longitude' => 101.6918621,
            ],
        ];
        return view ('dashboard', compact('substations'));
    }
}
