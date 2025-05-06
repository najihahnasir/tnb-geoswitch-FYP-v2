<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        
        // Set the global date format
        Carbon::setToStringFormat('Y-m-d');  // Choose your preferred date format

        // // Define Gates
        // Gate::define('admin-only', function ($user) {
        //     return $user->userCategory === 'Admin';
        // });

        // Gate::define('leader-only', function ($user, $researchGrant = null) {
        //     if ($researchGrant) {
        //         return $user->academician && $user->academician->id === $researchGrant->leader_id;
        //     }
        //     return false;
        // });
        
        // // iRMC Staff Gate
        // Gate::define('irmc-staff-only', function ($user) {
        //     return $user->userCategory === 'iRMC Staff';
        // });

        // // Allow viewing details of a research grant or milestone (Admins and the specific Leader)
        // Gate::define('view-research-grant', function ($user, $researchGrant) {
        //     return $user->userCategory === 'Admin' ||
        //         ($user->userCategory === 'Leader' && $researchGrant->leader_id === $user->id);
        // });

        // // Allow updating milestones (Admins and the specific Leader)
        // Gate::define('update-milestone', function ($user, $researchGrant) {
        //     return $user->userCategory === 'Admin' ||
        //         ($user->userCategory === 'Leader' && $researchGrant->leader_id === $user->id);
        // });

        // Gate::define('member-only', function ($user, $researchGrant) {
        //     return $researchGrant->members->contains($user->academician->id);
        // });
        

        
    }
}
