<?php

namespace App\Http\Controllers;

use App\Models\DashboardInvoice;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    /**
     * Toont het formulier om een nieuwe factuur aan te maken (requirement 3.1).
     */
    public function create(): View
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        // Requirement 4.2: haal alle patiënten op voor de dropdown (join/relatie).
        $patients = DB::table('patients')
            ->select('id', 'first_name', 'last_name', 'email')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Requirement 2.1: responsive create form with TailwindCSS.
        return view('dashboard.invoices.create', [
            'user' => $user,
            'patients' => $patients,
        ]);
    }

    /**
     * Valideert de invoer en slaat de nieuwe factuur op in de database (requirement 4.1).
     * - Requirement 1.1: Happy scenario – succesvol opslaan.
     * - Requirement 1.3: Unhappy scenario – validatiefouten.
     * - Requirement 4.3: Stored procedure CreateInvoice voor INSERT.
     * - Requirement 4.4: Try/catch foutafhandeling.
     * - Requirement 4.7: Technische log.
     */
    public function store(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        abort_if(!$user, 403);

        // Requirement 1.3 & 1.4: validate input with clear error messages.
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'total_amount' => 'required|numeric|min:0.01|max:99999.99',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after:issue_date',
            'status' => 'required|in:open,betaald,verlopen',
        ], [
            'patient_id.required' => 'Selecteer een patiënt.',
            'patient_id.exists' => 'De geselecteerde patiënt bestaat niet.',
            'total_amount.required' => 'Totaalbedrag is verplicht.',
            'total_amount.numeric' => 'Totaalbedrag moet een geldig bedrag zijn.',
            'total_amount.min' => 'Totaalbedrag moet minimaal €0.01 zijn.',
            'total_amount.max' => 'Totaalbedrag mag niet hoger zijn dan €99.999,99.',
            'issue_date.required' => 'Factuurdatum is verplicht.',
            'issue_date.date' => 'Voer een geldige factuurdatum in.',
            'due_date.date' => 'Voer een geldige vervaldatum in.',
            'due_date.after' => 'De vervaldatum moet na de factuurdatum liggen.',
            'status.required' => 'Selecteer een status.',
            'status.in' => 'Selecteer een geldige status: open, betaald of verlopen.',
        ]);

        // Requirement 4.4: wrap database operation in try/catch.
        try {
            // Generate unique invoice number (format: INV-YYYYMMDD-XXXX)
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(DB::table('invoices')->whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Requirement 4.3: call stored procedure CreateInvoice to insert new invoice.
            DB::statement('CALL CreateInvoice(?, ?, ?, ?, ?, ?)', [
                $validatedData['patient_id'],
                $invoiceNumber,
                $validatedData['total_amount'],
                $validatedData['issue_date'],
                $validatedData['due_date'],
                $validatedData['status'],
            ]);

            // Requirement 4.7: log successful creation.
            Log::info('Nieuwe factuur aangemaakt', [
                'user_id' => $user?->id,
                'invoice_number' => $invoiceNumber,
                'patient_id' => $validatedData['patient_id'],
                'amount' => $validatedData['total_amount'],
            ]);

            // Requirement 1.2: flash success message for end-user.
            session()->flash('success', 'Factuur succesvol aangemaakt.');

            return redirect()->route('dashboard.invoices');
        } catch (Throwable $exception) {
            // Requirement 4.7: log technical errors.
            Log::error('Fout bij aanmaken factuur', [
                'user_id' => $user?->id,
                'message' => $exception->getMessage(),
            ]);

            // Requirement 1.4: unhappy scenario messaging.
            session()->flash('error', 'Kon de factuur niet aanmaken. Probeer later opnieuw.');

            return redirect()->back()->withInput();
        }
    }
}
