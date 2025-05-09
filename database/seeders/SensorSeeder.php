<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sensor;

class SensorSeeder extends Seeder
{
    public function run(): void
    {
        // Existing sensors to keep
        $existingSensors = [
            'S01L02P06ERPDCP',
            'S01L02P04MBUPDCP',
            'S01L02P06ERPLLL',
            'S01L02P04MBUPPRP',
        ];

        // Add 10 new randomized sensors (non-duplicate, switchgear 9-14)
        $statuses = ['online', 'offline', 'repairing'];
        $types = ['Partial Discharge', 'Thermal'];
        $switchgearIds = range(9, 14);

        $newSensors = [];

        for ($i = 1; $i <= 10; $i++) {
            $switchgearId = $switchgearIds[array_rand($switchgearIds)];
            $type = $types[array_rand($types)];
            $status = $statuses[array_rand($statuses)];
            $tag = 'SEN' . str_pad($i, 3, '0', STR_PAD_LEFT); // e.g., SEN001, SEN002...

            if (!in_array($tag, $existingSensors)) {
                $newSensors[] = [
                    'switchgear_id' => $switchgearId,
                    'type' => $type,
                    'tag' => $tag,
                    'status' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert original 4 sensors (linked to switchgear_id 1 & 2)
        $existing = [
            [
                'switchgear_id' => 1,
                'type' => 'Partial Discharge',
                'tag' => 'S01L02P06ERPDCP',
                'status' => 'offline',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'switchgear_id' => 1,
                'type' => 'Thermal',
                'tag' => 'S01L02P04MBUPDCP',
                'status' => 'online',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'switchgear_id' => 2,
                'type' => 'Partial Discharge',
                'tag' => 'S01L02P06ERPLLL',
                'status' => 'online',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'switchgear_id' => 2,
                'type' => 'Thermal',
                'tag' => 'S01L02P04MBUPPRP',
                'status' => 'online',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Seed both existing and new sensors
        foreach (array_merge($existing, $newSensors) as $sensor) {
            Sensor::updateOrInsert(['tag' => $sensor['tag']], $sensor);
        }
    }
}
