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
        Schema::create('inventory', function (Blueprint $table) {
            $table->foreignId('bahan_id')->constrained('bahan_baku')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('cabang_id')->constrained('cabang')->onUpdate('cascade')->onDelete('cascade');

            $table->decimal('stok_saat_ini', 10, 2)->default(0.00);
            $table->decimal('harga_beli_rata_rata', 10, 2)->default(0.00);
            $table->timestamps(); // updated_at

            // Primary Key Komposit
            $table->primary(['bahan_id', 'cabang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
