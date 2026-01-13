<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Stored procedure CreateAvailability volgens requirement 4.3.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Requirement 4.3: CreateAvailability stored procedure voor INSERT operatie.
        DB::unprepared('
            CREATE PROCEDURE CreateAvailability(
                IN p_user_id BIGINT UNSIGNED,
                IN p_available_on DATE,
                IN p_slot VARCHAR(255),
                IN p_status ENUM("open", "booked", "blocked"),
                IN p_notes TEXT
            )
            BEGIN
                INSERT INTO availabilities (user_id, available_on, slot, status, notes, created_at, updated_at)
                VALUES (p_user_id, p_available_on, p_slot, p_status, p_notes, NOW(), NOW());
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS CreateAvailability');
    }
};
