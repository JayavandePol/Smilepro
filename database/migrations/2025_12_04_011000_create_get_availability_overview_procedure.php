<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetAvailabilityOverview');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE GetAvailabilityOverview()
BEGIN
    SELECT
        availabilities.id,
        availabilities.available_on,
        availabilities.slot,
        availabilities.status,
        availabilities.notes,
        users.name AS staff_name,
        users.email AS staff_email
    FROM availabilities
    INNER JOIN users ON users.id = availabilities.user_id
    ORDER BY availabilities.available_on ASC, availabilities.slot ASC;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetAvailabilityOverview');
    }
};
