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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->onDelete('cascade');
            $table->string('uraian_transaksi');
            $table->decimal('total', 15, 2);
            $table->text('dasar_transaksi')->nullable();
            $table->string('lawan_transaksi')->nullable();
            $table->string('rekening_transaksi')->nullable();
            $table->date('rencana_tanggal_transaksi')->nullable();
            $table->string('pengakuan_transaksi')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
