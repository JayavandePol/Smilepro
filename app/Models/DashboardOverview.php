<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Aggregates dashboard overview metrics through a stored procedure call.
 */
class DashboardOverview extends Model
{
    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * Stored procedure driven metrics for the landing dashboard.
     *
     * @return array<string, int>
     */
    public static function metrics(): array
    {
        // Requirement 4.3: primary read uses stored procedure GetDashboardMetrics.
        try {
            $result = DB::select('CALL GetDashboardMetrics()');
            $row = $result[0] ?? null;

            return [
                'total_users' => (int) ($row->total_users ?? 0),
                'verified_users' => (int) ($row->verified_users ?? 0),
                'management_members' => (int) ($row->management_members ?? 0),
            ];
        } catch (Throwable $exception) {
            Log::warning('Stored procedure GetDashboardMetrics failed, falling back to query.', [
                'message' => $exception->getMessage(),
            ]);

            return static::fallbackMetrics();
        }
    }

    /**
     * Fallback metrics using regular queries when stored procedures are unavailable.
     *
     * @return array<string, int>
     */
    protected static function fallbackMetrics(): array
    {
        // Requirement 4.2: fallback uses explicit JOIN queries.
        $totalUsers = DB::table('users')->count();
        $verifiedUsers = DB::table('users')->whereNotNull('email_verified_at')->count();
        $managementMembers = DB::table('model_has_roles as mr')
            ->join('roles as r', 'mr.role_id', '=', 'r.id')
            ->where('mr.model_type', '=', User::class)
            ->where('r.name', '=', 'praktijkmanagement')
            ->count();

        return [
            'total_users' => (int) $totalUsers,
            'verified_users' => (int) $verifiedUsers,
            'management_members' => (int) $managementMembers,
        ];
    }
}
