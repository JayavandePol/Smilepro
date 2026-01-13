<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Stored procedure CreateEmployee volgens requirement 4.3.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Requirement 4.3: CreateEmployee stored procedure voor INSERT operatie.
        DB::unprepared('
            CREATE PROCEDURE CreateEmployee(
                IN p_name VARCHAR(255),
                IN p_email VARCHAR(255),
                IN p_password VARCHAR(255)
            )
            BEGIN
                INSERT INTO users (name, email, password, email_verified_at, created_at, updated_at)
                VALUES (p_name, p_email, p_password, NOW(), NOW(), NOW());
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS CreateEmployee');
    }
};
