<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('kas_kecil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabang_id')->constrained('cabang');
            $table->foreignId('user_id')->constrained('users'); // Kasir yang input
            // 'masuk' untuk Modal Awal, 'keluar' untuk biaya operasional
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->decimal('jumlah', 15, 2);
            $table->string('keterangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_kecils');
    }
};
