<?php

namespace App\Http\Controllers;

use App\Models\DashboardPatient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Throwable;

class DashboardPatientsController extends Controller
{
    use AuthorizesRequests;

    /**
     * Overzicht patienten volgens requirements 1.x en 2.1.
     */
    public function index(): View
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        // Requirement 1.2 (Create): show success flash after creation.
        // Requirement 1.4 (Create): show error flash after validation failure.

        // Requirements 1.1 & 4.4: load data within try/catch to capture feedback states.
        try {
            /** @var \Illuminate\Support\Collection<int, DashboardPatient> $patients */
            $patients = DashboardPatient::records();
            $segment = request('segment');
            $thirtyDaysAgo = now()->subDays(30);
            $activePatients = $patients->filter(function (DashboardPatient $patient) use ($thirtyDaysAgo) {
                $lastVisit = $patient->last_visit_at ? Carbon::parse($patient->last_visit_at) : null;
                return $lastVisit && $lastVisit->greaterThanOrEqualTo($thirtyDaysAgo);
            });
            $inactivePatients = $patients->filter(function (DashboardPatient $patient) use ($thirtyDaysAgo) {
                $lastVisit = $patient->last_visit_at ? Carbon::parse($patient->last_visit_at) : null;
                return !$lastVisit || $lastVisit->lessThan($thirtyDaysAgo);
            });
            $stats = [
                'total' => $patients->count(),
                'active' => $activePatients->count(),
                'inactive' => $inactivePatients->count(),
            ];
            $filteredPatients = match ($segment) {
                'recent' => $activePatients->values(),
                'inactive' => $inactivePatients->values(),
                default => $patients,
            };
            // Requirement 1.2: give the user success feedback.
            session()->flash('success', 'Patiënten succesvol geladen.');
            // Requirement 4.7: log meta details for later explanation.
            Log::info('Patients overview loaded', ['user_id' => $user?->id, 'total' => $filteredPatients->count()]);

            // Requirement 2.1: return responsive patient table view.
            return view('dashboard.patients.view', [
                'user' => $user,
                'patients' => $filteredPatients,
                'stats' => $stats,
                'activeSegment' => $segment,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to load patients overview', [
                'user_id' => $user?->id,
                'message' => $exception->getMessage(),
            ]);

            // Requirement 1.4: unhappy scenario message for the UI.
            session()->flash('error', 'Kon de patiëntenlijst niet laden.');

            return view('dashboard.patients.view', [
                'user' => $user,
                'patients' => collect(),
                'stats' => ['total' => 0, 'active' => 0, 'inactive' => 0],
                'activeSegment' => request('segment'),
            ]);
        }
    }

    /**
     * Toont het formulier om een nieuwe patiënt aan te maken (requirement 3.1).
     */
    public function create(): View
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        // Requirement 2.1: responsive create form with TailwindCSS.
        return view('dashboard.patients.create', [
            'user' => $user,
        ]);
    }

    /**
     * Valideert de invoer en slaat de nieuwe patiënt op in de database (requirement 4.1).
     * - Requirement 1.1: Happy scenario – succesvol opslaan.
     * - Requirement 1.3: Unhappy scenario – validatiefouten (bijv. email al in gebruik).
     * - Requirement 4.3: Stored procedure CreatePatient voor INSERT.
     * - Requirement 4.4: Try/catch foutafhandeling.
     * - Requirement 4.7: Technische log.
     */
    public function store(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        // Requirement 1.3 & 1.4: validate input with clear error messages.
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email|max:255',
            'phone' => 'nullable|string|regex:/^[0-9\s\+\-\(\)]+$/|min:10|max:20',
            'date_of_birth' => 'nullable|date|before:today|after:1900-01-01',
        ], [
            'phone.regex' => 'Het telefoonnummer mag alleen cijfers, spaties en +()-tekens bevatten.',
            'phone.min' => 'Het telefoonnummer moet minimaal 10 tekens bevatten.',
            'phone.max' => 'Het telefoonnummer mag maximaal 20 tekens bevatten.',
            'date_of_birth.before' => 'De geboortedatum mag niet in de toekomst liggen.',
            'date_of_birth.after' => 'De geboortedatum moet na 1 januari 1900 liggen.',
        ]);

        // Requirement 4.4: wrap database operation in try/catch.
        try {
            // Requirement 4.3: call stored procedure CreatePatient to insert new patient.
            DB::statement('CALL CreatePatient(?, ?, ?, ?, ?)', [
                $validatedData['first_name'],
                $validatedData['last_name'],
                $validatedData['email'],
                $validatedData['phone'] ?? null,
                $validatedData['date_of_birth'] ?? null,
            ]);

            // Requirement 4.7: log successful creation.
            Log::info('Nieuwe patiënt aangemaakt', [
                'user_id' => $user?->id,
                'email' => $validatedData['email'],
            ]);

            // Requirement 1.2: flash success message for end-user.
            return redirect()->route('dashboard.patients')
                ->with('success', 'Patiënt succesvol aangemaakt.');
        } catch (\Illuminate\Database\QueryException $exception) {
            // Requirement 4.7: log database errors.
            Log::error('Fout bij aanmaken patiënt', [
                'user_id' => $user?->id,
                'message' => $exception->getMessage(),
            ]);

            // Requirement 1.4: flash error message and return with old input.
            return redirect()->back()
                ->withInput()
                ->with('error', 'Database fout opgetreden. Kon de patiënt niet opslaan.');
        }
    }
}
