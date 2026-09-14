<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenRequest extends Model
{
    use HasFactory;

    protected $table = 'dokumen_requests';
    
    protected $fillable = [
        'siswa_id',
        'kategori_id',
        'judul',
        'keterangan',
        'status',
        'alasan',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriDokumen::class, 'kategori_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}