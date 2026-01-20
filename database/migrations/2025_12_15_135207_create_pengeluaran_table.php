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
        Schema::create('pengeluaran', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            
            // Relasi ke Kategori Pengeluaran
            $table->foreignId('kategori_id')->constrained('kategori_pengeluaran')->onUpdate('cascade')->onDelete('restrict');
            
            $table->foreignId('cabang_id')->constrained('cabang');
            
            $table->string('deskripsi', 255)->nullable();
            $table->decimal('jumlah', 12, 2);
            
            // Relasi ke User
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran');
    }
};
