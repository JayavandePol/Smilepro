<?php

namespace App\Http\Controllers;

use App\Models\DashboardAvailability;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class DashboardAvailabilityController extends Controller
{
    use AuthorizesRequests;

    /**
     * Overzicht beschikbaarheid (requirements 1.x & 2.1).
     */
    public function index(): View
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        // Requirements 1.1 & 4.4: protect read path with try/catch for feedback.
        try {
            $records = DashboardAvailability::records();
            $statusCounts = $records->groupBy('status')->map->count();
            $activeStatus = request('status');
            $filteredRecords = $records->when($activeStatus, function ($collection) use ($activeStatus) {
                return $collection->filter(fn ($slot) => $slot->status === $activeStatus)->values();
            }, fn ($collection) => $collection);
            // Requirement 1.2: communicate success.
            session()->flash('success', 'Beschikbaarheid succesvol geladen.');
            // Requirement 4.7: log diagnostic context for auditing.
            Log::info('Availability overview loaded', ['user_id' => $user?->id, 'total' => $filteredRecords->count()]);

            // Requirement 2.1: serve the responsive availability table view.
            return view('dashboard.availability.view', [
                'user' => $user,
                'records' => $filteredRecords,
                'statusCounts' => $statusCounts,
                'activeStatus' => $activeStatus,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to load availability overview', [
                'user_id' => $user?->id,
                'message' => $exception->getMessage(),
            ]);

            // Requirement 1.4: unhappy scenario messaging with clear guidance.
            session()->flash('error', 'Kon de beschikbaarheid niet laden. Probeer later opnieuw.');

            return view('dashboard.availability.view', [
                'user' => $user,
                'records' => collect(),
                'statusCounts' => collect(),
                'activeStatus' => request('status'),
            ]);
        }
    }

    /**
     * Toont het formulier om een nieuwe beschikbaarheid aan te maken (requirement 3.1).
     */
    public function create(): View
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        // Requirement 4.2: haal alle medewerkers op voor de dropdown (join/relatie).
        $employees = \App\Models\User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        // Requirement 2.1: responsive create form with TailwindCSS.
        return view('dashboard.availability.create', [
            'user' => $user,
            'employees' => $employees,
        ]);
    }

    /**
     * Valideert de invoer en slaat de nieuwe beschikbaarheid op in de database (requirement 4.1).
     * - Requirement 1.1: Happy scenario – succesvol opslaan.
     * - Requirement 1.3: Unhappy scenario – validatiefouten (datum in verleden, ongeldige slot, etc).
     * - Requirement 4.3: Stored procedure CreateAvailability voor INSERT.
     * - Requirement 4.4: Try/catch foutafhandeling.
     * - Requirement 4.7: Technische log.
     */
    public function store(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        // Requirement 1.3 & 1.4: validate input with clear error messages.
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'available_on' => 'required|date|after_or_equal:today',
            'slot' => ['required', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'],
            'status' => 'required|in:open,booked,blocked',
            'notes' => 'nullable|string|max:500',
        ], [
            'available_on.after_or_equal' => 'De datum mag niet in het verleden liggen.',
            'slot.regex' => 'Selecteer een geldig tijdstip (formaat: HH:MM).',
            'status.in' => 'Selecteer een geldige status: open, geboekt of geblokkeerd.',
            'user_id.exists' => 'De geselecteerde medewerker bestaat niet.',
            'notes.max' => 'Notities mogen maximaal 500 tekens bevatten.',
        ]);

        // Requirement 1.3: Check if availability already exists for this employee at this time slot.
        $existingAvailability = DB::table('availabilities')
            ->where('user_id', $validatedData['user_id'])
            ->where('available_on', $validatedData['available_on'])
            ->where('slot', $validatedData['slot'])
            ->exists();

        if ($existingAvailability) {
            // Requirement 1.4: Unhappy scenario - duplicate availability.
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['slot' => 'Er bestaat al een beschikbaarheid voor deze medewerker op dit tijdstip.']);
        }

        // Requirement 4.4: wrap database operation in try/catch.
        try {
            // Requirement 4.3: call stored procedure CreateAvailability to insert new availability.
            DB::statement('CALL CreateAvailability(?, ?, ?, ?, ?)', [
                $validatedData['user_id'],
                $validatedData['available_on'],
                $validatedData['slot'],
                $validatedData['status'],
                $validatedData['notes'] ?? null,
            ]);

            // Requirement 4.7: log successful creation.
            Log::info('Nieuwe beschikbaarheid aangemaakt', [
                'user_id' => $user?->id,
                'employee_id' => $validatedData['user_id'],
                'date' => $validatedData['available_on'],
                'slot' => $validatedData['slot'],
            ]);

            // Requirement 1.2: flash success message for end-user.
            return redirect()->route('dashboard.availability')
                ->with('success', 'Beschikbaarheid succesvol aangemaakt.');
        } catch (\Illuminate\Database\QueryException $exception) {
            // Requirement 4.7: log database errors.
            Log::error('Fout bij aanmaken beschikbaarheid', [
                'user_id' => $user?->id,
                'message' => $exception->getMessage(),
            ]);

            // Requirement 1.4: flash error message and return with old input.
            return redirect()->back()
                ->withInput()
                ->with('error', 'Database fout opgetreden. Kon de beschikbaarheid niet opslaan.');
        }
    }
}
