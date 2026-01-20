<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';
    protected $primaryKey = 'id';
    public $timestamps = false; // Berdasarkan query DB Anda, menu tidak punya created_at/updated_at

    protected $fillable = [
        'nama_menu',
        'harga',
        'is_active',
    ];

    protected $casts = [
        'harga' => 'integer',
        'is_active' => 'boolean',
    ];

    public function detailPenjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'menu_id');
    }
}