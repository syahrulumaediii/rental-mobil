@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')
@section('breadcrumb', 'Kasir / Transaksi / Detail')

@section('sidebar-nav')
    @include('components.sidebar-kasir')
@endsection

@section('content')
{{-- Konten Utama: Menggunakan grid 1 kolom di mobile/tablet, dan 3 kolom di layar besar (lg) --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 px-1 sm:px-0">

    {{-- Left Section --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Header Transaksi --}}
        <div class="card p-4 sm:p-5">
            {{-- Bagian Kode & Badge Status: Flex-col di HP, flex-row di PC --}}
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-5">
                <div>
                    <p class="font-mono text-base sm:text-lg font-extrabold text-slate-800 break-all">{{ $transaksi->kode_transaksi }}</p>
                    <p class="text-xs sm:text-sm text-slate-400">Dibuat {{ $transaksi->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
                @php 
                    $sc = ['berjalan' => 'badge-blue', 'selesai' => 'badge-green', 'dibatalkan' => 'badge-red']; 
                @endphp
                <div class="w-fit">
                    <span class="badge {{ $sc[$transaksi->status] ?? 'badge-gray' }} text-xs sm:text-sm px-4 py-1.5">{{ ucfirst($transaksi->status) }}</span>
                </div>
            </div>
            
            {{-- Perubahan: Mengubah list grid agar ramah layar HP (grid-cols-1 di HP, sm:grid-cols-2 di tablet/PC) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-1 text-sm">
                @foreach([
                    ['Pelanggan',          $transaksi->booking->pelanggan->user->name ?? '-'],
                    ['Kendaraan',          ($transaksi->booking->kendaraan->nama ?? '-').' ('.(($transaksi->booking->kendaraan->plat_nomor) ?? '-').')'],
                    ['Tgl Ambil Aktual',   $transaksi->tanggal_ambil_aktual?->format('d M Y') ?? '-'],
                    ['Tgl Kembali Plan',   $transaksi->booking->tanggal_selesai?->format('d M Y') ?? '-'],
                    ['Tgl Kembali Aktual', $transaksi->tanggal_kembali_aktual?->format('d M Y') ?? '-'],
                    ['Kasir Pelaksana',    $transaksi->kasir->name ?? '-'],
                ] as [$lbl,$val])
                <div class="flex flex-col sm:flex-row sm:justify-between py-2 border-b border-slate-50 last:border-0 sm:last:border-b">
                    <span class="text-xs text-slate-400 uppercase tracking-wider sm:normal-case sm:text-sm">{{ $lbl }}</span>
                    <span class="font-medium text-slate-700 sm:text-right mt-0.5 sm:mt-0 break-words">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Kondisi Kendaraan (Ambil & Kembali) --}}
        @if($transaksi->kondisiKendaraan->count())
        <div class="card p-4 sm:p-5">
            <h3 class="font-bold text-slate-700 mb-4 text-sm flex items-center gap-1.5">
                <i data-lucide="shield-check" class="w-4 h-4 text-slate-500"></i> Pemeriksaan Kondisi Kendaraan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($transaksi->kondisiKendaraan->sortBy('id') as $k)
                
                @php
                    $waktuStatus = trim(strtolower($k->waktu));
                    $isAmbil = ($waktuStatus === 'sebelum');
                @endphp

                <div class="border rounded-xl p-4 {{ $isAmbil ? 'bg-blue-50/40 border-blue-100' : 'bg-slate-50 border-slate-100' }}">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 mb-2 border-b border-slate-200/60 pb-1.5">
                        <p class="text-xs font-bold {{ $isAmbil ? 'text-blue-700' : 'text-slate-600' }} uppercase tracking-wide">
                            Kondisi Pasca {{ $isAmbil ? 'Pengambilan' : 'Pengembalian' }}
                        </p>
                        <span class="text-[11px] text-slate-400 font-mono">{{ $k->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Bahan Bakar</span>
                            <span class="font-semibold text-slate-700 capitalize">{{ $k->bahan_bakar }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Odometer</span>
                            <span class="font-medium font-mono text-slate-700">{{ number_format($k->km_odometer, 0, ',', '.') }} km</span>
                        </div>
                        @if($k->catatan_kondisi)
                        <div class="mt-2 bg-white border border-slate-200/60 rounded-lg p-2 text-xs text-slate-500 italic break-words">
                            <strong>Catatan:</strong> {{ $k->catatan_kondisi }}
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Riwayat Pembayaran --}}
        <div class="card overflow-hidden">
            <div class="px-4 py-4 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h3 class="font-bold text-slate-700 text-sm flex items-center gap-1.5">
                    <i data-lucide="receipt" class="w-4 h-4 text-slate-500"></i> Riwayat Pembayaran
                </h3>
                @if($transaksi->status === 'berjalan')
                <a href="{{ route('kasir.pembayaran.create', ['transaksi_id' => $transaksi->id]) }}" class="btn-primary text-xs px-3 py-2 text-center w-full sm:w-auto">
                    + Tambah Pembayaran
                </a>
                @endif
            </div>
            
            {{-- Proteksi Table dengan scroll x jika dibuka via handphone --}}
            <div class="overflow-x-auto whitespace-nowrap">
                <table class="w-full text-left border-collapse min-w-[500px]">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-100">
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-5 py-3 font-semibold">Metode</th>
                            <th class="px-5 py-3 font-semibold">Jumlah Bayar</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-50">
                        @forelse($transaksi->pembayaran as $p)
                        <tr>
                            <td class="px-5 py-3 text-slate-500 text-xs">{{ $p->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-3 font-medium text-slate-700">{{ $p->metodePembayaran->nama ?? '-' }}</td>
                            <td class="px-5 py-3 font-bold text-slate-800">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="px-5 py-3">
                                <span class="badge {{ $p->status === 'lunas' || $p->status === 'berhasil' ? 'badge-green' : 'badge-red' }} text-xs">
                                    {{ $p->status === 'lunas' || $p->status === 'berhasil' ? 'Lunas' : ucfirst($p->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-8 text-slate-400 italic">Belum ada rekaman pembayaran</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right Section --}}
    <div class="space-y-5">
        
        {{-- Ringkasan Komponen Biaya Real --}}
        <div class="card p-4 sm:p-5">
            <h3 class="font-bold text-slate-700 mb-4 text-sm flex items-center gap-1.5">
                <i data-lucide="calculator" class="w-4 h-4 text-slate-500"></i> Neraca & Ringkasan Biaya
            </h3>
            
            @php
                $totalDendaReal = $transaksi->denda->sum('total_denda');
                $potonganDepositReal = $transaksi->deposit->jumlah_dipotong ?? 0;
                $sudahDibayar = $transaksi->pembayaran->whereIn('status', ['lunas', 'berhasil'])->sum('jumlah_bayar');
                $totalTagihanBersih = ($transaksi->total_biaya + $totalDendaReal) - $potonganDepositReal;
                $sisaTagihan = max(0, $totalTagihanBersih - $sudahDibayar);
            @endphp

            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between py-0.5">
                    <span class="text-slate-500">Biaya Sewa Pokok</span>
                    <span class="font-medium text-slate-700">Rp {{ number_format($transaksi->total_biaya, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between py-0.5">
                    <span class="text-slate-500">Kalkulasi Denda</span>
                    <span class="{{ $totalDendaReal > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">
                        {{ $totalDendaReal > 0 ? '+ Rp '.number_format($totalDendaReal, 0, ',', '.') : '—' }}
                    </span>
                </div>

                <div class="flex justify-between py-0.5">
                    <span class="text-slate-500">Potongan via Deposit</span>
                    <span class="{{ $potonganDepositReal > 0 ? 'text-emerald-600 font-semibold' : 'text-slate-400' }}">
                        {{ $potonganDepositReal > 0 ? '− Rp '.number_format($potonganDepositReal, 0, ',', '.') : '—' }}
                    </span>
                </div>

                <div class="border-t border-slate-100 pt-2.5 flex justify-between font-bold text-sm">
                    <span class="text-slate-700">Total Tagihan Bersih</span>
                    <span class="text-slate-900 font-mono">Rp {{ number_format(max(0, $totalTagihanBersih), 0, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between text-xs text-emerald-600 font-semibold pt-1 border-b border-slate-100 pb-2">
                    <span class="flex items-center gap-0.5"><i data-lucide="check" class="w-3.5 h-3.5"></i> Total Dana Masuk</span>
                    <span class="font-mono">Rp {{ number_format($sudahDibayar, 0, ',', '.') }}</span>
                </div>

                {{-- Status Sisa Tagihan Kurang Bayar --}}
                @if($sisaTagihan > 0)
                <div class="flex justify-between items-center text-red-600 font-bold bg-red-50/50 border border-red-100 rounded-lg px-3 py-2 mt-2">
                    <span class="text-xs flex items-center gap-1">
                        <i data-lucide="info" class="w-3.5 h-3.5 shrink-0"></i> Sisa Kurang Bayar
                    </span>
                    <span class="font-mono text-sm break-all pl-2 text-right">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                </div>
                @else
                <div class="flex justify-between text-emerald-700 font-bold bg-emerald-50/60 border border-emerald-100 rounded-lg px-3 py-1.5 mt-2 text-xs items-center">
                    <span class="flex items-center gap-1"><i data-lucide="badge-check" class="w-4 h-4"></i> LUNAS / SELESAI</span>
                    <span>Impas</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Deposit Section --}}
        @if($transaksi->deposit)
        <div class="card p-4 sm:p-5">
            <h3 class="font-bold text-slate-700 mb-4 text-sm flex items-center gap-1.5">
                <i data-lucide="wallet" class="w-4 h-4 text-slate-500"></i> Uang Jaminan (Deposit)
            </h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Mulai Deposit Ditahan</span>
                    <span class="font-medium text-slate-700">Rp {{ number_format($transaksi->deposit->jumlah, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Dipotong (Ganti Rugi)</span>
                    <span class="{{ $potonganDepositReal > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">
                        {{ $potonganDepositReal > 0 ? 'Rp '.number_format($potonganDepositReal, 0, ',', '.') : '—' }}
                    </span>
                </div>
                
                @php
                    $sisaDepositDikembalikan = max(0, $transaksi->deposit->jumlah - $potonganDepositReal);
                @endphp
                
                <div class="flex justify-between font-bold border-t border-slate-100 pt-2 text-slate-800">
                    <span class="text-slate-600">Hak Refund Pelanggan</span>
                    <span class="text-emerald-600 font-mono">Rp {{ number_format($sisaDepositDikembalikan, 0, ',', '.') }}</span>
                </div>
                
                <div class="pt-1">
                    @php
                        $ds = $transaksi->deposit->status;
                        $color = $ds === 'ditahan' ? 'badge-yellow' : ($ds === 'dikembalikan' ? 'badge-green' : 'badge-red');
                    @endphp
                    <span class="badge {{ $color }} text-xs px-2.5 py-1">{{ ucfirst($ds) }}</span>
                </div>
            </div>
        </div>
        @endif

        {{-- Rincian Item Pelanggaran Denda --}}
        @if($transaksi->denda->count())
        <div class="card p-4 sm:p-5">
            <h3 class="font-bold text-slate-700 mb-3 text-sm flex items-center gap-1.5">
                <i data-lucide="gavel" class="w-4 h-4 text-slate-500"></i> Rincian Pelanggaran Denda
            </h3>
            <div class="space-y-3">
                @foreach($transaksi->denda as $d)
                <div class="bg-red-50/60 border border-red-100 rounded-xl p-3 text-sm relative">
                    <span class="absolute top-2 right-3 text-[10px] text-slate-400 uppercase font-bold bg-white px-2 py-0.5 rounded-full border border-slate-100">
                        {{ $d->jumlah_hari_telat > 0 ? $d->jumlah_hari_telat . ' Satuan' : 'Fixed Rate' }}
                    </span>
                    <p class="font-bold text-red-800 text-xs uppercase tracking-wide pr-16 break-words">
                        {{ str_replace('_', ' ', $d->jenis_denda) }}
                    </p>
                    @if($d->keterangan)
                    <p class="text-slate-500 text-xs mt-0.5 leading-relaxed break-words">{{ $d->keterangan }}</p>
                    @endif
                    
                    @if($d->tarif_denda > 0)
                    <p class="text-[11px] text-slate-400 mt-1">
                        Tarif Dasar: Rp {{ number_format($d->tarif_denda, 0, ',', '.') }}
                    </p>
                    @endif
                    
                    <div class="font-extrabold text-red-700 mt-1 border-t border-red-200/40 pt-1 flex justify-between items-center">
                        <span class="text-[11px] font-normal text-slate-400">Subtotal Item:</span>
                        <span class="font-mono">Rp {{ number_format($d->total_denda, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Action Button Sesuai Kondisi Status --}}
        @if($transaksi->status === 'berjalan')
        <a href="{{ route('kasir.transaksi.form-pengembalian', $transaksi) }}" class="btn-primary w-full flex items-center justify-center gap-2 py-3 font-semibold text-sm shadow-sm transition-all hover:bg-primary-700 text-center">
            <i data-lucide="car-front" class="w-4 h-4"></i> Proses Pengembalian Kendaraan
        </a>
        @endif
    </div>
</div>
@endsection