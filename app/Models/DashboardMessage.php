<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Read model for patient messages overview.
 */
class DashboardMessage extends Model
{
    protected $table = 'messages';

    public static function records(): Collection
    {
        try {
            $rows = DB::select('CALL GetMessagesOverview()');
            return static::mapRows($rows);
        } catch (Throwable $exception) {
            Log::warning('Stored procedure GetMessagesOverview failed, fallback query.', [
                'message' => $exception->getMessage(),
            ]);

            return static::fallback();
        }
    }

    protected static function fallback(): Collection
    {
        $rows = DB::table('messages')
            ->select(
                'messages.*',
                'patients.first_name',
                'patients.last_name',
                'patients.email'
            )
            ->join('patients', 'patients.id', '=', 'messages.patient_id')
            ->orderByDesc('messages.received_at')
            ->get();

        return static::mapRows($rows);
    }

    protected static function mapRows(iterable $rows): Collection
    {
        return collect($rows)->map(function ($row) {
            $model = new static();
            $model->id = $row->id;
            $model->subject = $row->subject;
            $model->status = $row->status;
            $model->received_at = Carbon::parse($row->received_at);
            $model->body = $row->body ?? null;
            $model->patient_name = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));
            $model->patient_email = $row->email ?? null;

            return $model;
        });
    }
}
