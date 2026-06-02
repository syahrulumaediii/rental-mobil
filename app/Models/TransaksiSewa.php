<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransaksiSewa extends Model
{
    protected $table = 'transaksi_sewa';

    protected $fillable = [
        'kode_transaksi',
        'booking_id',
        'kasir_id',
        'tanggal_ambil_aktual',
        'tanggal_kembali_aktual',
        'total_biaya',
        'total_denda',
        'total_bayar',
        'status',
        'catatan_kasir',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_ambil_aktual' => 'datetime',
            'tanggal_kembali_aktual' => 'datetime',
            'total_biaya' => 'decimal:2',
            'total_denda' => 'decimal:2',
            'total_bayar' => 'decimal:2',
        ];
    }

    // ==================== RELASI ====================

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }

    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'transaksi_id');
    }

    public function deposit(): HasOne
    {
        return $this->hasOne(Deposit::class, 'transaksi_id');
    }

    public function denda(): HasMany
    {
        return $this->hasMany(Denda::class, 'transaksi_id');
    }

    public function kondisiKendaraan(): HasMany
    {
        return $this->hasMany(KondisiKendaraan::class, 'transaksi_id');
    }
}
