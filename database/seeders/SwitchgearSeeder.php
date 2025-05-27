<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Switchgear;
use App\Models\Substation;

class SwitchgearSeeder extends Seeder
{
    public function run(): void
    {
        // Define layout for each panel (shared across all substations)
        $panels = [
            'Bus Section 30' => ['Main Busbar', 'Reserve Busbar'],
            'Bus Coupler 34' => ['Main Busbar', 'Reserve Busbar'],
            'Feeder 1' => ['Main Busbar', 'Reserve Busbar', 'CB - TOP', 'CB - BOTTOM', 'Cable Compartment'],
            'Feeder 2' => ['Main Busbar', 'Reserve Busbar', 'CB - TOP', 'CB - BOTTOM', 'Cable Compartment'],
            'Feeder 3' => ['Main Busbar', 'Reserve Busbar', 'CB - TOP', 'CB - BOTTOM', 'Cable Compartment'],
        ];

        // Loop all substations and assign the same layout
        $substations = \App\Models\Substation::all();

        foreach ($substations as $substation) {
            foreach ($panels as $panel => $compartments) {
                foreach ($compartments as $compartment) {
                    Switchgear::updateOrInsert([
                        'substation_id' => $substation->id,
                        'panel' => $panel,
                        'compartment' => $compartment,
                    ], [
                        'substation_id' => $substation->id,
                        'name' => $panel,
                        'lineup' => '11kV Lineup',
                        'panel' => $panel,
                        'compartment' => $compartment,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        echo "✅ Switchgears seeded for all substations with standardized layout.\n";
    }
}
