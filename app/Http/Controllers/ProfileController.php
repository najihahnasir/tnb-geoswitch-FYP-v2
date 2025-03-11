<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        // Default empty arrays for notifications and messages
        $notifications = [
            ['title' => 'New Grant Assigned', 'message' => 'You were assigned to a new grant.', 'time' => '2 hrs ago'],
        ];

        $messages = [
            ['sender' => 'Maria Hudson', 'content' => 'Can we discuss tomorrow?', 'avatar' => asset('assets/img/user.jpg'), 'time' => '4 hrs ago'],
        ];
        return view('profile', compact('user', 'notifications', 'messages'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->route('profile.index')->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile.index')->with('success', 'Password changed successfully.');
    }
}
