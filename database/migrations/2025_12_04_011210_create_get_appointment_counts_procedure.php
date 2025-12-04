<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetAppointmentCounts');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE GetAppointmentCounts()
BEGIN
    SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'gepland' THEN 1 ELSE 0 END) AS scheduled,
        SUM(CASE WHEN status = 'afgerond' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN status = 'geannuleerd' THEN 1 ELSE 0 END) AS cancelled
    FROM appointments;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetAppointmentCounts');
    }
};
