<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
 
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Atribut yang dapat diisi (Mass Assignable).
     */
    protected $fillable = [
        'username',
        'password',
        'cabang_id', // WAJIB DITAMBAHKAN agar bisa menyimpan data cabang
        'name',      // Tambahkan jika Anda punya kolom nama
        'role',      // Tambahkan jika Anda punya kolom role
    ];

    /**
     * Atribut yang harus disembunyikan.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relasi ke model Cabang (Many to One).
     * Pastikan di tabel 'users' ada kolom 'cabang_id'.
     */
    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    /**
     * Penyesuaian untuk pencarian username (Jika menggunakan Passport/Custom Auth).
     */
    public function findForPassport(string $username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Casting atribut (Password otomatis di-hash).
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}