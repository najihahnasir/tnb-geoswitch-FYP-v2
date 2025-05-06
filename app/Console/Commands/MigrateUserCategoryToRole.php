<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;


class MigrateUserCategoryToRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-user-category-to-role';
    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert userCategory column into standardized role column';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            $category = strtolower(trim($user->userCategory));

            switch ($category) {
                case 'admin':
                case 'administrator':
                    $user->role = 'admin';
                    break;

                case 'member':
                case 'staff':
                case 'irmc staff':
                case 'maintenance':
                default:
                    $user->role = 'maintenance';
                    break;
            }

            $user->save();
            $count++;
        }

        $this->info("Successfully updated roles for {$count} users.");
    }
}
