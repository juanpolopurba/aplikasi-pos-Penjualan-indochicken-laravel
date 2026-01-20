<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $table = 'cabang';
    protected $primaryKey = 'id';
    public $timestamps = true; // Asumsi menggunakan created_at/updated_at default Laravel

    protected $fillable = [
        'nama_cabang',
        'alamat',
        'is_aktif',
    ];

    public function laporanPenjualan()
    {
        return $this->hasMany(LaporanPenjualan::class, 'cabang_id');
    }
}