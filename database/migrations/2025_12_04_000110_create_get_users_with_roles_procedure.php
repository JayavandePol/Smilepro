<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Create the stored procedure used for fetching users with their roles.
     */
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetUsersWithRoles');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE GetUsersWithRoles()
BEGIN
    SELECT
        users.id,
        users.name,
        users.email,
        users.created_at,
        GROUP_CONCAT(DISTINCT roles.name ORDER BY roles.name SEPARATOR ',') AS role_names
    FROM users
    LEFT JOIN model_has_roles ON users.id = model_has_roles.model_id
    LEFT JOIN roles ON model_has_roles.role_id = roles.id
    GROUP BY users.id, users.name, users.email, users.created_at
    ORDER BY users.name;
END
SQL);
    }

    /**
     * Drop the stored procedure.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetUsersWithRoles');
    }
};
