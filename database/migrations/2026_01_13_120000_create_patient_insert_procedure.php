<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Stored procedure CreatePatient volgens requirement 4.3.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Requirement 4.3: CreatePatient stored procedure voor INSERT operatie.
        DB::unprepared('
            CREATE PROCEDURE CreatePatient(
                IN p_first_name VARCHAR(255),
                IN p_last_name VARCHAR(255),
                IN p_email VARCHAR(255),
                IN p_phone VARCHAR(255),
                IN p_date_of_birth DATE
            )
            BEGIN
                INSERT INTO patients (first_name, last_name, email, phone, date_of_birth, created_at, updated_at)
                VALUES (p_first_name, p_last_name, p_email, p_phone, p_date_of_birth, NOW(), NOW());
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS CreatePatient');
    }
};
