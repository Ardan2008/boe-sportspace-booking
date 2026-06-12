<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'penyewa_id',
        'fasilitas_id',
        'nomor_lapangan',
        'allocated_rooms',
        'tgl_mulai',
        'tgl_selesai',
        'package_type',
        'selected_days',
        'selected_packages',
        'total_harga',
        'status',
        'rejection_reason',
        'expired_at',
        'checkin_at'
    ];

    protected $casts = [
        'expired_at'        => 'datetime',
        'checkin_at'        => 'datetime',
        'allocated_rooms'   => 'array',
        'nomor_lapangan'       => 'array',
        'selected_packages' => 'array',
        'tgl_mulai'         => 'date',
        'tgl_selesai'       => 'date',
    ];

    public function penyewa()
    {
        return $this->belongsTo(Penyewa::class);
    }

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class);
    }
}
