<?php

namespace App\Http\Controllers;

use App\Models\DashboardOverview;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class DashboardOverviewController extends Controller
{
    use AuthorizesRequests;

    /**
     * Toon het dashboard-overzicht met metrics.
     */
    public function index()
    {
        $user = Auth::user();

        // Requirements 1.1 & 1.3 + 4.4: wrap the read in try/catch so we can surface success/error feedback.
        try {
            $metrics = DashboardOverview::metrics();
            session()->flash('success', 'Dashboardgegevens succesvol geladen.');
            Log::info('Dashboard metrics loaded', ['user_id' => $user?->id]);

            return view('dashboard-overview', compact('user', 'metrics'));
        } catch (Throwable $exception) {
            Log::error('Failed to load dashboard metrics', [
                'user_id' => $user?->id,
                'message' => $exception->getMessage(),
            ]);

            // Requirement 1.4: unhappy scenario feedback for the end-user.
            session()->flash('error', 'Kon dashboardgegevens niet ophalen. Probeer het later opnieuw.');

            return view('dashboard-overview', [
                'user' => $user,
                'metrics' => [
                    'total_users' => 0,
                    'verified_users' => 0,
                    'management_members' => 0,
                ],
            ]);
        }
    }
}
