<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Read model for appointments overview with fallback join query.
 */
class DashboardAppointment extends Model
{
    protected $table = 'appointments';

    public static function records(): Collection
    {
        try {
            $rows = DB::select('CALL GetAppointmentsOverview()');
            return static::mapRows($rows);
        } catch (Throwable $exception) {
            Log::warning('Stored procedure GetAppointmentsOverview failed, fallback query.', [
                'message' => $exception->getMessage(),
            ]);

            return static::fallback();
        }
    }

    public static function counts(): array
    {
        try {
            $result = DB::select('CALL GetAppointmentCounts()');
            $row = $result[0] ?? null;
            return [
                'total' => (int) ($row->total ?? 0),
                'scheduled' => (int) ($row->scheduled ?? 0),
                'completed' => (int) ($row->completed ?? 0),
                'cancelled' => (int) ($row->cancelled ?? 0),
            ];
        } catch (Throwable $exception) {
            Log::warning('Stored procedure GetAppointmentCounts failed, fallback aggregate.', [
                'message' => $exception->getMessage(),
            ]);

            $builder = DB::table('appointments');
            return [
                'total' => (int) $builder->count(),
                'scheduled' => (int) $builder->where('status', 'gepland')->count(),
                'completed' => (int) DB::table('appointments')->where('status', 'afgerond')->count(),
                'cancelled' => (int) DB::table('appointments')->where('status', 'geannuleerd')->count(),
            ];
        }
    }

    protected static function fallback(): Collection
    {
        $rows = DB::table('appointments')
            ->select(
                'appointments.*',
                'patients.first_name',
                'patients.last_name',
                'patients.email as patient_email',
                'users.name as staff_name'
            )
            ->join('patients', 'patients.id', '=', 'appointments.patient_id')
            ->join('users', 'users.id', '=', 'appointments.staff_id')
            ->orderByDesc('appointments.scheduled_at')
            ->get();

        return static::mapRows($rows);
    }

    protected static function mapRows(iterable $rows): Collection
    {
        return collect($rows)->map(function ($row) {
            $model = new static();
            $model->id = $row->id;
            $model->scheduled_at = Carbon::parse($row->scheduled_at);
            $model->status = $row->status;
            $model->treatment_type = $row->treatment_type;
            $model->notes = $row->notes;
            $model->patient_name = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
            $model->patient_email = $row->patient_email ?? null;
            $model->staff_name = $row->staff_name ?? null;

            return $model;
        });
    }
}
