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
        Schema::create('cabang', function (Blueprint $table) {
            $table->id(); // id INT PRIMARY KEY AUTO_INCREMENT
            $table->string('nama_cabang', 100)->unique(); // RENAME COLUMN nama TO nama_cabang
            $table->text('alamat')->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabang');
    }
};
