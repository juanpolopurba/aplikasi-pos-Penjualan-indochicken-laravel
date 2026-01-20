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
        Schema::create('laporan_penjualan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('cabang_id')->constrained('cabang')->onUpdate('cascade')->onDelete('restrict');
            $table->integer('total_penjualan')->default(0); // Sama dengan total_pendapatan Anda sebelumnya
            $table->text('catatan')->nullable(); // Opsional
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_penjualan');
    }
};
