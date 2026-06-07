<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\MetodePembayaran;
use App\Models\Pembayaran;
use App\Models\TransaksiSewa;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
        // public function index(Request $request)
        // {
        //     $query = Pembayaran::with(['transaksiSewa.booking.pelanggan.user', 'metodePembayaran'])
        //         ->latest();

        //     if ($request->filled('status')) {
        //         $query->where('status', $request->status);
        //     }

        //     $pembayaran = $query->paginate(15);

        //     return view('kasir.pembayaran.index', compact('pembayaran'));
        // }

    public function index(Request $request)
    {
        $query = TransaksiSewa::with(['booking.pelanggan.user', 'booking.kendaraan', 'deposit', 'pembayaran']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('kode_transaksi', 'like', "%{$request->search}%")
                ->orWhereHas('booking.pelanggan.user', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $transaksi = $query->latest()->paginate(15);

        return view('kasir.pembayaran.index', compact('transaksi'));
    }

    public function create(Request $request)
    {
        $transaksi = TransaksiSewa::findOrFail($request->transaksi_id);
        $metode    = MetodePembayaran::active()->get();

        return view('kasir.pembayaran.create', compact('transaksi', 'metode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaksi_id'         => 'required|exists:transaksi_sewa,id',
            'metode_pembayaran_id' => 'required|exists:metode_pembayaran,id',
            'jumlah_bayar'         => 'required|numeric|min:1',
            'bukti_transfer'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $buktiPath = $request->file('bukti_transfer')->store('bukti-transfer', 'public');
        }

        $transaksi = TransaksiSewa::findOrFail($request->transaksi_id);
        $sudahBayar = $transaksi->pembayaran()->where('status', 'berhasil')->sum('jumlah_bayar');

        // Logika penghitungan kembalian jika uang kasir berlebih
        $totalTagihan = ($transaksi->total_biaya + $transaksi->total_denda) - $sudahBayar;
        $kembalian  = max(0, $request->jumlah_bayar - $totalTagihan);

        Pembayaran::create([
            'transaksi_id'         => $request->transaksi_id,
            'metode_pembayaran_id' => $request->metode_pembayaran_id,
            'jumlah_bayar'         => $request->jumlah_bayar,
            'jumlah_kembali'       => $kembalian,
            'bukti_transfer'       => $buktiPath,
            'status'               => 'berhasil',
        ]);

        return redirect()->route('kasir.transaksi.show', $request->transaksi_id)
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    /**
     * BARU DI SINI: Menyinkronkan Route kasir.transaksi.bayar
     * Eksekusi pembayaran instan dari halaman detail transaksi (Form denda/pelunasan pengembalian)
     */
    public function bayarTransaksi(Request $request, TransaksiSewa $transaksi)
    {
        $request->validate([
            'metode_pembayaran_id' => 'required|exists:metode_pembayaran,id',
            'jumlah_bayar'         => 'required|numeric|min:1',
            'bukti_transfer'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $buktiPath = $request->file('bukti_transfer')->store('bukti-transfer', 'public');
        }

        $sudahBayar = $transaksi->pembayaran()->where('status', 'berhasil')->sum('jumlah_bayar');

        // Menghitung total tagihan riil (Sewa Pokok + Akumulasi Denda Lapangan)
        $totalTagihan = ($transaksi->total_biaya + $transaksi->total_denda) - $sudahBayar;
        $kembalian  = max(0, $request->jumlah_bayar - $totalTagihan);

        Pembayaran::create([
            'transaksi_id'         => $transaksi->id,
            'metode_pembayaran_id' => $request->metode_pembayaran_id,
            'jumlah_bayar'         => $request->jumlah_bayar,
            'jumlah_kembali'       => $kembalian,
            'bukti_transfer'       => $buktiPath,
            'status'               => 'berhasil',
        ]);

        return redirect()->route('kasir.transaksi.show', $transaksi->id)
            ->with('success', 'Pelunasan/Pembayaran denda berhasil disimpan.');
    }
}
