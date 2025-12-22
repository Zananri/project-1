<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change status column from ENUM to VARCHAR to support more status values
        DB::statement("ALTER TABLE `transaction_approvals` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM
        DB::statement("ALTER TABLE `transaction_approvals` MODIFY `status` ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending'");
    }
};
