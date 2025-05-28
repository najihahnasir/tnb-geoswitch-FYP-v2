<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Switchgear;
use App\Models\Substation;

class SwitchgearSeeder extends Seeder
{
    public function run(): void
    {
        // Standard layout for all substations
        $panels = [
            'Bus Section 30' => ['Main Busbar', 'Reserve Busbar'],
            'Bus Coupler 34' => ['Main Busbar', 'Reserve Busbar'],
            'Feeder 1' => ['Main Busbar', 'Reserve Busbar', 'CB - TOP', 'CB - BOTTOM', 'Cable Compartment'],
            'Feeder 2' => ['Main Busbar', 'Reserve Busbar', 'CB - TOP', 'CB - BOTTOM', 'Cable Compartment'],
            'Feeder 3' => ['Main Busbar', 'Reserve Busbar', 'CB - TOP', 'CB - BOTTOM', 'Cable Compartment'],
        ];

        $substations = Substation::all();

        foreach ($substations as $substation) {
            foreach ($panels as $panel => $compartments) {
                foreach ($compartments as $compartment) {
                    $displayName = "$panel - $compartment";

                    Switchgear::updateOrInsert([
                        'substation_id' => $substation->id,
                        'panel' => $panel,
                        'compartment' => $compartment,
                    ], [
                        'substation_id' => $substation->id,
                        'name' => $panel,
                        'display_name' => $displayName,
                        'lineup' => '11kV Lineup',
                        'panel' => $panel,
                        'compartment' => $compartment,
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        echo "✅ Switchgears updated with display_name, no duplication created.\n";
    }
}
