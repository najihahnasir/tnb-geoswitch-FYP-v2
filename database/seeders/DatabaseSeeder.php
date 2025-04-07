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
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'userCategory' => 'Admin'
        // ]);

        // User::factory()->create([
        //     'name' => 'iRMC Staff User',
        //     'email' => 'irmcstaff@example.com',
        //     'userCategory' => 'iRMC Staff'
        // ]);
        
        Substation::factory()->count(10)->create();
    }
}
