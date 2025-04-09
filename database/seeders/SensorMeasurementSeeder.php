<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SensorMeasurement;

class SensorMeasurementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $measurements = [
            [
                'sensor_id' => 1,
                'value' => 98,
                'unit' => 'dB',
                'threshold' => '>= 50 for 300s',
                'status' => 'ALARM',
                'severity' => 'WARN',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'sensor_id' => 2,
                'value' => 122,
                'unit' => 'dB',
                'threshold' => '>= 50 for 300s',
                'status' => 'ALARM',
                'severity' => 'WARN',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        SensorMeasurement::insert($measurements);
    }
}
