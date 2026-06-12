<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{

    protected $table = 'fasilitas';

    protected $fillable = [
        'nama',
        'tipe',
        'deskripsi',
        'detail',
        'harga',
        'harga_bulanan',
        'paket_harian',
        'max_dewasa',
        'max_anak',
        'max_durasi_harian',
        'max_durasi_hari',
        'max_durasi_minggu',
        'max_durasi_bulan',
        'max_durasi_tahun',
        'jumlah_lapangan',
        'all_same',
        'jam_operasional',
        'image',
        'gallery',
        'labels',
        'harga_thumbnail',
    ];

    protected $casts = [
        'paket_harian' => 'array',
        'gallery'      => 'array',
        'labels'       => 'array',
        'all_same'     => 'boolean',
    ];

    public function bookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'fasilitas_id');
    }

    public function histories()
    {
        return $this->hasMany(HargaSewaHistory::class, 'fasilitas_id');
    }
}
