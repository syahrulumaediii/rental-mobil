<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Tambahkan ini di atas

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('pelanggan.notifikasi.index', compact('notifikasi'));
    }

    // PERBAIKAN: Ubah parameter dari (Notifikasi $notifikasi) menjadi ($id) biasa
    public function markRead($id)
    {
        // 1. Ambil data notifikasi secara manual berdasarkan ID dan pastikan milik user yang login
        $notifikasi = DB::table('notifikasi')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        // Jika tidak ditemukan, gagalkan akses
        if (!$notifikasi) {
            abort(403, 'Akses ditolak atau data tidak ditemukan.');
        }

        // 2. Jika read_at masih NULL, update langsung menggunakan query manual agar 100% masuk ke DB
        if (is_null($notifikasi->read_at)) {
            DB::table('notifikasi')
                ->where('id', $id)
                ->update(['read_at' => now()]);
        }

        // 3. Alihkan halaman sesuai kolom URL tujuan jika ada
        if ($notifikasi->url) {
            return redirect($notifikasi->url);
        }

        return back();
    }

    public function markAllRead()
    {
        Notifikasi::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }
}