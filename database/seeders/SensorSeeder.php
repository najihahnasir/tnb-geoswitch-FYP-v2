<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Sensor;
use App\Models\Switchgear;

class SensorSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Step 1: Truncate the sensor table
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Sensor::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $switchgears = Switchgear::with('substation')->get();
        $createdAt = '2024-12-01 00:00:00';
        $counter = 1;

        // ✅ Substation code map
        $substationCodes = [
            'PPU KOMPLEKS SUKAN'     => 'KSKN',
            'SSU Matrade'            => 'MTRD',
            'PPU Metroplex'          => 'MTPX',
            'PPU TASIK TAMBAHAN'     => 'TBHN',
            'PPU Wangsa Maju'        => 'WSMJ',
            'PPU Alam Damai'         => 'ALDM',
            'PPU HUKM'               => 'HUKM',
            'PPU Yow Chuan'          => 'YWCH',
            'PMU KLCC'               => 'KLCC',
            'PMU Bandar Tun Razak'   => 'BTRZ',
        ];

        // ✅ Compartment code map
        $compCode = [
            'Main Busbar'        => 'MBSB',
            'Reserve Busbar'     => 'RBSB',
            'CB - TOP'           => 'CBT',
            'CB - BOTTOM'        => 'CBB',
            'Cable Compartment'  => 'CBL',
        ];

        // ✅ Group by substation and panel to seed only once per panel
        $grouped = $switchgears->groupBy(function ($item) {
            return $item->substation_id . '|' . $item->panel;
        });

        $total = 0;

        foreach ($grouped as $groupKey => $compartments) {
            $first = $compartments->first();
            $substationName = $first->substation->substation_name;
            $substationCode = $substationCodes[$substationName] ?? 'XXXX';
            $panelCode = $first->panel_code;

            // ✅ PD sensors at 3 compartments
            $pdCompartments = ['Main Busbar', 'CB - TOP', 'Cable Compartment'];
            foreach ($pdCompartments as $compartment) {
                $code = $compCode[$compartment] ?? 'UNK';
                $tag = "{$substationCode}_PD_{$panelCode}_{$code}_" . str_pad($counter, 3, '0', STR_PAD_LEFT);

                Sensor::create([
                    'switchgear_id' => $first->id,
                    'type' => 'Partial Discharge',
                    'tag' => $tag,
                    'status' => 'Online',
                    'status_changed_at' => $createdAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                $counter++;
                $total++;
            }

            // ✅ Thermal sensor layout: total 12 per panel
            $thermalLayout = [
                'Main Busbar'        => 2,
                'CB - TOP'           => 3,
                'CB - BOTTOM'        => 3,
                'Cable Compartment'  => 4,
            ];

            foreach ($thermalLayout as $compartment => $count) {
                $code = $compCode[$compartment] ?? 'UNK';

                for ($i = 1; $i <= $count; $i++) {
                    $tag = "{$substationCode}_TH_{$panelCode}_{$code}_" . str_pad($counter, 3, '0', STR_PAD_LEFT);

                    Sensor::create([
                        'switchgear_id' => $first->id,
                        'type' => 'Thermal',
                        'tag' => $tag,
                        'status' => 'Online',
                        'status_changed_at' => $createdAt,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    $counter++;
                    $total++;
                }
            }
        }

        echo "✅ Seeded $total sensors using 15-sensor per panel layout.\n";
    }
}
