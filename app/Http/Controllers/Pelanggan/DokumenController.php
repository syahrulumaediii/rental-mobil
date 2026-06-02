<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\DokumenPelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DokumenController extends Controller
{
    public function index()
    {
        $pelanggan = Auth::user()->pelanggan;
        $dokumen   = $pelanggan?->dokumen()->latest()->get() ?? collect();

        return view('pelanggan.dokumen.index', compact('dokumen'));
    }

    public function create()
    {
        return view('pelanggan.dokumen.create');
    }

    public function store(Request $request)
    {
        $pelanggan = Auth::user()->pelanggan;

        if (! $pelanggan) {
            return back()->with('error', 'Lengkapi profil terlebih dahulu.');
        }

        $request->validate([
            'jenis_dokumen' => ['required', Rule::in(['ktp', 'sim', 'paspor', 'lainnya'])],
            'file'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Cek apakah dokumen jenis ini sudah ada, jika ada ganti yang lama
        $dokumenLama = $pelanggan->dokumen()
            ->where('jenis_dokumen', $request->jenis_dokumen)
            ->first();

        if ($dokumenLama) {
            Storage::disk('public')->delete($dokumenLama->file_path);
            $dokumenLama->delete();
        }

        $filePath = $request->file('file')->store('dokumen-pelanggan', 'public');

        DokumenPelanggan::create([
            'pelanggan_id'  => $pelanggan->id,
            'jenis_dokumen' => $request->jenis_dokumen,
            'file_path'     => $filePath,
            'status'        => 'pending',
        ]);

        return redirect()->route('pelanggan.dokumen.index')
            ->with('success', 'Dokumen berhasil diunggah. Menunggu verifikasi admin.');
    }

    public function destroy(DokumenPelanggan $dokumen)
    {
        // Pastikan hanya pemilik yang bisa hapus
        if ($dokumen->pelanggan->user_id !== Auth::id()) {
            abort(403);
        }

        if ($dokumen->status === 'verified') {
            return back()->with('error', 'Dokumen yang sudah terverifikasi tidak dapat dihapus.');
        }

        Storage::disk('public')->delete($dokumen->file_path);
        $dokumen->delete();

        return redirect()->route('pelanggan.dokumen.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }
}
