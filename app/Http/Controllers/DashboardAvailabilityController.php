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

        try {
            $records = DashboardAvailability::records();
            $statusCounts = $records->groupBy('status')->map->count();
            $activeStatus = request('status');
            $filteredRecords = $records->when($activeStatus, function ($collection) use ($activeStatus) {
                return $collection->filter(fn ($slot) => $slot->status === $activeStatus)->values();
            }, fn ($collection) => $collection);
            session()->flash('success', 'Beschikbaarheid succesvol geladen.');
            Log::info('Availability overview loaded', ['user_id' => $user?->id, 'total' => $filteredRecords->count()]);

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
