<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Switchgear;
use App\Models\Substation;

class SwitchgearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all substations to ensure we have correct IDs
        $substations = Substation::all()->keyBy('substation_name');
        
        $switchgears = [
            // // Existing switchgears (keep these)
            // [
            //     'substation_id' => $substations['PPU TASIK TAMBAHAN']->id ?? 4, 
            //     'name' => 'Feeder 4P5',
            //     'lineup' => '33kV Lineup',
            //     'panel' => 'Feeder 4P5',
            //     'compartment' => 'CB - Top',
            //     'created_at' => now(),
            //     'updated_at' => now()
            // ],
            // [
            //     'substation_id' => $substations['PPU TASIK TAMBAHAN']->id ?? 4,
            //     'name' => 'Feeder 2P5',
            //     'lineup' => '33kV Lineup',
            //     'panel' => 'Feeder 2P5',
            //     'compartment' => 'Main busbar',
            //     'created_at' => now(),
            //     'updated_at' => now()
            // ],
            // Add more existing ones if needed...

            // New switchgears for added substations
            [
                'substation_id' => $substations['PPU Wangsa Maju']->id, 
                'name' => 'Feeder 4P5',
                'lineup' => '33kV Lineup',
                'panel' => 'Feeder 4P5',
                'compartment' => 'CB - Top',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_id' => $substations['PPU Alam Damai']->id,
                'name' => 'Feeder 2P5',
                'lineup' => '33kV Lineup',
                'panel' => 'Feeder 2P5',
                'compartment' => 'Main busbar',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_id' => $substations['PPU HUKM']->id,
                'name' => 'Feeder 2P5',
                'lineup' => '33kV Lineup',
                'panel' => 'Feeder 2P5',
                'compartment' => 'Main busbar',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_id' => $substations['PPU Yow Chuan']->id, 
                'name' => 'Feeder 4P5',
                'lineup' => '33kV Lineup',
                'panel' => 'Feeder 4P5',
                'compartment' => 'CB - Top',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_id' => $substations['PMU KLCC']->id,
                'name' => 'Feeder 2P5',
                'lineup' => '33kV Lineup',
                'panel' => 'Feeder 2P5',
                'compartment' => 'Main busbar',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_id' => $substations['PMU Bandar Tun Razak']->id, 
                'name' => 'Feeder 4P5',
                'lineup' => '33kV Lineup',
                'panel' => 'Feeder 4P5',
                'compartment' => 'CB - Top',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($switchgears as $gear) {
            Switchgear::updateOrInsert(
                [
                    'substation_id' => $gear['substation_id'],
                    'name' => $gear['name']
                ],
                $gear
            );
        }
    }
}