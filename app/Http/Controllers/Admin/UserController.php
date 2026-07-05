<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:teacher,parent,senior_assistant',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        ActivityLog::record(
            Auth::id(),
            'User Created',
            "Admin created {$user->role} account for {$user->name} ({$user->email})"
        );

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' added successfully.");
    }

    public function show(User $user)
    {
        $activityLogs = $user->activityLogs()->latest()->take(30)->get();
        return view('admin.users.show', compact('user', 'activityLogs'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:teacher,parent,senior_assistant,admin',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
            'phone' => $request->phone,
        ]);

        ActivityLog::record(
            Auth::id(),
            'User Updated',
            "Admin edited profile of {$user->name} (ID {$user->id})"
        );

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Profile updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        ActivityLog::record(
            Auth::id(),
            'User Deleted',
            "Admin deleted user: {$name} ({$user->email}) — role: {$user->role}"
        );

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$name}' deleted.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        ActivityLog::record(
            Auth::id(),
            'Password Reset by Admin',
            "Admin reset password for {$user->name} (ID {$user->id})"
        );

        return back()->with('success', 'Password reset successfully.');
    }
}
