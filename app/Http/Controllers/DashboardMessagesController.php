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

    public function index(): View
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        try {
            $messages = DashboardMessage::records();
            $activeStatus = request('status');
            $statusCounts = $messages->groupBy('status')->map->count();
            $filteredMessages = $messages->when($activeStatus, function ($collection) use ($activeStatus) {
                return $collection->filter(fn ($message) => $message->status === $activeStatus)->values();
            }, fn ($collection) => $collection);
            session()->flash('success', 'Berichten succesvol geladen.');
            Log::info('Messages overview loaded', ['user_id' => $user?->id, 'total' => $filteredMessages->count()]);

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
