@extends('layouts.app')

@section('title', 'Proses Pengembalian (Admin Mode)')
@section('page-title', 'Proses Pengembalian Kendaraan')
@section('breadcrumb', 'Admin / Transaksi / Pengembalian')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')

@vite('resources/js/transaksi/pengembalian.js')

<div class="max-w-3xl mx-auto space-y-5">

    @php
        $pembayaranSewa = $transaksi->pembayaran->where('status', 'lunas')->sortBy('created_at')->first();
        $sewaSudahLunas = $pembayaranSewa !== null;
        $jumlahDeposit  = $transaksi->deposit?->jumlah ?? 0;
        $batasKembali   = $transaksi->booking->tanggal_selesai;
        $sekarang       = now();
        $telat          = $sekarang->gt($batasKembali);
        $hariTelat      = $telat ? (int) $sekarang->diffInDays($batasKembali) : 0;
    @endphp

    {{-- INFO TRANSAKSI --}}
    <div class="card p-5">
        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
            <i data-lucide="info" class="w-5 h-5 text-primary-600"></i> Rangkuman Sewa Aktif
        </h3>
        <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm">
            <div>
                <p class="text-slate-400 text-xs">Kode Transaksi / Unit</p>
                <p class="font-semibold text-slate-700 font-mono mt-0.5">{{ $transaksi->kode_transaksi }} / {{ $transaksi->booking->kendaraan->nama }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs">Penyewa</p>
                <p class="font-semibold text-slate-700 mt-0.5">{{ $transaksi->booking->pelanggan->user->name }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs">Batas Waktu Pengembalian Resmi</p>
                <p class="font-semibold text-slate-700 mt-0.5">{{ $batasKembali?->format('d M Y, H:i') }} WIB</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs">Status Keterlambatan Sistem</p>
                @if($telat)
                <p class="font-bold text-red-600 mt-0.5 flex items-center gap-1">
                    <i data-lucide="clock" class="w-4 h-4"></i> Terlambat ± {{ $hariTelat }} Hari
                </p>
                @else
                <p class="font-bold text-emerald-600 mt-0.5">Aman (Tepat Waktu)</p>
                @endif
            </div>
        </div>
    </div>

    {{-- FORM INPUT PENGEMBALIAN --}}
    <form action="{{ route('admin.transaksi.proses-pengembalian', $transaksi) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div class="card p-5 space-y-4">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i data-lucide="check-square" class="w-5 h-5 text-primary-600"></i> Kondisi Akhir Kendaraan
            </h3>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Tanggal Kembali Aktual</label>
                    <input type="datetime-local" name="tanggal_kembali_aktual" value="{{ now()->format('Y-m-d\TH:i') }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Bahan Bakar Akhir</label>
                    <input type="text" name="bahan_bakar_akhir" class="form-input" placeholder="Contoh: Indikator E, Half, Full" required>
                </div>
                <div>
                    <label class="form-label">KM Odometer Akhir</label>
                    <input type="number" name="km_odometer_akhir" class="form-input" placeholder="0" required>
                </div>
                <div>
                    <label class="form-label">Foto Bukti Kondisi Akhir</label>
                    <input type="file" name="foto_kondisi_akhir" class="form-input">
                </div>
            </div>
            <div>
                <label class="form-label">Catatan Kerusakan Baru / Kondisi Pengembalian</label>
                <textarea name="catatan_kondisi_akhir" class="form-input h-20" placeholder="Tulis kerusakan baru atau keluhan mesin jika ada..."></textarea>
            </div>
        </div>

        {{-- WIDGET INPUT DENDA DINAMIS --}}
        <div class="card p-5 space-y-4">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-slate-700 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i> Klaim & Penalti Denda Lapangan
                </h3>
                <button type="button" id="btn-tambah-denda" class="btn-secondary text-xs py-1.5 flex items-center gap-1">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah Baris Denda
                </button>
            </div>

            <div id="wrapper-denda" class="space-y-3">
                </div>

            <div class="pt-4 border-t border-slate-100 flex justify-between items-center text-sm font-semibold">
                <span class="text-slate-500">Total Akumulasi Nilai Pelanggaran:</span>
                <span class="text-red-600 font-mono text-base font-bold" id="label-total-denda">Rp 0</span>
            </div>
        </div>

        {{-- FORM PENYELESAIAN KEUANGAN --}}
        <div class="card p-5 space-y-4 hidden" id="box-administrasi-denda">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i data-lucide="calculator" class="w-5 h-5 text-primary-600"></i> Penyelesaian Neraca Kas & Deposit
            </h3>
            
            <div class="p-4 rounded-xl bg-slate-50 space-y-2 text-sm text-slate-600 border border-slate-100">
                <div class="flex justify-between">
                    <span>Uang Jaminan Pelanggan (Deposit):</span>
                    <span class="font-mono font-bold text-slate-700">Rp {{ number_format($jumlahDeposit, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-red-600">
                    <span>Potongan Deposit Otomatis:</span>
                    <span class="font-mono font-bold" id="label-potongan-deposit">- Rp 0</span>
                </div>
                <div class="flex justify-between border-t border-slate-200/60 pt-2 font-bold text-slate-800">
                    <span>Sisa Kekurangan yang Harus Dibayar Tunai:</span>
                    <span class="font-mono text-primary-600" id="label-sisa-tagihan">Rp 0</span>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-4" id="form-pembayaran-denda">
                <div class="md:col-span-1">
                    <label class="form-label">Alasan Potongan Memo</label>
                    <input type="text" name="alasan_potongan" class="form-input" placeholder="Sebab klaim penalti...">
                </div>
                <div>
                    <label class="form-label">Metode Pembayaran Sisa</label>
                    <select name="metode_pembayaran_id" class="form-input">
                        @foreach($metode as $m)
                        <option value="{{ $m->id }}">{{ $m->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Jumlah Bayar (Rp)</label>
                    <input type="number" name="jumlah_bayar" id="input-jumlah-bayar" value="0" class="form-input" min="0" step="1000">
                </div>
            </div>
        </div>

        {{-- BOX INFO KONDISI IMPAS --}}
        <div class="card p-4 border border-emerald-200 bg-emerald-50" id="box-info-impas">
            <div class="flex items-center gap-2 text-sm text-emerald-700 font-medium">
                <i data-lucide="circle-check-big" class="w-4 h-4"></i>
                <span id="text-info-impas">Selesai aman tanpa tagihan denda baru.</span>
            </div>
            <input type="hidden" name="jumlah_bayar" id="hidden-jumlah-bayar" value="0">
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary flex items-center gap-2" onclick="return confirm('Konfirmasi pengembalian kendaraan ini?')">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Selesaikan Pengembalian Unit (Admin)
            </button>
            <a href="{{ route('admin.transaksi.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection