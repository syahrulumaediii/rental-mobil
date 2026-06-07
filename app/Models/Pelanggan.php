<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';

    protected $fillable = [
        'user_id',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'kota',
        'pekerjaan',
        'status_verifikasi',
        'foto_profil',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    // ==================== RELASI ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenPelanggan::class);
    }

    public function booking(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function blacklist(): HasMany
    {
        return $this->hasMany(BlacklistPelanggan::class);
    }

    // ==================== HELPERS ====================

    public function isVerified(): bool
    {
        return $this->status_verifikasi === 'verified';
    }

    public function isBlacklisted(): bool
    {
        return $this->blacklist()->where('is_active', true)->exists();
    }
}
