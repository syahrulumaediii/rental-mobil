@extends('layouts.app')

@section('title', 'Detail Transaksi (Admin)')
@section('page-title', 'Detail Transaksi')
@section('breadcrumb', 'Admin / Transaksi / Detail')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
{{-- Layout Induk: Stack Vertikal di HP, 3 Kolom di Laptop --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Kiri & Tengah: Detail Unit & Kondisi (Makan 2 kolom di layar besar) --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Header Transaksi --}}
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
                <div>
                    <p class="font-mono text-lg font-extrabold text-slate-800">{{ $transaksi->kode_transaksi }}</p>
                    <p class="text-sm text-slate-400">Dibuat @indo_datetime($transaksi->created_at) WIB</p>
                </div>
                @php $sc = ['berjalan' => 'badge-blue', 'selesai' => 'badge-green', 'dibatalkan' => 'badge-red']; @endphp
                <span class="badge {{ $sc[$transaksi->status] ?? 'badge-gray' }} text-sm px-4 py-1.5">{{ ucfirst($transaksi->status) }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                @foreach([
                    ['Pelanggan',          $transaksi->booking->pelanggan->user->name ?? '-'],
                    ['Kasir PJ Lapangan',  $transaksi->kasir->name ?? 'Sistem'],
                    ['Kendaraan / Plat',   ($transaksi->booking->kendaraan->nama ?? '-').' / '.($transaksi->booking->kendaraan->plat_nomor ?? '-')],
                    ['Tanggal Ambil',      $transaksi->tanggal_ambil_aktual ? \Carbon\Carbon::parse($transaksi->tanggal_ambil_aktual)->locale('id')->translatedFormat('l, d F Y H:i') : '-'],
                    ['Tanggal Kembali',    $transaksi->tanggal_kembali_aktual ? \Carbon\Carbon::parse($transaksi->tanggal_kembali_aktual)->locale('id')->translatedFormat('l, d F Y H:i') : 'Belum Dikembalikan'],
                ] as $info)
                <div>
                    <p class="text-slate-400 text-xs">{{ $info[0] }}</p>
                    <p class="font-semibold text-slate-700 mt-0.5">{{ $info[1] }}</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Kondisi Fisik Kendaraan --}}
        <div class="card p-5">
            <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
                <i data-lucide="shield-check" class="w-5 h-5 text-primary-600"></i> Rekam Kondisi Fisik Kendaraan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($transaksi->kondisiKendaraan as $k)
                <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50 flex flex-col">
                    <p class="text-xs font-bold uppercase tracking-wider {{ $k->waktu === 'sebelum' ? 'text-blue-600' : 'text-emerald-600' }} mb-3">
                        Kondisi {{ ucfirst($k->waktu) }} Sewa
                    </p>
                    @if($k->foto)
                    <img src="{{ asset('storage/' . $k->foto) }}" alt="Foto" class="w-full h-40 object-cover rounded-lg mb-3 border border-slate-200 shrink-0">
                    @else
                    <div class="w-full h-40 bg-slate-100 rounded-lg mb-3 flex items-center justify-center text-slate-400 text-xs shrink-0">Tidak Ada Foto</div>
                    @endif
                    <div class="space-y-1.5 text-xs flex-1">
                        <div class="flex justify-between border-b border-slate-200/60 pb-1">
                            <span class="text-slate-400">Bahan Bakar:</span>
                            <span class="font-semibold text-slate-700">{{ $k->bahan_bakar }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200/60 pb-1">
                            <span class="text-slate-400">Odometer:</span>
                            <span class="font-mono font-semibold text-slate-700">{{ number_format($k->km_odometer, 0, ',', '.') }} KM</span>
                        </div>
                        <div class="mt-2">
                            <span class="text-slate-400 block mb-0.5">Catatan/Kerusakan:</span>
                            <p class="text-slate-600 italic bg-white p-2 rounded border border-slate-100 leading-relaxed">{{ $k->catatan_kondisi ?? 'Tidak ada catatan.' }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Kanan: Panel Audit Finansial (Otomatis turun ke bawah saat di HP) --}}
    <div class="space-y-5">
        <div class="card p-5 bg-slate-900 text-white">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4">Rincian Finansial (Audit)</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between gap-2">
                    <span class="text-slate-400">Biaya Sewa Pokok:</span>
                    <span class="font-mono text-white shrink-0">Rp {{ number_format($transaksi->total_biaya, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between gap-2 border-b border-slate-800 pb-3">
                    <span class="text-slate-400">Akumulasi Denda:</span>
                    <span class="font-mono text-red-400 shrink-0">+ Rp {{ number_format($transaksi->total_denda, 0, ',', '.') }}</span>
                </div>
                @if($transaksi->deposit)
                <div class="pt-1 text-xs space-y-1 bg-slate-800/50 p-3 rounded-lg border border-slate-800">
                    <div class="flex justify-between gap-2 text-slate-400">
                        <span>Deposit Jaminan:</span>
                        <span class="font-mono text-slate-200 shrink-0">Rp {{ number_format($transaksi->deposit->jumlah, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between gap-2 text-red-400">
                        <span>Potongan Internal:</span>
                        <span class="font-mono '- shrink-0'">- Rp {{ number_format($transaksi->deposit->jumlah_dipotong, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endif
                <div class="flex justify-between items-center gap-2 text-base font-bold pt-2 border-t border-slate-800">
                    <span>Total Tagihan Akhir:</span>
                    <span class="font-mono text-yellow-400 shrink-0">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Log Riwayat Kas --}}
        <div class="card p-5">
            <h4 class="font-bold text-slate-700 mb-3 text-sm flex items-center gap-2">
                <i data-lucide="receipt" class="w-4 h-4 text-slate-500"></i> Riwayat Kas Masuk
            </h4>
            <div class="space-y-3">
                @forelse($transaksi->pembayaran as $p)
                <div class="flex items-center justify-between gap-2 border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $p->metodePembayaran->nama ?? 'Cash' }} &bull; @indo_datetime($p->created_at)</p>
                    </div>
                    <span class="badge {{ $p->status === 'lunas' || $p->status === 'berhasil' ? 'badge-green' : 'badge-gray' }} text-[10px] shrink-0">
                        {{ strtoupper($p->status) }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4">Belum ada kas masuk tercatat.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection