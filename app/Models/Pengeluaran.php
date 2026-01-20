<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengeluaran extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $table = 'pengeluaran';

    protected $fillable = [
    'tanggal',
    'kategori_id',
    'cabang_id', // Pastikan ini ada
    'deskripsi',
    'jumlah',
    'user_id',
];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriPengeluaran::class, 'kategori_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}