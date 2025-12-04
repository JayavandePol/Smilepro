<?php

namespace App\Http\Controllers;

use App\Models\DashboardUser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Throwable;

class DashboardUsersController extends Controller
{
    use AuthorizesRequests;

    /**
     * Toon het gebruikersoverzicht met feedback voor eindgebruikers.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        abort_if(!$user, 403);
        $activeRole = $request->query('role');

        // Requirements 1.1–1.4: happy/unhappy flows plus UI feedback for reads.
        try {
            $roleOptions = Role::orderBy('name')->pluck('name');
            $users = DashboardUser::withRoles($activeRole);
            // Requirement 1.2: success toast so the user knows the read completed.
            session()->flash('success', 'Gebruikers succesvol geladen.');
            // Requirement 4.7: structured logging of totals/filters.
            Log::info('Dashboard users loaded', [
                'user_id' => $user?->id,
                'total' => $users->count(),
                'role_filter' => $activeRole,
            ]);

            // Requirement 2.1: return Tailwind responsive table view.
            return view('dashboard.users.view', [
                'user' => $user,
                'users' => $users,
                'roleOptions' => $roleOptions,
                'activeRole' => $activeRole,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to load dashboard users', [
                'user_id' => $user?->id,
                'message' => $exception->getMessage(),
            ]);

            // Requirement 1.4: explicit unhappy scenario feedback, logged per 4.4 & 4.7.
            session()->flash('error', 'Kon de gebruikerslijst niet ophalen. Probeer het later opnieuw.');

            return view('dashboard.users.view', [
                'user' => $user,
                'users' => collect(),
                'roleOptions' => collect(),
                'activeRole' => $activeRole,
            ]);
        }
    }
}
