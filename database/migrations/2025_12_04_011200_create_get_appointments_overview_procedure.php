<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetAppointmentsOverview');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE GetAppointmentsOverview()
BEGIN
    SELECT
        appointments.id,
        appointments.scheduled_at,
        appointments.status,
        appointments.treatment_type,
        appointments.notes,
        patients.first_name,
        patients.last_name,
        patients.email AS patient_email,
        users.name AS staff_name
    FROM appointments
    INNER JOIN patients ON patients.id = appointments.patient_id
    INNER JOIN users ON users.id = appointments.staff_id
    ORDER BY appointments.scheduled_at DESC;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetAppointmentsOverview');
    }
};
