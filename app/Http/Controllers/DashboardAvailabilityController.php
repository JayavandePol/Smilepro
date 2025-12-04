<?php

namespace App\Http\Controllers;

use App\Models\DashboardAvailability;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
}
