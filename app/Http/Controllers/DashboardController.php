<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        //summary for popup alert (top 3 offline)
        $offlineSummary = $offlineSensors->take(3)->map(function ($sensor) {
            return [
                'substation' => optional($sensor->switchgear->substation)->substation_name ?? 'Unknown',
                'sensor_type' => $sensor->type,
                'sensor_tag' => $sensor->tag,
            ];
        });

        //group sensors by substation
        $substationOffline = $sensors->groupBy(function ($sensor) {
            return optional($sensor->switchgear->substation)->id;
        });

        // Prepare data for substation chart
        $groupedBySubstation = $sensors->groupBy(function ($sensor) {
            return optional($sensor->switchgear->substation)->substation_name ?? 'Unknown';
        });

        $overviewData = [];

        foreach ($groupedBySubstation as $substationName => $sensorGroup) {
            $overviewData[] = [
                'label' => $substationName,
                'online' => $sensorGroup->where('status', 'online')->count(),
                'repairing' => $sensorGroup->where('status', 'repairing')->count(),
                'offline' => $sensorGroup->where('status', 'offline')->count(),
            ];
        }

        // Count substations that have at least one OFFLINE sensor
        $substationsWithOffline = $substationOffline->filter(function ($group) {
            return $group->contains('status', 'offline');
        });

         // Actual severity stats
        $online = $onlineSensors->count();
        $repairing = $repairingSensors->count();
        $offline = $offlineSensors->count();
        $total = $online + $repairing + $offline;
            
        return view('dashboard', [
            'sensors' => $sensors,
            'substations' => $substations,
            'onlineSensors' => $onlineSensors,
            'repairingSensors' => $repairingSensors,
            'offlineSensors' => $offlineSensors,
            'onlineCount' => $online,
            'repairingCount' => $repairing,
            'offlineCount' => $offline,
            'offlineSummary' => $offlineSummary,
    
            // For chart component
            'totalDefectSubstations' => $substationsWithOffline->count(),
            'severityStats' => [
                'online' => $online,
                'repairing' => $repairing,
                'offline' => $offline,
                'total' => $total,
            ],
            'overviewData' => $overviewData
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
