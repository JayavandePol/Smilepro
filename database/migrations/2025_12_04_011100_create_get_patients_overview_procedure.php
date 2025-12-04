<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetPatientsOverview');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE GetPatientsOverview()
BEGIN
    SELECT
        patients.id,
        patients.first_name,
        patients.last_name,
        patients.email,
        patients.phone,
        patients.date_of_birth,
        patients.last_visit_at
    FROM patients
    ORDER BY
        patients.last_visit_at IS NULL ASC,
        patients.last_visit_at DESC,
        patients.last_name ASC;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetPatientsOverview');
    }
};
