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

    public function index(): View
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        try {
            $invoices = DashboardInvoice::records();
            $activeStatus = request('status');
            $filteredInvoices = $invoices->when($activeStatus, function ($collection) use ($activeStatus) {
                return $collection->filter(fn ($invoice) => $invoice->status === $activeStatus)->values();
            }, fn ($collection) => $collection);
            session()->flash('success', 'Facturen succesvol geladen.');
            Log::info('Invoices overview loaded', ['user_id' => $user?->id, 'total' => $filteredInvoices->count()]);

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
