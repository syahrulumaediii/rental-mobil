<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


// Redirect otomatis jika membuka root URL
Route::get('/', function () {
    if (Auth::check()) {
        return match (Auth::user()->role) {
            'admin'     => redirect()->route('admin.dashboard'),
            'kasir'     => redirect()->route('kasir.dashboard'),
            'pelanggan' => redirect()->route('pelanggan.dashboard'),
            default     => redirect('/login'),
        };
    }
    return redirect('/login');
});

// =====================================================================
// AUTHENTICATION (GUEST & AUTH)
// =====================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login',     [App\Http\Controllers\Auth\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [App\Http\Controllers\Auth\AuthController::class, 'login']);
    Route::get('/register',  [App\Http\Controllers\Auth\AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\AuthController::class, 'register']);
});

Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// =====================================================================
// ADMIN & OWNER (role: admin)
// =====================================================================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // User Management (Admin & Kasir)
    Route::resource('users', App\Http\Controllers\Admin\UserController::class)->except(['show']);
    Route::get('users/{user}',                 [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::patch('users/{user}/toggle-active', [App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle-active');

    // Armada Kendaraan & Kategori
    Route::resource('kendaraan', App\Http\Controllers\Admin\KendaraanController::class);


    // Transaksi Sewa & Validasi Booking
    Route::resource('transaksi', App\Http\Controllers\Admin\TransaksiSewaController::class)->only(['index', 'show', 'destroy']);
    Route::get('transaksi',                           [App\Http\Controllers\Admin\TransaksiSewaController::class, 'index'])->name('transaksi.index');
    Route::get('transaksi/{transaksi}',               [App\Http\Controllers\Admin\TransaksiSewaController::class, 'show'])->name('transaksi.show');

    // Jalur Serah Terima Aktif Admin
    Route::get('booking/{booking}/serah-terima', [App\Http\Controllers\Admin\TransaksiSewaController::class, 'formSerahTerima'])->name('transaksi.serah-terima');
    Route::post('booking/{booking}/serah-terima', [App\Http\Controllers\Admin\TransaksiSewaController::class, 'prosesSerahTerima'])->name('transaksi.proses-serah-terima');


    // Jalur Pengembalian Aktif Admin
    Route::get('transaksi/{transaksi}/pengembalian', [App\Http\Controllers\Admin\TransaksiSewaController::class, 'formPengembalian'])->name('transaksi.form-pengembalian');
    Route::post('transaksi/{transaksi}/pengembalian', [App\Http\Controllers\Admin\TransaksiSewaController::class, 'prosesPengembalian'])->name('transaksi.proses-pengembalian');


    // Validasi Booking Sisi Admin/Owner (Disinkronkan: approve & reject)
    Route::get('booking',                      [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('booking.index');
    Route::get('booking/{booking}',            [App\Http\Controllers\Admin\BookingController::class, 'show'])->name('booking.show');
    Route::patch('booking/{booking}/disetujui',  [App\Http\Controllers\Admin\BookingController::class, 'disetujui'])->name('booking.disetujui');
    Route::patch('booking/{booking}/ditolak',   [App\Http\Controllers\Admin\BookingController::class, 'ditolak'])->name('booking.ditolak');



    // Walk-In Admin
    Route::get('walk-in',               [App\Http\Controllers\Admin\WalkInController::class, 'step1'])->name('walkin.step1');
    Route::post('walk-in/cari',         [App\Http\Controllers\Admin\WalkInController::class, 'cariPelanggan'])->name('walkin.cari');
    Route::post('walk-in/step2',        [App\Http\Controllers\Admin\WalkInController::class, 'step2'])->name('walkin.step2');
    Route::post('walk-in/store',        [App\Http\Controllers\Admin\WalkInController::class, 'store'])->name('walkin.store');

    // Manajemen Pelanggan, Dokumen & Blacklist
    Route::get('pelanggan',                             [App\Http\Controllers\Admin\PelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('pelanggan/{pelanggan}',                 [App\Http\Controllers\Admin\PelangganController::class, 'show'])->name('pelanggan.show');
    Route::patch('dokumen/{dokumen}/verifikasi',        [App\Http\Controllers\Admin\PelangganController::class, 'verifikasiDokumen'])->name('dokumen.verifikasi');
    Route::post('pelanggan/{pelanggan}/blacklist',       [App\Http\Controllers\Admin\PelangganController::class, 'blacklist'])->name('pelanggan.blacklist');
    Route::delete('pelanggan/{pelanggan}/blacklist',     [App\Http\Controllers\Admin\PelangganController::class, 'unblacklist'])->name('pelanggan.unblacklist');

    // Fitur Admin Mengirim Pesan / Notifikasi Dokumen Kurang ke Pelanggan
    Route::get('kirim-pesan',  [App\Http\Controllers\Admin\PesanController::class, 'index'])->name('pesan.index');
    Route::post('kirim-pesan', [App\Http\Controllers\Admin\PesanController::class, 'store'])->name('pesan.store');

    // Kategori Kendaraan
    Route::resource('kategori-kendaraan', App\Http\Controllers\Admin\KategoriKendaraanController::class);

    // Pengaturan Finansial & Metode Pembayaran
    Route::resource('metode-pembayaran', App\Http\Controllers\Admin\MetodePembayaranController::class)->except(['show']);

    // Laporan Keuangan & Audit
    Route::get('laporan/pendapatan', [App\Http\Controllers\Admin\LaporanController::class, 'pendapatan'])->name('laporan.pendapatan');
    Route::get('/laporan/kendaraan', [App\Http\Controllers\Admin\LaporanKendaraanController::class, 'index'])->name('laporan.kendaraan');
    Route::get('laporan/audit-log',  [App\Http\Controllers\Admin\LaporanController::class, 'auditLog'])->name('laporan.audit-log');
});

// =====================================================================
// KASIR / FRONT OFFICE (role: kasir)
// =====================================================================
Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'role:kasir'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Kasir\DashboardController::class, 'index'])->name('dashboard');

    // Kelola Transaksi Sewa & Riwayat Lapangan
    Route::get('transaksi',                           [App\Http\Controllers\Kasir\TransaksiSewaController::class, 'index'])->name('transaksi.index');
    Route::get('transaksi/{transaksi}',               [App\Http\Controllers\Kasir\TransaksiSewaController::class, 'show'])->name('transaksi.show');

    // Jembatan Serah Terima: Sesuai dengan tag href di Blade index kasir
    Route::get('booking/{booking}/serah-terima',      [App\Http\Controllers\Kasir\TransaksiSewaController::class, 'formSerahTerima'])->name('transaksi.serah-terima');
    Route::post('booking/{booking}/serah-terima',     [App\Http\Controllers\Kasir\TransaksiSewaController::class, 'prosesSerahTerima'])->name('transaksi.proses-serah-terima');

    // Alur Pengembalian Mobil & Input Denda/Kondisi Akhir
    Route::get('transaksi/{transaksi}/pengembalian',  [App\Http\Controllers\Kasir\TransaksiSewaController::class, 'formPengembalian'])->name('transaksi.form-pengembalian');
    Route::post('transaksi/{transaksi}/pengembalian', [App\Http\Controllers\Kasir\TransaksiSewaController::class, 'prosesPengembalian'])->name('transaksi.proses-pengembalian');

    // Pembayaran Khusus Transaksi (Denda / Pelunasan saat mobil kembali)
    Route::post('transaksi/{transaksi}/pembayaran',   [App\Http\Controllers\Kasir\PembayaranController::class, 'bayarTransaksi'])->name('transaksi.bayar');

    // Pencatatan Pembayaran Umum Buku Kas (DP manual diluar booking, dll)
    Route::get('pembayaran',         [App\Http\Controllers\Kasir\PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('pembayaran/create',  [App\Http\Controllers\Kasir\PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('pembayaran',        [App\Http\Controllers\Kasir\PembayaranController::class, 'store'])->name('pembayaran.store');

    // Kelola Antrean Booking Sisi Kasir (Disinkronkan: approve & reject)
    Route::get('booking',                      [App\Http\Controllers\Kasir\BookingController::class, 'index'])->name('booking.index');
    Route::get('booking/{booking}',            [App\Http\Controllers\Kasir\BookingController::class, 'show'])->name('booking.show');
    Route::patch('booking/{booking}/disetujui',  [App\Http\Controllers\Kasir\BookingController::class, 'disetujui'])->name('booking.disetujui');
    Route::patch('booking/{booking}/ditolak',   [App\Http\Controllers\Kasir\BookingController::class, 'ditolak'])->name('booking.ditolak');

    // Walk-In: Pelanggan Datang Langsung ke Tempat (tanpa booking online)
    Route::get('walk-in',               [App\Http\Controllers\Kasir\WalkInController::class, 'step1'])->name('walkin.step1');
    Route::post('walk-in/cari',         [App\Http\Controllers\Kasir\WalkInController::class, 'cariPelanggan'])->name('walkin.cari');
    Route::post('walk-in/step2',        [App\Http\Controllers\Kasir\WalkInController::class, 'step2'])->name('walkin.step2');
    Route::post('walk-in/store',        [App\Http\Controllers\Kasir\WalkInController::class, 'store'])->name('walkin.store');
});

// =====================================================================
// PELANGGAN / CUSTOMER PORTAL (role: pelanggan)
// =====================================================================
Route::prefix('pelanggan')->name('pelanggan.')->middleware(['auth', 'role:pelanggan'])->group(function () {

    Route::get('/dashboard', [App\Http\Controllers\Pelanggan\DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Akun Profil
    Route::get('profil',             [App\Http\Controllers\Pelanggan\ProfilController::class, 'show'])->name('profil.show');
    Route::get('profil/edit',        [App\Http\Controllers\Pelanggan\ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('profil',             [App\Http\Controllers\Pelanggan\ProfilController::class, 'update'])->name('profil.update');
    Route::get('profil/password',    [App\Http\Controllers\Pelanggan\ProfilController::class, 'editPassword'])->name('profil.edit-password');
    Route::put('profil/password',    [App\Http\Controllers\Pelanggan\ProfilController::class, 'updatePassword'])->name('profil.update-password');

    // Upload Persyaratan Berkas (KTP/SIM)
    Route::get('dokumen',              [App\Http\Controllers\Pelanggan\DokumenController::class, 'index'])->name('dokumen.index');
    Route::get('dokumen/upload',       [App\Http\Controllers\Pelanggan\DokumenController::class, 'create'])->name('dokumen.create');
    Route::post('dokumen',             [App\Http\Controllers\Pelanggan\DokumenController::class, 'store'])->name('dokumen.store');
    Route::delete('dokumen/{dokumen}', [App\Http\Controllers\Pelanggan\DokumenController::class, 'destroy'])->name('dokumen.destroy');

    // Katalog Sewa Mobil & Pembuatan Reservasi/Booking
    Route::get('katalog',                  [App\Http\Controllers\Pelanggan\BookingController::class, 'katalog'])->name('katalog');
    Route::get('booking',                  [App\Http\Controllers\Pelanggan\BookingController::class, 'index'])->name('booking.index');
    Route::get('booking/buat/{kendaraan}', [App\Http\Controllers\Pelanggan\BookingController::class, 'create'])->name('booking.create');
    Route::post('booking',                 [App\Http\Controllers\Pelanggan\BookingController::class, 'store'])->name('booking.store');
    Route::get('booking/{booking}',        [App\Http\Controllers\Pelanggan\BookingController::class, 'show'])->name('booking.show');
    Route::patch('booking/{booking}/cancel', [App\Http\Controllers\Pelanggan\BookingController::class, 'cancel'])->name('booking.cancel');

    // Notifikasi & Pesan Masuk Sistem (SINKRON DENGAN CONTROLLER ANDA)
    Route::get('notifikasi',                    [App\Http\Controllers\Pelanggan\NotifikasiController::class, 'index'])->name('notifikasi.index');
    // UBAH DARI PATCH MENJADI GET AGAR BISA DIKLIK OLEH LINK <a>
    Route::get('notifikasi/{notifikasi}/read',  [App\Http\Controllers\Pelanggan\NotifikasiController::class, 'markRead'])->name('notifikasi.read');
    // Yang ini tetap PATCH karena dipicu dari Tombol Form
    Route::patch('notifikasi/read-all',         [App\Http\Controllers\Pelanggan\NotifikasiController::class, 'markAllRead'])->name('notifikasi.read-all');
});
