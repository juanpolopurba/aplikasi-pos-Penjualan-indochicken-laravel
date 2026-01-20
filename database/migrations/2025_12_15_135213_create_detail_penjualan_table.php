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
        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Laporan Penjualan
            $table->foreignId('laporan_id')->constrained('laporan_penjualan')->onUpdate('cascade')->onDelete('cascade');
            
            // Relasi ke Menu
            $table->foreignId('menu_id')->constrained('menu')->onUpdate('cascade')->onDelete('restrict');
            
            $table->integer('jumlah_terjual');
            $table->integer('subtotal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
    }
};
