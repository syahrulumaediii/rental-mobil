<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Denda extends Model
{
    protected $table = 'denda';

    protected $fillable = [
        'transaksi_id',
        'jenis_denda',
        'keterangan',
        'jumlah_jam_telat',
        'tarif_denda',
        'total_denda',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_jam_telat' => 'integer',
            'tarif_denda' => 'decimal:2',
            'total_denda' => 'decimal:2',
        ];
    }

    // ==================== RELASI ====================

    public function transaksiSewa(): BelongsTo
    {
        return $this->belongsTo(TransaksiSewa::class, 'transaksi_id');
    }
}
