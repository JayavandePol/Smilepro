<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Read model dedicated to the dashboard users index.
 */
class DashboardUser extends Model
{
    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * Fetch users and their roles via stored procedure with joins.
     */
    public static function withRoles(?string $roleFilter = null): Collection
    {
        // Requirement 4.3: prefer stored procedure for read operations.
        try {
            $rows = DB::select('CALL GetUsersWithRoles()');
            return static::filterByRole(static::mapRows($rows), $roleFilter);
        } catch (Throwable $exception) {
            Log::warning('Stored procedure GetUsersWithRoles failed, falling back to builder.', [
                'message' => $exception->getMessage(),
            ]);

            return static::fallbackWithRoles($roleFilter);
        }
    }

    /**
     * Fallback query that reproduces the stored procedure behaviour using joins.
     */
    protected static function fallbackWithRoles(?string $roleFilter = null): Collection
    {
        // Requirement 4.2: ensure we still use JOIN logic when stored procedures are unavailable.
        $rows = DB::table('users')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.created_at',
                DB::raw("GROUP_CONCAT(DISTINCT roles.name ORDER BY roles.name SEPARATOR ',') as role_names")
            )
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at')
            ->orderBy('users.name')
            ->get();

        return static::filterByRole(static::mapRows($rows), $roleFilter);
    }

    /**
     * Map raw database rows to DashboardUser instances.
     */
    protected static function mapRows(iterable $rows): Collection
    {
        return collect($rows)->map(function ($row) {
            $model = new static();
            $model->id = $row->id;
            $model->name = $row->name;
            $model->email = $row->email;
            $model->created_at = $row->created_at ? Carbon::parse($row->created_at) : null;
            $model->role_names = array_filter(explode(',', (string) ($row->role_names ?? '')));

            return $model;
        });
    }

    /**
     * Filter a list of users by role name (if provided).
     */
    protected static function filterByRole(Collection $users, ?string $roleFilter): Collection
    {
        if (!$roleFilter) {
            return $users;
        }

        // Requirement 1.1: keep server-side filtering logic so the controller stays lean.
        $normalized = strtolower($roleFilter);

        return $users->filter(function ($user) use ($normalized) {
            $roles = collect($user->role_names ?? [])->map(fn ($role) => strtolower($role));
            return $roles->contains($normalized);
        })->values();
    }
}
