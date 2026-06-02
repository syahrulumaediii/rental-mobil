<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlacklistPelanggan;
use App\Models\DokumenPelanggan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::with('user');

        if ($request->filled('status_verifikasi')) {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
                ->orWhere('nik', 'like', "%{$request->search}%");
        }

        $pelanggan = $query->latest()->paginate(15);

        return view('admin.pelanggan.index', compact('pelanggan'));
    }

    public function show(Pelanggan $pelanggan)
    {
        $pelanggan->load(['user', 'dokumen.verifier', 'booking.kendaraan', 'blacklist.admin']);

        return view('admin.pelanggan.show', compact('pelanggan'));
    }

    // ===================== VERIFIKASI DOKUMEN =====================

    public function verifikasiDokumen(Request $request, DokumenPelanggan $dokumen)
    {
        $request->validate([
            'status'  => 'required|in:verified,rejected',
            'catatan' => 'nullable|string|max:500',
        ]);

        $dokumen->update([
            'status'      => $request->status,
            'catatan'     => $request->catatan,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);


        // Jika semua dokumen wajib sudah verified, update status pelanggan
        $pelanggan = Pelanggan::with('dokumen')
            ->find($dokumen->pelanggan_id);
        $semuaVerified = $pelanggan->dokumen()
            ->whereIn('jenis_dokumen', ['ktp', 'sim'])
            ->where('status', 'verified')
            ->count() === 2;

        if ($semuaVerified) {
            $pelanggan->update(['status_verifikasi' => 'verified']);
            $pelanggan->refresh();
        }

        // dd(
        //     $dokumen->pelanggan_id,
        //     $pelanggan->status_verifikasi,
        //     $pelanggan->dokumen()->get()
        // );

        return back()->with('success', 'Dokumen berhasil diperbarui.');
    }

    // ===================== BLACKLIST =====================

    public function blacklist(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'alasan' => 'required|string|max:500',
        ]);

        // Nonaktifkan blacklist lama jika ada
        BlacklistPelanggan::where('pelanggan_id', $pelanggan->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        BlacklistPelanggan::create([
            'pelanggan_id'     => $pelanggan->id,
            'alasan'           => $request->alasan,
            'ditambahkan_oleh' => Auth::id(),
            'is_active'        => true,
        ]);

        return back()->with('success', 'Pelanggan berhasil dimasukkan ke blacklist.');
    }

    public function unblacklist(Pelanggan $pelanggan)
    {
        BlacklistPelanggan::where('pelanggan_id', $pelanggan->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return back()->with('success', 'Pelanggan berhasil dihapus dari blacklist.');
    }
}
