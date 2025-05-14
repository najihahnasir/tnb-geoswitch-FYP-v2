<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SensorStatusHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sensorIds = DB::table('sensors')->pluck('id')->toArray();

        // ✅ Get only admin and maintenance user IDs
        $userIds = DB::table('users')
            ->whereIn('role', ['admin', 'maintenance'])
            ->pluck('id')
            ->toArray();

        $statuses = ['online', 'offline', 'repairing'];

        for ($i = 0; $i < 30; $i++) {
            $sensorId = collect($sensorIds)->random();
            $userId = collect($userIds)->random();
            $oldStatus = collect($statuses)->random();
            
            // Ensure new_status is different from old_status
            do {
                $newStatus = collect($statuses)->random();
            } while ($newStatus === $oldStatus);

            $start = Carbon::now()->subDays(rand(10, 60))->setTime(rand(0, 23), rand(0, 59), 0);
            $resolved = (clone $start)->addHours(rand(2, 72));

            DB::table('sensor_status_histories')->insert([
                'sensor_id'   => $sensorId,
                'user_id'     => $userId,
                'old_status'  => $oldStatus,
                'new_status'  => $newStatus,
                'created_at'  => $start,
                'updated_at'  => $start,
                'resolved_at' => $resolved,
            ]);
        }
    }
}