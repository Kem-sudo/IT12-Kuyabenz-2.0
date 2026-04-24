<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\AuditLogger;

class AuthController extends Controller
{
    public function showLogin()
    {
        User::getAdminUser();

        return view('Auth.Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();

            AuditLogger::log('auth.login', [
                'username' => $user->username,
            ], $request, User::class, $user->id);

            return $this->redirectToDashboard($user->role);
        }

        AuditLogger::log('auth.login_failed', [
            'username' => $request->username,
        ], $request);

        return back()->withErrors([
            'login' => 'Invalid username or password'
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:15|confirmed',
            'role' => 'required|in:admin,cashier,kitchen',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectToDashboard($user->role);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            AuditLogger::log('auth.logout', [
                'username' => $user->username,
            ], $request, User::class, $user->id);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

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
