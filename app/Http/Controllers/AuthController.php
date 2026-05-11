<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = [
            'name' => $request->username,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json(['success' => true, 'redirect' => '/planner']);
        }

        return response()->json(['success' => false], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['success' => true, 'redirect' => '/login']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,name|min:3|max:30',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name'     => $request->username,
            'email'    => $request->username . '@planner.local',
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json(['success' => true, 'redirect' => '/planner']);
    }
}
