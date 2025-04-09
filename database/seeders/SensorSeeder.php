<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sensor;

class SensorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sensors = [
            [
                'switchgear_id' => 1,
                'type' => 'Partial Discharge',
                'tag' => 'S01L02P06ERPDCP',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'switchgear_id' => 2,
                'type' => 'Partial Discharge',
                'tag' => 'S01L02P04MBUPDCP',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($sensors as $sensor) {
            Sensor::updateOrInsert(
                ['tag' => $sensor['tag']],
                $sensor
            );
        }
    }
}