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
        Schema::create('detail_pembelian_baku', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('pembelian_id')->constrained('pembelian_bahan_baku')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('bahan_id')->constrained('bahan_baku')->onUpdate('cascade')->onDelete('restrict');

            $table->decimal('kuantitas', 10, 2);
            $table->decimal('harga_satuan', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pembelian_baku');
    }
};
