<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Read model for patient overview (requirements 4.2 & 4.3).
 */
class DashboardPatient extends Model
{
    protected $table = 'patients';

    /**
     * Haal alle patientrecords via stored procedure (requirements 4.2 & 4.3).
     *
     * @return \Illuminate\Support\Collection<int, static>
     */
    public static function records(): Collection
    {
        try {
            // Requirement 4.3: stored procedure GetPatientsOverview powers this read.
            $rows = DB::select('CALL GetPatientsOverview()');
            return static::mapRows($rows);
        } catch (Throwable $exception) {
            Log::warning('Stored procedure GetPatientsOverview failed, using builder.', [
                'message' => $exception->getMessage(),
            ]);

            return static::fallback();
        }
    }

    /**
     * Fallback query met ordering wanneer de procedure onbeschikbaar is.
     *
     * @return \Illuminate\Support\Collection<int, static>
     */
    protected static function fallback(): Collection
    {
        $rows = DB::table('patients')
            ->orderBy('last_visit_at', 'desc')
            ->orderBy('last_name')
            ->get();

        return static::mapRows($rows);
    }

    /**
     * Vertaal rauwe queryresultaten naar read-model instanties.
     *
     * @param iterable<object> $rows
     * @return \Illuminate\Support\Collection<int, static>
     */
    protected static function mapRows(iterable $rows): Collection
    {
        return collect($rows)->map(function ($row) {
            $model = new static();
            $model->id = $row->id;
            $model->first_name = $row->first_name;
            $model->last_name = $row->last_name;
            $model->email = $row->email;
            $model->phone = $row->phone;
            $model->date_of_birth = $row->date_of_birth ? Carbon::parse($row->date_of_birth) : null;
            $model->last_visit_at = $row->last_visit_at ? Carbon::parse($row->last_visit_at) : null;

            return $model;
        });
    }
}
