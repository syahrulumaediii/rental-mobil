<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PesanController extends Controller
{
    public function index()
    {
        // Mengambil user yang rolenya 'pelanggan'
        $pelanggan = User::where('role', 'pelanggan')->orderBy('name', 'asc')->get();
        return view('admin.pesan.index', compact('pelanggan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tipe'    => 'required|in:booking,denda,dokumen,pembayaran,sistem',
            'judul'   => 'required|string|max:150',
            'isi'     => 'required|string',
        ]);

        // Menentukan URL redirect otomatis di sisi pelanggan berdasarkan tipenya
        $urlTujuan = match($request->tipe) {
            'dokumen'    => route('pelanggan.dokumen.index'),
            'booking', 'denda' => route('pelanggan.booking.index'),
            default      => null, // Pembayaran atau sistem ke halaman notifikasi saja
        };

        // Bersihkan prefix kurung jika admin mengetik manual agar tidak double
        $judulClean = preg_replace('/^\[.*?\]\s*/', '', $request->judul);
        // Gabungkan tipe ke judul sebagai pengenal: "[tipe] Judul Asli"
        $judulFinal = '[' . strtoupper($request->tipe) . '] ' . $judulClean;

        DB::table('notifikasi')->insert([
            'user_id'    => $request->user_id,
            'judul'      => $judulFinal,
            'pesan'      => $request->isi,
            'url'        => $urlTujuan,
            'read_at'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Pesan kategori ' . $request->tipe . ' berhasil dikirim.');
    }
}