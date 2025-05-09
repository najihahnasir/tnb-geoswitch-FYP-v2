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
            ],
            // New substations
            [
                'substation_name' => 'PPU Wangsa Maju',
                'substation_location' => 'Wangsa Maju',
                'code' => 'TEMP_' . uniqid(),
                'latitude' => 3.192788,
                'longitude' => 101.741585,
                'description' => 'Main substation for Wangsa Maju',
                'status' => 'active',
                'type' => 'Main',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_name' => 'PPU Alam Damai',
                'substation_location' => 'Alam Damai',
                'code' => 'TEMP_' . uniqid(),
                'latitude' => 3.060493,
                'longitude' => 101.739004,
                'description' => 'Main substation for Alam Damai',
                'status' => 'active',
                'type' => 'Main',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_name' => 'PPU HUKM',
                'substation_location' => 'HUKM',
                'code' => 'TEMP_' . uniqid(),
                'latitude' => 3.099545,
                'longitude' => 101.725374,
                'description' => 'Main substation for HUKM',
                'status' => 'active',
                'type' => 'Main',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_name' => 'PPU Yow Chuan',
                'substation_location' => 'Yow Chuan',
                'code' => 'TEMP_' . uniqid(),
                'latitude' => 3.150726,
                'longitude' => 101.735201,
                'description' => 'Main substation for Yow Chuan',
                'status' => 'active',
                'type' => 'Main',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_name' => 'PMU KLCC',
                'substation_location' => 'KLCC',
                'code' => 'TEMP_' . uniqid(),
                'latitude' => 3.158181,
                'longitude' => 101.716991,
                'description' => 'Main substation for KLCC',
                'status' => 'active',
                'type' => 'Main',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'substation_name' => 'PMU Bandar Tun Razak',
                'substation_location' => 'Bandar Tun Razak',
                'code' => 'TEMP_' . uniqid(),
                'latitude' => 3.099675,
                'longitude' => 101.708651,
                'description' => 'Main substation for Bandar Tun Razak',
                'status' => 'active',
                'type' => 'Main',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
        
        foreach ($substations as $substation) {
            $exists = Substation::where('substation_name', $substation['substation_name'])->exists();
            
            if (!$exists) {
                Substation::updateOrInsert(
                    ['code' => $substation['code']],
                    $substation
                );
            }
        }
    }
}
