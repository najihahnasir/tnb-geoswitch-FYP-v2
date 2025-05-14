<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Substation;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SubstationSeeder::class,
            SwitchgearSeeder::class,
            SensorSeeder::class,
            SensorMeasurementSeeder::class,
            SensorStatusHistorySeeder::class
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@geoswitch.com',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);
        
        User::create([
            'name' => 'Maintenance Team',
            'email' => 'maint@geoswitch.com',
            'password' => bcrypt('password123'),
            'role' => 'maintenance'
        ]);
       
    }
}
