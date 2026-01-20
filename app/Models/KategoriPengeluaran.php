<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'kategori_pengeluaran';
    
    protected $fillable = ['nama_kategori'];

    // Jika tabel Anda punya kolom created_at & updated_at, biarkan ini:
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Pengeluaran (Opsional tapi sangat berguna)
     * Satu kategori bisa punya banyak catatan pengeluaran
     */
    public function pengeluarans()
    {
        return $this->hasMany(Pengeluaran::class, 'kategori_id');
    }
}