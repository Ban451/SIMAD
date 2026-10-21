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

    /**
     * Semua user (guru + siswa) yang terdaftar di kelas ini.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'kelas_id');
    }

    /**
     * Siswa yang terdaftar di kelas ini.
     */
    public function siswa()
    {
        return $this->hasMany(User::class, 'kelas_id')->where('role', 'siswa');
    }

    /**
     * Wali kelas (guru) untuk kelas ini.
     * hasOne + where role=guru → hanya 1 guru per kelas.
     */
    public function waliKelas()
    {
        return $this->hasOne(User::class, 'kelas_id')->where('role', 'guru');
    }
}