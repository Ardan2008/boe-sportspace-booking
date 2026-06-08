<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalBlokir extends Model
{
    protected $table = 'jadwal_blokir';

    protected $fillable = [
        'fasilitas_id',
        'tgl_mulai',
        'tgl_selesai',
        'tipe',
        'nama_pic',
        'divisi',
        'whatsapp',
        'durasi',
        'catatan',
        'tujuan',
        'nomor_kamar',
        'created_by',
    ];

    protected $casts = [
        'tgl_mulai'   => 'date',
        'tgl_selesai' => 'date',
        'nomor_kamar' => 'array',
    ];

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class);
    }
}
