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
                'code' => 'PPU-KS',
                'latitude' => 3.051774932,
                'longitude' => 101.6957484,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_name' => 'SSU Matrade',
                'substation_location' => 'Matrade',
                'code' => 'SSU-MTR',
                'latitude' => 3.17794505,
                'longitude' => 101.6681519,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_name' => 'PPU Metroplex',
                'substation_location' => 'Metroplex',
                'code' => 'PPU-MPX',
                'latitude' => 3.166338296,
                'longitude' => 101.6918621,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];
    
        Substation::insert($substations);
    }
}
