<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetInvoicesOverview');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE GetInvoicesOverview()
BEGIN
    SELECT
        invoices.id,
        invoices.invoice_number,
        invoices.total_amount,
        invoices.issue_date,
        invoices.due_date,
        invoices.status,
        patients.first_name,
        patients.last_name,
        patients.email
    FROM invoices
    INNER JOIN patients ON patients.id = invoices.patient_id
    ORDER BY invoices.issue_date DESC;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS GetInvoicesOverview');
    }
};
