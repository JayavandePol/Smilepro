<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use App\Models\User;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        return view('dashboard-overview', compact('user'));
    }

    public function users()
    {
        $user = Auth::user();
        abort_if(!$user->hasRole('praktijkmanagement'), 403);

        $roles = Role::all();
        $users = User::with('roles')->orderBy('name')->get();

        return view('users', compact('user', 'roles', 'users'));
    }

    public function assignRole(Request $request, User $user)
    {
        $this->authorize('assignRole', $user);
        $role = $request->input('role');
        if ($role && Role::where('name', $role)->exists()) {
            $user->syncRoles([$role]);
        }
        return redirect()->route('dashboard.users')->with('status', 'Role updated!');
    }
}
