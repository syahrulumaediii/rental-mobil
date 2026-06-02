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
        Schema::create('blacklist_pelanggan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelanggan_id')
                ->unique()
                ->constrained('pelanggan')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->text('alasan');
            $table->foreignId('ditambahkan_oleh')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active', 'idx_blacklist_is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blacklist_pelanggan');
    }
};
