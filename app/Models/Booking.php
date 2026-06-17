<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $table = 'booking';

    protected $fillable = [
        'kode_booking',
        'pelanggan_id',
        'sumber_booking',   // penambahan
        'kendaraan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_hari',
        'estimasi_biaya',
        'catatan',
        'status',
        'alasan_penolakan',
        'disetujui_oleh',
        'disetujui_at',
        'dibuat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'disetujui_at' => 'datetime',
            'estimasi_biaya' => 'decimal:2',
            'durasi_hari' => 'integer',
        ];
    }


    // Format tanggal Indonesia
    public function getTanggalMulaiIndoAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal_mulai)->translatedFormat('d F Y');
    }

    public function getTanggalSelesaiIndoAttribute()
    {
        return \Carbon\Carbon::parse($this->tanggal_selesai)->translatedFormat('d F Y');
    }

    // ==================== RELASI ====================

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function dibuatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function transaksiSewa(): HasOne
    {
        return $this->hasOne(TransaksiSewa::class);
    }
}
