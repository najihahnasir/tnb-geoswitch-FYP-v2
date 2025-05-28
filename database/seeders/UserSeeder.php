<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table with default admin and maintenance accounts.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@geoswitch.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        User::updateOrCreate(
            ['email' => 'maint@geoswitch.com'],
            [
                'name' => 'Maintenance Team',
                'password' => bcrypt('password123'),
                'role' => 'maintenance',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        echo "✅ Default users created: Admin and Maintenance\n";
    }
}
