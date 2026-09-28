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

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function siswa()
    {
        return $this->hasMany(User::class)->where('role', 'siswa');
    }

    public function waliKelas()
    {
        return $this->hasOne(User::class)->where('role', 'guru');
    }
}