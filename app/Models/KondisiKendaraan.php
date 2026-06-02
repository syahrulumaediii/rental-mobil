<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KondisiKendaraan extends Model
{
    protected $table = 'kondisi_kendaraan';

    protected $fillable = [
        'transaksi_id',
        'waktu',
        'foto',
        'bahan_bakar',
        'km_odometer',
        'catatan_kondisi',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'km_odometer' => 'integer',
        ];
    }

    // ==================== RELASI ====================

    public function transaksiSewa(): BelongsTo
    {
        return $this->belongsTo(TransaksiSewa::class, 'transaksi_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
