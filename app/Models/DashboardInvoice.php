<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Read model for invoices overview (requirements 4.2 & 4.3).
 */
class DashboardInvoice extends Model
{
    protected $table = 'invoices';

    /**
     * Haal factuurrecords op via stored procedure (requirements 4.2 & 4.3).
     *
     * @return \Illuminate\Support\Collection<int, static>
     */
    public static function records(): Collection
    {
        try {
            // Requirement 4.3: stored procedure GetInvoicesOverview aggregates invoice joins.
            $rows = DB::select('CALL GetInvoicesOverview()');
            return static::mapRows($rows);
        } catch (Throwable $exception) {
            Log::warning('Stored procedure GetInvoicesOverview failed, fallback query.', [
                'message' => $exception->getMessage(),
            ]);

            return static::fallback();
        }
    }

    /**
     * Fallback query die joins gebruikt wanneer de procedure faalt.
     *
     * @return \Illuminate\Support\Collection<int, static>
     */
    protected static function fallback(): Collection
    {
        $rows = DB::table('invoices')
            ->select(
                'invoices.*',
                'patients.first_name',
                'patients.last_name',
                'patients.email'
            )
            ->join('patients', 'patients.id', '=', 'invoices.patient_id')
            ->orderByDesc('invoices.issue_date')
            ->get();

        return static::mapRows($rows);
    }

    /**
     * Vertaal queryresultaten naar read-model instanties zodat de view duidelijk blijft.
     *
     * @param iterable<object> $rows
     * @return \Illuminate\Support\Collection<int, static>
     */
    protected static function mapRows(iterable $rows): Collection
    {
        return collect($rows)->map(function ($row) {
            $model = new static();
            $model->id = $row->id;
            $model->invoice_number = $row->invoice_number;
            $model->total_amount = $row->total_amount;
            $model->issue_date = Carbon::parse($row->issue_date);
            $model->due_date = $row->due_date ? Carbon::parse($row->due_date) : null;
            $model->status = $row->status;
            $model->patient_name = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
            $model->patient_email = $row->email ?? null;

            return $model;
        });
    }
}
