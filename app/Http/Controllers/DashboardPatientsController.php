<?php

namespace App\Http\Controllers;

use App\Models\DashboardPatient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
}
