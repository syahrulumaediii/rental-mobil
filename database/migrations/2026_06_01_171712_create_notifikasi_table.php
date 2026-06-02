<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('judul');
            $table->text('pesan');
            $table->enum('tipe', [
                'booking',
                'dokumen',
                'pembayaran',
                'denda',
                'blacklist',
                'sistem',
            ])->default('sistem');
            $table->timestamp('read_at')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();

            $table->index('user_id', 'idx_notifikasi_user');
            $table->index('read_at', 'idx_notifikasi_read');
            $table->index('tipe', 'idx_notifikasi_tipe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
