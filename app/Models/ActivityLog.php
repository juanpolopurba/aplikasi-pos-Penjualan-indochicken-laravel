<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivityLog extends Model
{
    use HasFactory;

    // WAJIB ADA: Agar Laravel izinkan simpan data ke kolom ini
    protected $fillable = [
        'user_id', 
        'aksi', 
        'keterangan', 
        'ip_address'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Fungsi pembantu untuk mencatat log
    public static function record($aksi, $keterangan)
    {
        return self::create([
            'user_id'    => auth()->id(),
            'aksi'       => $aksi,
            'keterangan' => $keterangan,
            'ip_address' => request()->ip(),
        ]);
    }
}