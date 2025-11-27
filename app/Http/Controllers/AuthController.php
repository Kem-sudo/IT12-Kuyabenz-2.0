<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Ensure admin user exists
        User::getAdminUser();
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Find user by username
        $user = User::where('username', $credentials['username'])->first();

        // Check if user exists and password matches
        if ($user && $credentials['password'] === $user->password) {
            Auth::login($user);
            return $this->redirectToDashboard($user->role);
        }

        return back()->withErrors(['login' => 'Invalid username or password']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,cashier,kitchen',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => $request->password,
            'role' => $request->role,
        ]);

        Auth::login($user);
        return $this->redirectToDashboard($user->role);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    private function redirectToDashboard($role)
    {
        switch ($role) {
            case 'admin':
                return redirect('/admin/dashboard');
            case 'cashier':
                return redirect('/cashier');
            case 'kitchen':
                return redirect('/kitchen');
            default:
                return redirect('/');
        }
    }
}