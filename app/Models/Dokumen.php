<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $table = 'dokumen';
    
    protected $fillable = [
        'judul',
        'kategori_id',
        'siswa_id',
        'uploaded_by',
        'file_path',
        'nama_file',
        'status',
        'keterangan',
        'semester',
        'tahun_ajaran',
    ];

    // Relasi ke siswa (User)
    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    // Relasi ke kategori
    public function kategori()
    {
        return $this->belongsTo(KategoriDokumen::class, 'kategori_id');
    }

    // Relasi ke pengupload (User)
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Scope untuk filter
    public function scopeByKelas($query, $kelasId)
    {
        return $query->whereHas('siswa', function($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        });
    }

    public function scopeBySiswa($query, $siswaId)
    {
        return $query->where('siswa_id', $siswaId);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}