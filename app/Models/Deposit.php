<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    protected $table = 'deposit';

    protected $fillable = [
        'transaksi_id',
        'jumlah',
        'status',
        'jumlah_dipotong',
        'alasan_potongan',
        'dikembalikan_at',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'jumlah_dipotong' => 'decimal:2',
            'dikembalikan_at' => 'datetime',
        ];
    }

    // ==================== RELASI ====================

    public function transaksiSewa(): BelongsTo
    {
        return $this->belongsTo(TransaksiSewa::class, 'transaksi_id');
    }
}
