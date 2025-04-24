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
            ],
            [
                'sensor_id' => 3,
                'value' => 30,
                'unit' => 'dB',
                'threshold' => '>= 50 for 300s',
                'status' => 'NORMAL',
                'severity' => 'WARN',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'sensor_id' => 4,
                'value' => 45,
                'unit' => 'dB',
                'threshold' => '>= 60 for 300s',
                'status' => 'AWAIT',
                'severity' => 'PRESENT',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($measurements as $data) {
            SensorMeasurement::updateOrInsert(
                [
                    // Match condition (consider what defines uniqueness in your data)
                    'sensor_id' => $data['sensor_id'],
                    'value' => $data['value'],
                ],
                array_merge($data, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
