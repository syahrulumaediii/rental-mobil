<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['pelanggan.user', 'kendaraan', 'konfirmator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('kode_booking', 'like', "%{$request->search}%")
                ->orWhereHas('pelanggan.user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $booking = $query->latest()->paginate(15);

        return view('admin.booking.index', compact('booking'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['pelanggan.user', 'pelanggan.dokumen', 'kendaraan.kategori', 'konfirmator', 'transaksiSewa']);

        return view('admin.booking.show', compact('booking'));
    }

    public function approve(Request $request, Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses.');
        }

        if ($booking->pelanggan->isBlacklisted()) {
            return back()->with('error', 'Pelanggan ini ada dalam daftar blacklist.');
        }

        $booking->update([
            'status'           => 'disetujui',
            'dikonfirmasi_oleh' => Auth::id(),
            'dikonfirmasi_at'  => now(),
        ]);

        // Ubah status kendaraan menjadi tidak tersedia
        $booking->kendaraan->update(['status' => 'disewa']);

        return back()->with('success', 'Booking berhasil disetujui.');
    }

    public function reject(Request $request, Booking $booking)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking ini sudah diproses.');
        }

        $booking->update([
            'status'            => 'ditolak',
            'alasan_penolakan'  => $request->alasan_penolakan,
            'dikonfirmasi_oleh' => Auth::id(),
            'dikonfirmasi_at'   => now(),
        ]);

        return back()->with('success', 'Booking berhasil ditolak.');
    }
}
