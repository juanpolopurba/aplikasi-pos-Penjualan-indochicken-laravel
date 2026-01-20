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
            $table->decimal('stok', 10, 2)->default(0)->change();

            if (Schema::hasTable('inventory')) {
                Schema::table('inventory', function (Blueprint $table) {
                    $table->decimal('stok_saat_ini', 10, 2)->default(0)->change();
                });
            }
            //
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            $table->integer('stok')->default(0)->change();
            //
        });
    }
};
