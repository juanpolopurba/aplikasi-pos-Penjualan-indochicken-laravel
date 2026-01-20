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
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->dropColumn(['stok', 'cabang_id']);
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->decimal('stok', 10, 2)->default(0);
            $table->unsignedBigInteger('cabang_id')->nullable();
        });
    }
};
