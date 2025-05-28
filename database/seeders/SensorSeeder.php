<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sensor;
use App\Models\Switchgear;

class SensorSeeder extends Seeder
{
    public function run(): void
    {
        $switchgears = Switchgear::all();
        $count = 0;

        foreach ($switchgears as $switchgear) {
            $id = $switchgear->id;
            $compartment = $switchgear->compartment; // ✅ Use as-is

            // Thermal Sensor Logic
            if (in_array($compartment, ['Main Busbar', 'Reserve Busbar', 'CB - TOP', 'CB - BOTTOM'])) {
                Sensor::updateOrInsert([
                    'switchgear_id' => $id,
                    'type' => 'Thermal'
                ], [
                    'switchgear_id' => $id,
                    'type' => 'Thermal',
                    'tag' => 'TH-SW' . $id,
                    'status' => 'Online',
                    'status_changed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }

            // Partial Discharge Sensor Logic
            if (in_array($compartment, ['Main Busbar', 'Reserve Busbar', 'Cable Compartment'])) {
                Sensor::updateOrInsert([
                    'switchgear_id' => $id,
                    'type' => 'Partial Discharge'
                ], [
                    'switchgear_id' => $id,
                    'type' => 'Partial Discharge',
                    'tag' => 'PD-SW' . $id,
                    'status' => 'Online',
                    'status_changed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }

        echo "✅ Seeded $count sensors based on switchgear layout (capitalized & clean).\n";
    }
}
