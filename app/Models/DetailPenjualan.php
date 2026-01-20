<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

    protected $table = 'detail_penjualan';
    protected $primaryKey = 'id';
    public $timestamps = false; // Berdasarkan query DB Anda

    protected $fillable = [
        'laporan_id',
        'menu_id',
        'jumlah_terjual',
        'subtotal',
    ];

    protected $casts = [
        'jumlah_terjual' => 'integer',
        'subtotal' => 'integer',
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanPenjualan::class, 'laporan_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}