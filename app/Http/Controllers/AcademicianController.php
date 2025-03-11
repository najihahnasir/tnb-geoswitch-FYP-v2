<?php

namespace App\Http\Controllers;

use App\Models\Academician;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AcademicianController extends Controller
{
    public function __construct()
    {
        // $this->middleware('can:admin-only'); // Apply Gate for Admin role
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all academicians from the database
        $academicians = Academician::paginate(10);

        // Return the view to display the list of academicians
        return view('academicians.index', compact('academicians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Return the view to create a new academician
        return view('academicians.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'staff_number' => 'required|string|unique:academicians,staff_number|max:255',
            'email' => 'required|email|unique:academicians,email',
            'college' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|in:Professor,Assoc Prof,Senior Lecturer,Lecturer',
        ]);

        $user = User::create([ 
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make('password')
        ]);
        // Academician::create($request->all());
        Academician::create([
            'user_id' => $user->id, // Assign the created user's ID
            'name' => $request['name'], // Adjust as needed
            'email' => $request['email'], // Adjust as needed
            'staff_number' => $request['staff_number'], // Adjust as needed
            'college' => $request['college'], // Adjust as needed
            'department' => $request['department'], // Adjust as needed
            'position' => $request['position'], // Adjust as needed
        ]);


        // Redirect back to the list of academicians with a success message
        return redirect()->route('academicians.index')->with('success', 'Academician created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Academician $academician)
    {
        // Return the view to show the details of a specific academician
        return view('academicians.show', compact('academician'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Academician $academician)
    {
        // Return the view to edit an academician's details
        return view('academicians.edit', compact('academician'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Academician $academician)
    {
        // Validate the incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'staff_number' => 'required|string|unique:academicians,staff_number,' . $academician->id . '|max:255',
            'email' => 'required|email|unique:academicians,email,' . $academician->id,
            'college' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'required|in:Professor,Assoc Prof,Senior Lecturer,Lecturer',
        ]);

        $academician->user->update([
            'name' => $request['name'],
            'email' => $request['email'],
        ]);
    
        // Update the academician record with the new data
        $academician->update($request->all());

        // Redirect back to the list of academicians with a success message
        return redirect()->route('academicians.index')->with('success', 'Academician updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Academician $academician)
    // {
    //     // Delete the related user
    //     if ($academician->user) {
    //         $academician->user->delete();
    //     }
    //     try {
    //         // Delete the related user
    //         if ($academician->user) {
    //             $academician->user->delete();
    //         }
    
    //         // Delete the academician record
    //         $academician->delete();
    
    //         // Redirect back with a success message
    //         return redirect()->route('academicians.index')->with('success', 'Academician deleted successfully.');
    //     } catch (\Exception $e) {
    //         // Log the error for debugging (optional)
    //         \Log::error('Failed to delete academician: ' . $e->getMessage());
    
    //         // Redirect back with an error message
    //         return redirect()->route('academicians.index')->with('error', 'Failed to delete academician. Please try again.');
    //     }
    // }
}
