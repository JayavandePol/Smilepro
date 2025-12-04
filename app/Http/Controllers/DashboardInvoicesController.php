<?php

namespace App\Http\Controllers;

use App\Models\DashboardInvoice;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class DashboardInvoicesController extends Controller
{
    use AuthorizesRequests;

    /**
     * Toon factuuroverzicht met filter en totalen (requirements 1.x, 2.1, 4.1).
     */
    public function index(): View
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        // Requirements 1.1 & 4.4: read/aggregate within try/catch to provide feedback.
        try {
            $invoices = DashboardInvoice::records();
            $activeStatus = request('status');
            $filteredInvoices = $invoices->when($activeStatus, function ($collection) use ($activeStatus) {
                return $collection->filter(fn ($invoice) => $invoice->status === $activeStatus)->values();
            }, fn ($collection) => $collection);
            // Requirement 1.2: success flash for the end-user.
            session()->flash('success', 'Facturen succesvol geladen.');
            // Requirement 4.7: log context for auditing.
            Log::info('Invoices overview loaded', ['user_id' => $user?->id, 'total' => $filteredInvoices->count()]);

            // Requirement 2.1: serve Tailwind view with responsive cards/table.
            return view('dashboard.invoices.view', [
                'user' => $user,
                'invoices' => $filteredInvoices,
                'activeStatus' => $activeStatus,
                'totals' => [
                    'sum' => $invoices->sum('total_amount'),
                    'open' => $invoices->where('status', 'open')->sum('total_amount'),
                    'paid' => $invoices->where('status', 'betaald')->sum('total_amount'),
                ],
            ]);
        } catch (Throwable $exception) {
            Log::error('Failed to load invoices', [
                'user_id' => $user?->id,
                'message' => $exception->getMessage(),
            ]);

            // Requirement 1.4: unhappy scenario feedback with actionable message.
            session()->flash('error', 'Kon het factuuroverzicht niet laden.');

            return view('dashboard.invoices.view', [
                'user' => $user,
                'invoices' => collect(),
                'activeStatus' => request('status'),
                'totals' => ['sum' => 0, 'open' => 0, 'paid' => 0],
            ]);
        }
    }
}
