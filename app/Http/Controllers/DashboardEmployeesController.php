<?php

namespace App\Http\Controllers;

use App\Models\DashboardUser;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

            // Requirement 1.2: report happy scenario.
            session()->flash('success', 'Medewerkers succesvol geladen.');
            // Requirement 4.7: structured logging for audits.
            Log::info('Dashboard employees loaded', [
                'user_id' => $user?->id,
                'total' => $users->count(),
                'role_filter' => $activeRole,
            ]);

            // Requirement 2.1: return responsive employees grid/table.
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

    /**
     * Toont het formulier om een nieuwe medewerker aan te maken (requirement 3.1).
     */
    public function create(): View
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        // Requirement 2.1: responsive create form with TailwindCSS.
        return view('dashboard.employees.create', [
            'user' => $user,
        ]);
    }

    /**
     * Valideert de invoer en slaat de nieuwe medewerker op in de database (requirement 4.1).
     * - Requirement 1.1: Happy scenario – succesvol opslaan.
     * - Requirement 1.3: Unhappy scenario – validatiefouten (email bestaat al, etc).
     * - Requirement 4.3: Stored procedure CreateEmployee voor INSERT.
     * - Requirement 4.4: Try/catch foutafhandeling.
     * - Requirement 4.7: Technische log.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        // Requirement 1.3 & 1.4: validate input with clear error messages.
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:tandarts,mondhygienist,assistent,praktijkmanagement',
        ], [
            'name.required' => 'Naam is verplicht.',
            'email.required' => 'E-mailadres is verplicht.',
            'email.email' => 'Voer een geldig e-mailadres in.',
            'email.unique' => 'Dit e-mailadres is al in gebruik.',
            'password.required' => 'Wachtwoord is verplicht.',
            'password.min' => 'Wachtwoord moet minimaal 8 tekens bevatten.',
            'password.confirmed' => 'Wachtwoordbevestiging komt niet overeen.',
            'role.required' => 'Selecteer een rol.',
            'role.in' => 'Selecteer een geldige rol.',
        ]);

        // Requirement 4.4: wrap database operation in try/catch.
        try {
            // Hash password before storing
            $hashedPassword = Hash::make($validatedData['password']);

            // Requirement 4.3: call stored procedure CreateEmployee to insert new employee.
            DB::statement('CALL CreateEmployee(?, ?, ?)', [
                $validatedData['name'],
                $validatedData['email'],
                $hashedPassword,
            ]);

            // Get the newly created user to assign role
            $newUser = DB::table('users')->where('email', $validatedData['email'])->first();

            if ($newUser) {
                // Assign role using Spatie (requirement 4.2: relational data)
                $userModel = \App\Models\User::find($newUser->id);
                $userModel->assignRole($validatedData['role']);
            }

            // Requirement 4.7: log successful creation.
            Log::info('Nieuwe medewerker aangemaakt', [
                'user_id' => $user?->id,
                'employee_email' => $validatedData['email'],
                'role' => $validatedData['role'],
            ]);

            // Requirement 1.2: flash success message for end-user.
            session()->flash('success', 'Medewerker succesvol aangemaakt.');

            return redirect()->route('dashboard.employees');
        } catch (Throwable $exception) {
            // Requirement 4.7: log technical errors.
            Log::error('Fout bij aanmaken medewerker', [
                'user_id' => $user?->id,
                'message' => $exception->getMessage(),
            ]);

            // Requirement 1.4: unhappy scenario messaging.
            session()->flash('error', 'Kon de medewerker niet aanmaken. Probeer later opnieuw.');

            return redirect()->back()->withInput();
        }
    }
}
