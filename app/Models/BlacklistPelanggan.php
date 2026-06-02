<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlacklistPelanggan extends Model
{
    protected $table = 'blacklist_pelanggan';

    protected $fillable = [
        'pelanggan_id',
        'alasan',
        'ditambahkan_oleh',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ==================== RELASI ====================

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditambahkan_oleh');
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
