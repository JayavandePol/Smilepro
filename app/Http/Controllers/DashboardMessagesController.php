<?php

namespace App\Http\Controllers;

use App\Models\DashboardMessage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class DashboardMessagesController extends Controller
{
    use AuthorizesRequests;

    /**
     * Toon berichtenoverzicht met statusfilters (requirements 1.x, 2.1, 4.1).
     */
    public function index(): View
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        // Requirements 1.1 & 4.4: protect stored-procedure read with try/catch for user feedback.
        try {
            $messages = DashboardMessage::records();
            $activeStatus = request('status');
            $statusCounts = $messages->groupBy('status')->map->count();
            $filteredMessages = $messages->when($activeStatus, function ($collection) use ($activeStatus) {
                return $collection->filter(fn ($message) => $message->status === $activeStatus)->values();
            }, fn ($collection) => $collection);
            // Requirement 1.2: success feedback after loading.
            session()->flash('success', 'Berichten succesvol geladen.');
            // Requirement 4.7: log diagnostics for the audit trail.
            Log::info('Messages overview loaded', ['user_id' => $user?->id, 'total' => $filteredMessages->count()]);

            // Requirement 2.1: return responsive messaging board view.
            return view('dashboard.messages.view', [
                'user' => $user,
                'messages' => $filteredMessages,
                'statusCounts' => $statusCounts,
                'activeStatus' => $activeStatus,
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to load messages overview', [
                'user_id' => $user?->id,
                'message' => $exception->getMessage(),
            ]);

            // Requirement 1.4: unhappy scenario feedback for the inbox view.
            session()->flash('error', 'Kon het berichtenoverzicht niet laden.');

            return view('dashboard.messages.view', [
                'user' => $user,
                'messages' => collect(),
                'statusCounts' => collect(),
                'activeStatus' => request('status'),
            ]);
        }
    }
}
