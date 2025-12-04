<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Read model for availability overview (requirements 4.2 & 4.3).
 */
class DashboardAvailability extends Model
{
    protected $table = 'availabilities';

    /**
     * Haal alle beschikbaarheid op via stored procedure (requirements 4.2 & 4.3).
     *
     * @return \Illuminate\Support\Collection<int, static>
     */
    public static function records(): Collection
    {
        try {
            // Requirement 4.3: stored procedure entry point for availability reads.
            $rows = DB::select('CALL GetAvailabilityOverview()');
            return static::mapRows($rows);
        } catch (Throwable $exception) {
            Log::warning('Stored procedure GetAvailabilityOverview failed, using builder.', [
                'message' => $exception->getMessage(),
            ]);

            return static::fallback();
        }
    }

    /**
     * Fallback query met joins wanneer de procedure faalt.
     *
     * @return \Illuminate\Support\Collection<int, static>
     */
    protected static function fallback(): Collection
    {
        $rows = DB::table('availabilities')
            ->select(
                'availabilities.*',
                'users.name as staff_name',
                'users.email as staff_email'
            )
            ->join('users', 'users.id', '=', 'availabilities.user_id')
            ->orderBy('availabilities.available_on')
            ->orderBy('availabilities.slot')
            ->get();

        return static::mapRows($rows);
    }

    /**
     * Zet rauwe database-rijen om naar read-model instanties.
     *
     * @param iterable<object> $rows
     * @return \Illuminate\Support\Collection<int, static>
     */
    protected static function mapRows(iterable $rows): Collection
    {
        return collect($rows)->map(function ($row) {
            $model = new static();
            $model->id = $row->id;
            $model->available_on = Carbon::parse($row->available_on);
            $model->slot = $row->slot;
            $model->status = $row->status;
            $model->notes = $row->notes;
            $model->staff_name = $row->staff_name;
            $model->staff_email = $row->staff_email;

            return $model;
        });
    }
}
