<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianBahanBaku extends Model
{
    // Sesuaikan dengan nama tabel di database Anda
    protected $table = 'pembelian_bahan_baku'; 

    protected $fillable = ['tanggal', 'supplier', 'total_pembelian', 'cabang_id', 'user_id'];

    // Relasi ke detail pembelian
    public function details()
    {
        // Ganti 'DetailPembelian' sesuai nama model detail Anda
        return $this->hasMany(DetailPembelian::class, 'pembelian_id'); 
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}