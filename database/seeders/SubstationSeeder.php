<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Substation;

class SubstationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $substations = [
            [
                'substation_name' => 'PPU KOMPLEKS SUKAN',
                'substation_location' => 'Kompleks Sukan',
                'code' => 'TEMP_' . uniqid(),
                'latitude' => 3.051774932,
                'longitude' => 101.6957484,
                'description' => 'Main substation for Kompleks Sukan',
                'status' => 'active',
                'type' => 'Main',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_name' => 'SSU Matrade',
                'substation_location' => 'Matrade',
                'code' => 'TEMP_' . uniqid(),
                'latitude' => 3.17794505,
                'longitude' => 101.6681519,
                'description' => 'Main substation for Matrade',
                'status' => 'active',
                'type' => 'Main',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_name' => 'PPU Metroplex',
                'substation_location' => 'Metroplex',
                'code' => 'TEMP_' . uniqid(),
                'latitude' => 3.166338296,
                'longitude' => 101.6918621,
                'description' => 'Main substation for Metroplex',
                'status' => 'active',
                'type' => 'Main',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_name' => 'PPU TASIK TAMBAHAN',
                'substation_location' => 'Tasik Tambahan',
                'code' => 'TEMP_' . uniqid(),
                'latitude' => 3.123456,
                'longitude' => 101.6789,
                'description' => 'Main substation for Tasik Tambahan',
                'status' => 'active',
                'type' => 'Main',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        // Substation::insert($substations);
        foreach ($substations as $sub) {
            Substation::updateOrInsert(
                ['code' => $sub['code']],
                $sub
            );
        }        
    }
}
