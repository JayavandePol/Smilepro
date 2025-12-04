<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetMessagesOverview');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE GetMessagesOverview()
BEGIN
    SELECT
        messages.id,
        messages.subject,
        messages.status,
        messages.received_at,
        patients.first_name,
        patients.last_name,
        patients.email
    FROM messages
    INNER JOIN patients ON patients.id = messages.patient_id
    ORDER BY messages.received_at DESC;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetMessagesOverview');
    }
};
