<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditLogger;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('Admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,cashier,kitchen',
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        AuditLogger::log('admin.user.created', [
            'username' => $user->username,
            'role' => $user->role,
        ], $request, User::class, $user->id);

        return redirect()->route('admin.users')->with('success', 'User created successfully');
    }

    // Add this method for changing password
    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:15|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        AuditLogger::log('admin.user.password_updated', [
            'username' => $user->username,
        ], $request, User::class, $user->id);

        return redirect()->route('admin.users')->with('success', 'Password updated successfully for ' . $user->username);
    }

    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'Cannot delete your own account');
        }

        $snapshot = $user->only(['id', 'username', 'role']);
        $user->delete();

        AuditLogger::log('admin.user.deleted', [
            'user' => $snapshot,
        ], request(), User::class, $snapshot['id'] ?? null);

        return redirect()->route('admin.users')->with('success', 'User deleted successfully');
    }
}