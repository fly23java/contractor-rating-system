<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'supervisor') abort(403);
        $users = User::orderBy('created_at', 'desc')->get();
        return view('supervisor.users.index', compact('users'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'supervisor') abort(403);
        return view('supervisor.users.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'supervisor') abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:contractor,owner,supervisor',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }
}
