<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Switchgear;
use App\Models\Substation;

class SwitchgearSeeder extends Seeder
{
    public function run(): void
    {
        // Define compartment codes
        $compartmentCodes = [
            'Main Busbar' => 'MBSB',
            'Reserve Busbar' => 'RBSB',
            'CB - TOP' => 'CBT',
            'CB - BOTTOM' => 'CBB',
            'Cable Compartment' => 'CBL',
        ];

        // Define panel codes
        $panelCodes = [
            'Bus Section 30' => 'BS30',
            'Bus Coupler 34' => 'BC34',
            'Feeder 1' => 'F1',
            'Feeder 2' => 'F2',
            'Feeder 3' => 'F3',
        ];

        // Panel layout (panel => compartments)
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
                    $panelCode = $panelCodes[$panel] ?? null;
                    $compCode = $compartmentCodes[$compartment] ?? null;

                    if ($panelCode && $compCode) {
                        $switchgearCode = "{$panelCode}_{$compCode}";

                        Switchgear::updateOrInsert([
                            'substation_id' => $substation->id,
                            'panel' => $panel,
                            'compartment' => $compartment,
                        ], [
                            'substation_id' => $substation->id,
                            'panel_name' => $panel,
                            'panel_code' => $panelCode,
                            'lineup' => '11kV Lineup',
                            'panel' => $panel,
                            'compartment' => $compartment,
                            'compartment_code' => $compCode,
                            'switchgear_code' => $switchgearCode,
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        echo "✅ Switchgears updated with switchgear_code (e.g., F1_CBT), no duplication created.\n";
    }
}
