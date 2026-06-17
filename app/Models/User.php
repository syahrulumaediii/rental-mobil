<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // <-- Tambahkan import ini

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable; // <-- Tambahkan Notifiable di sini

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ==================== RELASI ====================

    public function pelanggan(): HasOne
    {
        return $this->hasOne(Pelanggan::class);
    }

    /**
     * Jika Anda menggunakan sistem notifikasi bawaan Laravel,
     * Anda bisa memanggil relasi ini untuk mengambil notifikasi di view pelanggan.
     */
    public function notifikasiKustom(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function notifikasi(): HasMany
    {
        // Pastikan nama Model Notifikasi Anda sesuai (misal: Notifikasi atau Notification)
        // Dan pastikan foreign key di tabel notifikasi adalah 'user_id'
        return $this->hasMany(Notifikasi::class, 'user_id');
    }



    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ==================== HELPERS ====================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function isPelanggan(): bool
    {
        return $this->role === 'pelanggan';
    }
}
