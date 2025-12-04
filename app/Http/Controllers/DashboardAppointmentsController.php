<?php

namespace App\Http\Controllers;

use App\Models\DashboardAppointment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class DashboardAppointmentsController extends Controller
{
    use AuthorizesRequests;

    /**
     * Toon afsprakenoverzicht inclusief tellers (requirements 1.x & 4.x).
     */
    public function index(): View
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        try {
            $appointments = DashboardAppointment::records();
            $counts = DashboardAppointment::counts();
            $activeStatus = request('status');
            $filteredAppointments = $appointments->when($activeStatus, function ($collection) use ($activeStatus) {
                return $collection->filter(fn ($appointment) => $appointment->status === $activeStatus)->values();
            }, fn ($collection) => $collection);
            session()->flash('success', 'Afspraken succesvol geladen.');
            Log::info('Appointments overview loaded', [
                'user_id' => $user?->id,
                'total' => $filteredAppointments->count(),
            ]);

            return view('dashboard.appointments.view', [
                'user' => $user,
                'appointments' => $filteredAppointments,
                'counts' => $counts,
                'activeStatus' => $activeStatus,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to load appointments overview', [
                'user_id' => $user?->id,
                'message' => $exception->getMessage(),
            ]);

            session()->flash('error', 'Kon de afspraken niet ophalen.');

            return view('dashboard.appointments.view', [
                'user' => $user,
                'appointments' => collect(),
                'counts' => [
                    'total' => 0,
                    'scheduled' => 0,
                    'completed' => 0,
                    'cancelled' => 0,
                ],
                'activeStatus' => request('status'),
            ]);
        }
    }
}
