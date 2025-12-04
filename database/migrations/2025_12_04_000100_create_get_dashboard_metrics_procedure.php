<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Create the stored procedure that powers the dashboard overview metrics.
     */
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetDashboardMetrics');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE GetDashboardMetrics()
BEGIN
    SELECT
        (SELECT COUNT(*) FROM users) AS total_users,
        (SELECT COUNT(*) FROM users WHERE email_verified_at IS NOT NULL) AS verified_users,
        (
            SELECT COUNT(*)
            FROM model_has_roles mr
            INNER JOIN roles r ON mr.role_id = r.id
            WHERE r.name = 'praktijkmanagement'
        ) AS management_members;
END
SQL);
    }

    /**
     * Drop the procedure when rolling the migration back.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetDashboardMetrics');
    }
};
