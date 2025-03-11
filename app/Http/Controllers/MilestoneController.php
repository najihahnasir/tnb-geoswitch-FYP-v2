<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\ResearchGrant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MilestoneController extends Controller
{
    /**
     * Display a listing of the milestones for a specific research grant.
     */
    public function index($researchGrantId)
    {
        $researchGrant = ResearchGrant::findOrFail($researchGrantId);
        $milestones = $researchGrant->milestones;
        // dd(compact('milestones', 'researchGrant')); // Debug the data being passed
        // dd($milestones);
        return view('milestones.index', compact('milestones', 'researchGrant'));
    }
    /**
     * Show the form for creating a new milestone.
     */
    public function create($researchGrantId)
    { 
        $researchGrant = ResearchGrant::findOrFail($researchGrantId);

        // Check if the authenticated user is the leader
        $user = auth()->user();
        if (empty($user->academician) || $user->academician->id != $researchGrant->leader_id) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to create milestones for this grant.');
        }
        return view('milestones.create', compact('researchGrant'));
    }

    /**
     * Store a newly created milestone in storage.
     */
    public function store(Request $request)
    {
        // Ensure only the leader of the research grant can save a milestone
        $researchGrant = ResearchGrant::findOrFail($request->input('grant_id'));
        $user = auth()->user();

        if (empty($user->academician) || $user->academician->id != $researchGrant->leader_id) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to save milestones for this grant.');
        }
        // $this->authorize('leader-only'); // Leaders can store milestones for their projects
        $validated = $request->validate([
            'milestone_name' => 'required|string|max:255',
            'completion_date' => 'required|date',
            'deliverable' => 'required|string',
            'status' => 'required|in:Not Started,In Progress,Completed',
            'remark' => 'nullable|string|max:255',
            'grant_id' => 'required|exists:research_grants,id',
        ]);

        Milestone::create($validated);

        return redirect()
            ->route('researchgrants.milestones.index', $validated['grant_id'])
            ->with('success', 'Milestone created successfully.');
    }

    /**
     * Display the specified milestone.
     */
    public function show($researchGrantId, $milestoneId)
    {
        // Manually resolve the research grant
        $researchGrant = ResearchGrant::findOrFail($researchGrantId);
    
        // Manually resolve the milestone
        $milestone = Milestone::where('id', $milestoneId)
            ->where('grant_id', $researchGrant->id)
            ->firstOrFail();
    
        return view('milestones.show', compact('milestone', 'researchGrant'));
    }
    
    /**
     * Show the form for editing the specified milestone.
     */
    public function edit($researchGrantId, $milestoneId)
    {
        // Retrieve the research grant and milestone
        $researchGrant = ResearchGrant::findOrFail($researchGrantId);
        $milestone = Milestone::where('id', $milestoneId)
            ->where('grant_id', $researchGrant->id)
            ->firstOrFail();

        // Check if the authenticated user is the leader
        $user = auth()->user();
        if (empty($user->academician) || $user->academician->id != $researchGrant->leader_id) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to edit milestones for this grant.');
        }

        // Return the edit view
        return view('milestones.edit', compact('researchGrant', 'milestone'));
    }

    /**
     * Update the specified milestone in storage.
     */
    // public function update(Request $request, $researchGrantId, $milestoneId)
    // {
    //     // dd($request->all());
    //     // Retrieve the research grant and milestone
    //     $researchGrant = ResearchGrant::findOrFail($researchGrantId);
    //     $milestone = Milestone::where('id', $milestoneId)
    //         ->where('grant_id', $researchGrant->id)
    //         ->firstOrFail();
    
    //     // Debug the models and request
    //     // dd($researchGrant->toArray(), $milestone->toArray(), $request->all());
    
    //     // Authorization: Allow only the project leader or admin
    //     $user = auth()->user();
    //     if (empty($user->academician) || $user->academician->id != $researchGrant->leader_id) {
    //         return redirect()->route('dashboard')->with('error', 'You are not authorized to update milestones for this grant.');
    //     }
    
    //     // Validate the incoming data
    //     $request->validate([
    //         'milestone_name' => 'required|string|max:255',
    //         'completion_date' => 'required|date',
    //         'deliverable' => 'required|string',
    //         'status' => 'required|in:Not Started,In Progress,Completed',
    //         'remark' => 'nullable|string|max:255',
    //         'date_updated' => 'required|date',
    //     ]);
    
    //     // Update manually
    //     $milestone->milestone_name = $request->input('milestone_name');
    //     $milestone->completion_date = $request->input('completion_date');
    //     $milestone->deliverable = $request->input('deliverable');
    //     $milestone->status = $request->input('status');
    //     $milestone->remark = $request->input('remark');
    //     $milestone->date_updated = $request->input('date_updated');
    
    //     // Attempt to save and check for success
    //     if (!$milestone->save()) {
    //         return redirect()->back()->with('error', 'Failed to update milestone.');
    //     }
    
    //     // Redirect back with success message
    //     return redirect()
    //         ->route('researchgrants.milestones.index', $researchGrant->id)
    //         ->with('success', 'Milestone updated successfully');
    // }
    public function update(Request $request, $researchGrantId, $milestoneId)
    {
        $researchGrant = ResearchGrant::findOrFail($researchGrantId);
        $milestone = Milestone::where('id', $milestoneId)
            ->where('grant_id', $researchGrant->id)
            ->firstOrFail();
    
        // Authorization check
        $user = auth()->user();
        if (empty($user->academician) || $user->academician->id != $researchGrant->leader_id) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to update milestones for this grant.');
        }
    
        // Validate request
        $request->validate([
            'milestone_name' => 'required|string|max:255',
            'completion_date' => 'required|date',
            'deliverable' => 'required|string',
            'status' => 'required|in:Not Started,In Progress,Completed',
            'remark' => 'nullable|string|max:255',
            'date_updated' => 'required|date',
        ]);
    
        // Update milestone fields
        $milestone->milestone_name = $request->input('milestone_name');
        $milestone->completion_date = Carbon::createFromFormat('Y-m-d', $request->input('completion_date'))->format('Y-m-d');
        $milestone->deliverable = $request->input('deliverable');
        $milestone->status = $request->input('status');
        $milestone->remark = $request->input('remark');
        $milestone->date_updated = Carbon::now();
    
        // Save and debug
        try {
            if (!$milestone->save()) {
                dd('Save returned false. Possible issue with database constraints.');
            }
        } catch (\Exception $e) {
            dd('Save failed: ' . $e->getMessage());
        }
    
        // Debug redirect
        dd('Redirecting to: ', route('researchgrants.milestones.index', $researchGrant->id));
    
        return redirect()
            ->route('researchgrants.milestones.index', $researchGrant->id)
            ->with('success', 'Milestone updated successfully');
    }
    
    
    /**
     * Remove the specified milestone from storage.
     */
    public function destroy(ResearchGrant $researchGrant, Milestone $milestone)
{
    // Ensure the milestone belongs to the research grant
    if ($milestone->grant_id !== $researchGrant->id) {
        abort(404, 'Milestone not found for this research grant.');
    }

    // Authorization: Ensure the user has permission to delete
    $user = auth()->user();
    if (empty($user->academician) || $user->academician->id != $researchGrant->leader_id) {
        return redirect()->route('dashboard')->with('error', 'You are not authorized to delete this milestone.');
    }

    // Delete the milestone
    $milestone->delete();

    // Redirect with success message
    return redirect()->route('researchgrants.milestones.index', $researchGrant->id)
        ->with('success', 'Milestone deleted successfully.');
}

    public function updateStatus(Request $request, ResearchGrant $researchGrant, Milestone $milestone)
    {
        // Ensure the milestone belongs to the research grant
        if ($milestone->grant_id !== $researchGrant->id) {
            return response()->json(['success' => false, 'message' => 'Invalid milestone or research grant.'], 404);
        }

        // Validate the new status
        $validated = $request->validate([
            'status' => 'required|in:Not Started,In Progress,Completed',
        ]);

        // Update the status
        $milestone->status = $validated['status'];
        $milestone->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

}
