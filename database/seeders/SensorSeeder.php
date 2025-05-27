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
            $compartment = strtolower($switchgear->compartment);

            // Thermal Sensor Logic
            if (in_array($compartment, ['main busbar', 'reserve busbar', 'cb - top', 'cb - bottom'])) {
                Sensor::updateOrInsert([
                    'switchgear_id' => $id,
                    'type' => 'thermal'
                ], [
                    'switchgear_id' => $id,
                    'type' => 'thermal',
                    'tag' => 'TH-SW' . $id,
                    'status' => 'online',
                    'status_changed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }

            // Partial Discharge Sensor Logic
            if (in_array($compartment, ['main busbar', 'reserve busbar', 'cable compartment'])) {
                Sensor::updateOrInsert([
                    'switchgear_id' => $id,
                    'type' => 'partial_discharge'
                ], [
                    'switchgear_id' => $id,
                    'type' => 'partial_discharge',
                    'tag' => 'PD-SW' . $id,
                    'status' => 'online',
                    'status_changed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }

        echo "✅ Seeded $count sensors based on switchgear layout.\n";
    }
}
