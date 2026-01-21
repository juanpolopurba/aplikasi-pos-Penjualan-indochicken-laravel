<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {

            if (Schema::hasColumn('bahan_baku', 'stok')) {
                $table->dropColumn('stok');
            }

            if (Schema::hasColumn('bahan_baku', 'cabang_id')) {
                $table->dropColumn('cabang_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bahan_baku', function (Blueprint $table) {
            // optional: restore columns
        });
    }
};
