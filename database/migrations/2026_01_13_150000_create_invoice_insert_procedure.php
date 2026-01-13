<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Stored procedure CreateInvoice volgens requirement 4.3.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Requirement 4.3: CreateInvoice stored procedure voor INSERT operatie.
        DB::unprepared('
            CREATE PROCEDURE CreateInvoice(
                IN p_patient_id BIGINT UNSIGNED,
                IN p_invoice_number VARCHAR(255),
                IN p_total_amount DECIMAL(8,2),
                IN p_issue_date DATE,
                IN p_due_date DATE,
                IN p_status ENUM("open", "betaald", "verlopen")
            )
            BEGIN
                INSERT INTO invoices (patient_id, invoice_number, total_amount, issue_date, due_date, status, created_at, updated_at)
                VALUES (p_patient_id, p_invoice_number, p_total_amount, p_issue_date, p_due_date, p_status, NOW(), NOW());
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS CreateInvoice');
    }
};
