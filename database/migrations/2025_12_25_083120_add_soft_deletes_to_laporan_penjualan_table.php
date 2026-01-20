<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_penjualan', function (Blueprint $blueprint) {
            // Menambahkan kolom deleted_at
            $blueprint->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('laporan_penjualan', function (Blueprint $blueprint) {
            // Menghapus kolom deleted_at jika migration di-rollback
            $blueprint->dropSoftDeletes();
        });
    }
};