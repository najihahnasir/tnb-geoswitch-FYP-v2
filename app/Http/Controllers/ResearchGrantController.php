<?php

namespace App\Http\Controllers;

use App\Models\ResearchGrant;
use App\Models\Academician;
use App\Models\AcademicianGrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB; 
use Symfony\Component\HttpFoundation\AcceptHeaderItem;

class ResearchGrantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Ensures authentication
    }
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $user = auth()->user();

    //     // Fetch research grants based on the user role
    //     if ($user->userCategory === 'Admin' || $user->userCategory === 'iRMC Staff') {
    //         // Admin and iRMC Staff see all grants
    //         $researchGrants = ResearchGrant::with('leader')->paginate(10);
    //         return view('researchgrants.index', compact('researchGrants'));
    //     }

    //     // For leaders and members
    //     $academician = $user->academician;

    //     $leaderProjects = ResearchGrant::whereHas('academiciansGrant', function ($query) use ($academician) {
    //         $query->where('academician_id', $academician->id)->where('role', 'leader');
    //     })->get();

    //     $memberProjects = ResearchGrant::whereHas('academiciansGrant', function ($query) use ($academician) {
    //         $query->where('academician_id', $academician->id)->where('role', 'member');
    //     })->get();

    //     return view('researchgrants.index', compact('leaderProjects', 'memberProjects'));
    // }
    public function index()
{
    $user = auth()->user();
    $academician = $user->academician;

    // For Admin and iRMC Staff
    if ($user->userCategory === 'Admin' || $user->userCategory === 'iRMC Staff') {
        $researchGrants = ResearchGrant::with('leader')->paginate(10);
        return view('researchgrants.index', compact('researchGrants'));
    }

    // For Leaders
    if ($user->isLeader()) {
        $leaderProjects = ResearchGrant::whereHas('academiciansGrant', function ($query) use ($academician) {
            $query->where('academician_id', $academician->id)->where('role', 'leader');
        })->get();

        $memberProjects = ResearchGrant::whereHas('academiciansGrant', function ($query) use ($academician) {
            $query->where('academician_id', $academician->id)->where('role', 'member');
        })->get();

        return view('researchgrants.index', compact('leaderProjects', 'memberProjects'));
    }

    // For Members
    if ($user->userCategory === 'Member') {
        $memberProjects = ResearchGrant::whereHas('academiciansGrant', function ($query) use ($academician) {
            $query->where('academician_id', $academician->id)->where('role', 'member');
        })->get();

        return view('researchgrants.index', compact('memberProjects'));
    }

    return abort(403, 'Unauthorized action.');
}


    public function create()
    {
        $this->authorize('admin-only'); // Only Admin can create grants
        // Fetch all academicians for the project leader and members
        $academicians = Academician::all();

        // Fetch all research grants to display in the form
        $researchGrants = ResearchGrant::all();

        // Return the view to create a new research grant, passing both academicians and research grants
        return view('researchgrants.create', compact('academicians', 'researchGrants'));
    }

    public function store(Request $request)
    {
        // Allow both Admin and iRMC Staff to store grants
        $user = auth()->user();
        if (!($user->userCategory === 'Admin' || $user->userCategory === 'iRMC Staff')) {
            abort(403, 'You are not authorized to store research grants.');
        }
        // Validate input data
        $validated = $request->validate([
            'project_title' => 'required|string|max:255',
            'grant_amount' => 'required|numeric',
            'grant_provider' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration_month' => 'required|integer|min:1',
            'members' => 'required|array|min:1', // Members array validation
            'members.*' => 'exists:academicians,id', // Ensure each selected member exists
            'leader_id' => [
                'required',
                'exists:academicians,id',
                function ($attribute, $value, $fail) use ($request) {
                    // Ensure the leader is also part of the members array
                    if (!in_array($value, $request->members)) {
                        $fail('The leader must also be a member of the research grant.');
                    }
                },
            ],
        ]);

        // Use a transaction to ensure atomicity
        DB::transaction(function () use ($validated) {
            // Create the research grant
            $researchGrant = ResearchGrant::create([
                'project_title' => $validated['project_title'],
                'grant_amount' => $validated['grant_amount'],
                'grant_provider' => $validated['grant_provider'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'duration_month' => $validated['duration_month'],
                'leader_id' => $validated['leader_id'],
            ]);

            // Attach all members to the research grant
            $members = collect($validated['members'])->mapWithKeys(function ($id) use ($validated) {
                return [$id => ['role' => ($id == $validated['leader_id']) ? 'leader' : 'member']];
            });

            $researchGrant->academicians()->sync($members);
        });

        // Redirect back with a success message
        return redirect()->route('researchgrants.index')->with('success', 'Research Grant created successfully.');
    }

    public function show($id)
    {
        $researchGrant = ResearchGrant::with(['academicians', 'milestones'])->findOrFail($id);
        
        // Retrieve the currently authenticated user
        $user = auth()->user();
        if ($user->userCategory == 'Member' && !AcademicianGrant::where('academician_id', $user->academician->id)->where('research_grant_id', $id)->exists()) {
            abort(403, 'You are not authorized to view this research grant.');
        }            
        return view('researchgrants.show', compact('researchGrant'));
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Allow both Admin and iRMC Staff to edit grants
        $user = auth()->user();
        if (!($user->userCategory === 'Admin' || $user->userCategory === 'iRMC Staff')) {
            abort(403, 'You are not authorized to edit this research grant.');
        }        
        // Find the ResearchGrant by ID, abort if not found
        $researchGrant = ResearchGrant::findOrFail($id);

        // Fetch all academicians for dropdowns
        $academicians = Academician::all();

        return view('researchgrants.edit', compact('researchGrant', 'academicians'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Allow both Admin and iRMC Staff to update grants
        $user = auth()->user();
        if (!($user->userCategory === 'Admin' || $user->userCategory === 'iRMC Staff')) {
            abort(403, 'You are not authorized to update this research grant.');
        }
        // Fetch the ResearchGrant by ID
        $researchGrant = ResearchGrant::findOrFail($id);

        // Validate input data
        $validated = $request->validate([
            'project_title' => 'required|string|max:255',
            'grant_amount' => 'required|numeric',
            'grant_provider' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration_month' => 'required|integer|min:1',
            'members' => 'required|array|min:1',
            'members.*' => 'exists:academicians,id',
            'leader_id' => [
                'required',
                'exists:academicians,id',
                function ($attribute, $value, $fail) use ($request) {
                    if (!in_array($value, $request->members)) {
                        $fail('The leader must also be a member of the research grant.');
                    }
                },
            ],
        ]);

        // Use a transaction to ensure atomicity
        DB::transaction(function () use ($validated, $researchGrant) {
            // Update the research grant
            $researchGrant->update([
                'project_title' => $validated['project_title'],
                'grant_amount' => $validated['grant_amount'],
                'grant_provider' => $validated['grant_provider'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'duration_month' => $validated['duration_month'],
                'leader_id' => $validated['leader_id'],
            ]);

            // Sync the selected members with the pivot table
            $researchGrant->academicians()->sync($validated['members']);

            // Attach the leader role to the pivot table
            $researchGrant->academicians()->attach($validated['leader_id'], ['role' => 'leader']);
        });

        // Redirect back with a success message
        return redirect()->route('researchgrants.index')->with('success', 'Research Grant updated successfully.');
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('admin-only'); // Only Admin can delete grants
        // Manually fetch the model
        $researchGrant = ResearchGrant::findOrFail($id); // Abort if not found

        // Delete related data
        $researchGrant->milestones()->delete(); // Assuming relationships exist
        $researchGrant->academicians()->detach();

        // Delete the research grant itself
        $researchGrant->delete();

        return redirect()->route('researchgrants.index')->with('success', 'Research Grant deleted successfully');
    } 
}
