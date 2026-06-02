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
        'kendaraan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'durasi_hari',
        'estimasi_biaya',
        'catatan',
        'status',
        'alasan_penolakan',
        'dikonfirmasi_oleh',
        'dikonfirmasi_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'dikonfirmasi_at' => 'datetime',
            'estimasi_biaya' => 'decimal:2',
            'durasi_hari' => 'integer',
        ];
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

    public function konfirmator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

    public function transaksiSewa(): HasOne
    {
        return $this->hasOne(TransaksiSewa::class);
    }
}
