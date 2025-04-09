<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Switchgear;

class SwitchgearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $switchgears = [
            [
                'substation_id' => 4, 
                'name' => 'Feeder 4P5',
                'lineup' => '33kV Lineup',
                'panel' => 'Feeder 4P5',
                'compartment' => 'CB - Top',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_id' => 4,
                'name' => 'Feeder 2P5',
                'lineup' => '33kV Lineup',
                'panel' => 'Feeder 2P5',
                'compartment' => 'Main busbar',
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
