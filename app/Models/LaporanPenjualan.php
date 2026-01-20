<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // WAJIB ADA: Import ini agar kolom deleted_at terbaca

class LaporanPenjualan extends Model
{
    use HasFactory, SoftDeletes; // Gabungkan di sini

    protected $table = 'laporan_penjualan';

    protected $primaryKey = 'id';

    // Jika tabel Anda benar-benar tidak punya created_at & updated_at,
    // SoftDeletes tetap butuh timestamps aktif secara sistem untuk kolom deleted_at.
    public $timestamps = true;

    protected $fillable = [
        'tanggal',
        'cabang_id',
        'total_penjualan',
        'catatan',
        'user_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_penjualan' => 'integer',
        'deleted_at' => 'datetime', // Tambahkan cast untuk deleted_at
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function details()
    {
        // Pastikan nama kolom di tabel detail_penjualan adalah 'laporan_penjualan_id'
        return $this->hasMany(DetailPenjualan::class, 'laporan_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
