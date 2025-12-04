<?php

namespace App\Http\Controllers;

use App\Models\DashboardPatient;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        try {
            $patients = DashboardPatient::records();
            $segment = request('segment');
            $thirtyDaysAgo = now()->subDays(30);
            $activePatients = $patients->filter(fn ($patient) => $patient->last_visit_at && $patient->last_visit_at->greaterThanOrEqualTo($thirtyDaysAgo));
            $inactivePatients = $patients->filter(fn ($patient) => !$patient->last_visit_at || $patient->last_visit_at->lessThan($thirtyDaysAgo));
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
            session()->flash('success', 'Patiënten succesvol geladen.');
            Log::info('Patients overview loaded', ['user_id' => $user?->id, 'total' => $filteredPatients->count()]);

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
