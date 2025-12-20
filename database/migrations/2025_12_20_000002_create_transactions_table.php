<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Form Fields
            $table->string('nama_pemohon');
            $table->string('nama_perusahaan');
            $table->date('tanggal_pengajuan');
            $table->text('uraian_transaksi');
            $table->decimal('total', 15, 2);
            $table->text('dasar_transaksi')->nullable();
            $table->string('lawan_transaksi')->nullable();
            $table->string('rekening_transaksi')->nullable();
            $table->date('rencana_tanggal_transaksi')->nullable();
            $table->string('pengakuan_transaksi')->nullable();
            $table->text('keterangan')->nullable();
            
            // Status tracking
            $table->enum('status', [
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
            ])->default('draft');
            
            $table->text('alasan_penolakan')->nullable();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->timestamp('tanggal_ditolak')->nullable();
            
            // Attachment
            $table->string('lampiran_dokumen')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
