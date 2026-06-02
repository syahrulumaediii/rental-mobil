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
        'status',
        'foto',
        'deskripsi',
    ];

    protected function casts(): array
    {
        return [
            'tarif_harian' => 'decimal:2',
            'kapasitas' => 'integer',
            'tahun' => 'integer',
        ];
    }

    // ==================== RELASI ====================

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriKendaraan::class, 'kategori_id');
    }

    public function booking(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // ==================== HELPERS ====================

    public function isTersedia(): bool
    {
        return $this->status === 'tersedia';
    }
}
