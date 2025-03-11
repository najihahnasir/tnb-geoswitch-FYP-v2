<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Academician;
use App\Models\ResearchGrant;
use App\Models\Milestone;

class DashboardController extends Controller
{
    
    public function dashboard()
    {
        $user = auth()->user();
        // dd('Controller Reached');
        // dd($user); // Check if the user is authenticated
        if (!$user) {
            return redirect()->route('login'); // Redirect to login if not authenticated
        }

        $leaderProjects = collect();
        $memberProjects = collect();

        $totalAcademicians = Academician::count();
        $totalGrants = ResearchGrant::count();
        $completedMilestones = Milestone::where('status', 'Completed')->count();
        $activeProjects = ResearchGrant::where('end_date', '>=', now())->count();

        $recentGrants = ResearchGrant::latest()->take(5)->get();
        $activeMilestones = Milestone::where('status', 'In Progress')->latest()->take(5)->get();

        // $totalGrantAmount = ResearchGrant::count();
        $totalGrantAmount = ResearchGrant::sum('grant_amount');



        // Role-based logic
        if ($user->userCategory === 'Admin') {
            // Admin: View all projects
            $leaderProjects = ResearchGrant::all();
        } else {
            // Non-admin: Get academician details
            $academician = $user->academician;
            if ($academician) {
                // Fetch projects the user leads
                $leaderProjects = ResearchGrant::whereHas('academiciansGrant', function ($query) use ($academician) {
                    $query->where('academician_id', $academician->id)->where('role', 'leader');
                })->get();
        
                // Fetch projects the user participates in as a member
                $memberProjects = ResearchGrant::whereHas('academiciansGrant', function ($query) use ($academician) {
                    $query->where('academician_id', $academician->id)->where('role', 'member');
                })->get();
            }
        }
         // Debug the data being fetched
    // dd($leaderProjects, $memberProjects);

        // Remove duplicate projects if the user is both a leader and a member
        // $allProjects = $leaderProjects->merge($memberProjects)->unique('id');
        
        return view('dashboard', compact(
            'totalAcademicians',
            'totalGrants',
            'completedMilestones',
            'activeProjects',
            'recentGrants',
            'activeMilestones',
            'leaderProjects', 
            'memberProjects',
            'totalGrantAmount'
            // 'allProjects'
        ));
    }

}
