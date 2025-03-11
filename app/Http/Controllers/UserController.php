<?php

namespace App\Http\Controllers;

use App\Models\User; // Import the User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Ensure authentication is required for all actions
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of all users.
     */
    public function index()
    {
        $this->authorize('admin-only'); // Ensure only admins can view the user list
        $users = User::all(); // Retrieve all users
        return view('users.index', compact('users'));
    }

    /**
     * Show the form to create a new user.
     */
    public function create()
    {
        $this->authorize('admin-only'); // Ensure only admins can create a user
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('admin-only'); // Ensure only admins can store users

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'userCategory' => 'required|in:Admin,Leader,iRMC Staff',
            'userLevel' => 'required|integer|min:0|max:2',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Securely hash the password
            'userCategory' => $request->userCategory,
            'userLevel' => $request->userLevel,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $this->authorize('admin-only'); // Ensure only admins can edit users
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('admin-only'); // Ensure only admins can update users

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'userCategory' => 'required|in:Admin,Leader,iRMC Staff',
            'userLevel' => 'required|integer|min:0|max:2',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'userCategory' => $request->userCategory,
            'userLevel' => $request->userLevel,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('admin-only'); // Ensure only admins can delete users

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Show the profile of the logged-in user.
     */
    public function profile()
{
    $user = Auth::user(); // Retrieve the logged-in user using the Auth facade
    return view('users.profile', compact('user'));
}

public function updateProfile(Request $request)
{
    $user = Auth::user(); // Retrieve the logged-in user
    if (!$user) {
        abort(403, 'Unauthorized access');
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'phone' => 'nullable|string|max:15',
        'password' => 'nullable|string|min:8|confirmed',
    ]);

    $data = $request->only(['name', 'email', 'phone']);
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password); // Update password if provided
    }

    // Update the user's data
    $user->update($data);

    return redirect()->route('profile')->with('success', 'Profile updated successfully!');
}


}
