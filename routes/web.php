<?php

use Illuminate\Support\Facades\Route;

// =====================================================================
// AUTH
// =====================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login',    [App\Http\Controllers\Auth\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\Auth\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\AuthController::class, 'register']);
});

Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// =====================================================================
// ADMIN  (role: admin)
// =====================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // User (admin & kasir)
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)->except(['show']);
    Route::get('users/{user}',              [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::patch('users/{user}/toggle-active', [App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle-active');

    // Kendaraan
    Route::resource('kendaraan', App\Http\Controllers\Admin\KendaraanController::class);

    // Kategori Kendaraan
    Route::resource('kategori-kendaraan', App\Http\Controllers\Admin\KategoriKendaraanController::class)->except(['show']);

    // Booking
    Route::get('booking',                        [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('booking.index');
    Route::get('booking/{booking}',              [App\Http\Controllers\Admin\BookingController::class, 'show'])->name('booking.show');
    Route::patch('booking/{booking}/approve',    [App\Http\Controllers\Admin\BookingController::class, 'approve'])->name('booking.approve');
    Route::patch('booking/{booking}/reject',     [App\Http\Controllers\Admin\BookingController::class, 'reject'])->name('booking.reject');

    // Pelanggan
    Route::get('pelanggan',                                              [App\Http\Controllers\Admin\PelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('pelanggan/{pelanggan}',                                  [App\Http\Controllers\Admin\PelangganController::class, 'show'])->name('pelanggan.show');
    Route::patch('dokumen/{dokumen}/verifikasi',                         [App\Http\Controllers\Admin\PelangganController::class, 'verifikasiDokumen'])->name('dokumen.verifikasi');
    Route::post('pelanggan/{pelanggan}/blacklist',                       [App\Http\Controllers\Admin\PelangganController::class, 'blacklist'])->name('pelanggan.blacklist');
    Route::delete('pelanggan/{pelanggan}/blacklist',                     [App\Http\Controllers\Admin\PelangganController::class, 'unblacklist'])->name('pelanggan.unblacklist');

    // Metode Pembayaran
    Route::resource('metode-pembayaran', App\Http\Controllers\Admin\MetodePembayaranController::class)->except(['show']);

    // Laporan
    Route::get('laporan/pendapatan', [App\Http\Controllers\Admin\LaporanController::class, 'pendapatan'])->name('laporan.pendapatan');
    Route::get('laporan/kendaraan',  [App\Http\Controllers\Admin\LaporanController::class, 'kendaraan'])->name('laporan.kendaraan');
    Route::get('laporan/audit-log',  [App\Http\Controllers\Admin\LaporanController::class, 'auditLog'])->name('laporan.audit-log');
});

// =====================================================================
// KASIR  (role: kasir)
// =====================================================================
Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'role:kasir'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Kasir\DashboardController::class, 'index'])->name('dashboard');

    // Transaksi Sewa
    Route::get('transaksi',                                 [App\Http\Controllers\Kasir\TransaksiSewaController::class, 'index'])->name('transaksi.index');
    Route::get('transaksi/{transaksi}',                     [App\Http\Controllers\Kasir\TransaksiSewaController::class, 'show'])->name('transaksi.show');
    Route::get('booking/{booking}/serah-terima',            [App\Http\Controllers\Kasir\TransaksiSewaController::class, 'formSerahTerima'])->name('transaksi.form-serah-terima');
    Route::post('booking/{booking}/serah-terima',           [App\Http\Controllers\Kasir\TransaksiSewaController::class, 'prosesSerahTerima'])->name('transaksi.proses-serah-terima');
    Route::get('transaksi/{transaksi}/pengembalian',        [App\Http\Controllers\Kasir\TransaksiSewaController::class, 'formPengembalian'])->name('transaksi.form-pengembalian');
    Route::post('transaksi/{transaksi}/pengembalian',       [App\Http\Controllers\Kasir\TransaksiSewaController::class, 'prosesPengembalian'])->name('transaksi.proses-pengembalian');

    // Pembayaran
    Route::get('pembayaran',         [App\Http\Controllers\Kasir\PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('pembayaran/create',  [App\Http\Controllers\Kasir\PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('pembayaran',        [App\Http\Controllers\Kasir\PembayaranController::class, 'store'])->name('pembayaran.store');

    // Booking
    Route::get('booking',                        [App\Http\Controllers\Kasir\BookingController::class, 'index'])->name('booking.index');
    Route::get('booking/{booking}',              [App\Http\Controllers\Kasir\BookingController::class, 'show'])->name('booking.show');
    Route::patch('booking/{booking}/approve',    [App\Http\Controllers\Kasir\BookingController::class, 'approve'])->name('booking.approve');
    Route::patch('booking/{booking}/reject',     [App\Http\Controllers\Kasir\BookingController::class, 'reject'])->name('booking.reject');
});

// =====================================================================
// PELANGGAN  (role: pelanggan)
// =====================================================================
Route::prefix('pelanggan')->name('pelanggan.')->middleware(['auth', 'role:pelanggan'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Pelanggan\DashboardController::class, 'index'])->name('dashboard');

    // Profil
    Route::get('profil',             [App\Http\Controllers\Pelanggan\ProfilController::class, 'show'])->name('profil.show');
    Route::get('profil/edit',        [App\Http\Controllers\Pelanggan\ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('profil',             [App\Http\Controllers\Pelanggan\ProfilController::class, 'update'])->name('profil.update');
    Route::get('profil/password',    [App\Http\Controllers\Pelanggan\ProfilController::class, 'editPassword'])->name('profil.edit-password');
    Route::put('profil/password',    [App\Http\Controllers\Pelanggan\ProfilController::class, 'updatePassword'])->name('profil.update-password');

    // Dokumen
    Route::get('dokumen',            [App\Http\Controllers\Pelanggan\DokumenController::class, 'index'])->name('dokumen.index');
    Route::get('dokumen/upload',     [App\Http\Controllers\Pelanggan\DokumenController::class, 'create'])->name('dokumen.create');
    Route::post('dokumen',           [App\Http\Controllers\Pelanggan\DokumenController::class, 'store'])->name('dokumen.store');
    Route::delete('dokumen/{dokumen}', [App\Http\Controllers\Pelanggan\DokumenController::class, 'destroy'])->name('dokumen.destroy');

    // Booking
    Route::get('katalog',            [App\Http\Controllers\Pelanggan\BookingController::class, 'katalog'])->name('katalog');
    Route::get('booking',            [App\Http\Controllers\Pelanggan\BookingController::class, 'index'])->name('booking.index');
    Route::get('booking/buat/{kendaraan}', [App\Http\Controllers\Pelanggan\BookingController::class, 'create'])->name('booking.create');
    Route::post('booking',           [App\Http\Controllers\Pelanggan\BookingController::class, 'store'])->name('booking.store');
    Route::get('booking/{booking}',  [App\Http\Controllers\Pelanggan\BookingController::class, 'show'])->name('booking.show');
    Route::patch('booking/{booking}/cancel', [App\Http\Controllers\Pelanggan\BookingController::class, 'cancel'])->name('booking.cancel');

    // Notifikasi
    Route::get('notifikasi',                          [App\Http\Controllers\Pelanggan\NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::patch('notifikasi/{notifikasi}/read',      [App\Http\Controllers\Pelanggan\NotifikasiController::class, 'markRead'])->name('notifikasi.read');
    Route::patch('notifikasi/read-all',               [App\Http\Controllers\Pelanggan\NotifikasiController::class, 'markAllRead'])->name('notifikasi.read-all');
});
