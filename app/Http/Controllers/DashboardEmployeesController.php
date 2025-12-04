<?php

namespace App\Http\Controllers;

use App\Models\DashboardUser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class DashboardEmployeesController extends Controller
{
    use AuthorizesRequests;

    /**
     * Toon het medewerker overzicht met filters per rol.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        $employeeRoles = ['tandarts', 'mondhygienist', 'assistent', 'praktijkmanagement'];
        $activeRole = $request->query('role');

        if ($activeRole && !in_array($activeRole, $employeeRoles, true)) {
            $activeRole = null;
        }

        // Requirements 1.1–1.4 + 4.4: capture both success and failure states with messaging.
        try {
            $users = DashboardUser::withRoles($activeRole)
                ->filter(fn ($managedUser) => collect($managedUser->role_names ?? [])->intersect($employeeRoles)->isNotEmpty());

            session()->flash('success', 'Medewerkers succesvol geladen.');
            Log::info('Dashboard employees loaded', [
                'user_id' => $user?->id,
                'total' => $users->count(),
                'role_filter' => $activeRole,
            ]);

            return view('dashboard.employees.view', [
                'user' => $user,
                'users' => $users,
                'roleOptions' => collect($employeeRoles),
                'activeRole' => $activeRole,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to load dashboard employees', [
                'user_id' => $user?->id,
                'message' => $exception->getMessage(),
            ]);

            // Requirement 1.4 unhappy scenario message.
            session()->flash('error', 'Kon het medewerkersoverzicht niet ophalen. Probeer het later opnieuw.');

            return view('dashboard.employees.view', [
                'user' => $user,
                'users' => collect(),
                'roleOptions' => collect($employeeRoles),
                'activeRole' => $activeRole,
            ]);
        }
    }
}
