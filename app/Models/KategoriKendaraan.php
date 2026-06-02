<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriKendaraan extends Model
{
    protected $table = 'kategori_kendaraan';

    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    // ==================== RELASI ====================

    public function kendaraan(): HasMany
    {
        return $this->hasMany(Kendaraan::class, 'kategori_id');
    }
}
