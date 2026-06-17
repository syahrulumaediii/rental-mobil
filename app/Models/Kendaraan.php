<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kendaraan extends Model
{
    protected $table = 'kendaraan';

    protected $fillable = [
        'kategori_id',
        'nama',
        'merk',
        'model',
        'tahun',
        'plat_nomor',
        'warna',
        'kapasitas',
        'transmisi',
        'bahan_bakar',
        'tarif_harian',
        'denda_per_jam',
        'status',
        'foto',
        'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'tarif_harian' => 'decimal:2',
            'denda_per_jam' => 'decimal:2',
            'kapasitas' => 'integer',
            'tahun' => 'integer',
        ];
    }

    public function transaksiSewa()
    {
        // Parameter: (Model_Tujuan, Model_Perantara, Foreign_Key_Perantara_di_Tujuan, Foreign_Key_Asal_di_Perantara)
        return $this->hasManyThrough(TransaksiSewa::class, Booking::class, 'kendaraan_id', 'booking_id');
    }

    // ==================== RELASI ====================

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriKendaraan::class, 'kategori_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // ==================== HELPERS ====================

    public function isTersedia(): bool
    {
        return $this->status === 'aktif';
    }
}
