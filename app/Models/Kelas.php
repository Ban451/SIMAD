<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    
    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'jurusan',
    ];

    // Relasi ke user (siswa)
    public function siswa()
    {
        return $this->hasMany(User::class, 'kelas_id')->where('role', 'siswa');
    }

    // Relasi ke user (guru wali kelas)
    public function waliKelas()
    {
        return $this->hasOne(User::class, 'kelas_id')->where('role', 'guru');
    }

    // Relasi ke dokumen (via siswa)
    public function dokumen()
    {
        return $this->hasManyThrough(Dokumen::class, User::class, 'kelas_id', 'siswa_id');
    }
}