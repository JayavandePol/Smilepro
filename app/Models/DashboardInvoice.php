<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Read model for invoices overview.
 */
class DashboardInvoice extends Model
{
    protected $table = 'invoices';

    public static function records(): Collection
    {
        try {
            $rows = DB::select('CALL GetInvoicesOverview()');
            return static::mapRows($rows);
        } catch (Throwable $exception) {
            Log::warning('Stored procedure GetInvoicesOverview failed, fallback query.', [
                'message' => $exception->getMessage(),
            ]);

            return static::fallback();
        }
    }

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
