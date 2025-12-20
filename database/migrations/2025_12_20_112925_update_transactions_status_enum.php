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
        // First, change column to varchar to allow any status value
        DB::statement("ALTER TABLE transactions MODIFY status VARCHAR(50) DEFAULT 'draft'");
        
        // Then update existing status values to new ones
        DB::statement("UPDATE transactions SET status = 'pemeriksaan_tahap_1' WHERE status = 'menunggu_pejabat_2'");
        DB::statement("UPDATE transactions SET status = 'pemeriksaan_tahap_2' WHERE status = 'menunggu_pejabat_3'");
        DB::statement("UPDATE transactions SET status = 'pemeriksaan_tahap_2' WHERE status = 'menunggu_pejabat_4'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to enum (optional)
        DB::statement("ALTER TABLE transactions MODIFY status ENUM(
            'draft',
            'menunggu_pejabat_1',
            'diskusi_pra_permohonan',
            'menunggu_pejabat_2',
            'pemeriksaan_tahap_2',
            'menunggu_pejabat_3',
            'dilengkapi',
            'menunggu_pejabat_4',
            'disetujui_pejabat_4',
            'diinformasikan',
            'selesai',
            'ditolak',
            'ajukan_ulang'
        ) DEFAULT 'draft'");
    }
};
