@extends('layouts.app')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')
@section('breadcrumb', 'Kasir / Transaksi / Detail')

@section('sidebar-nav')
    @include('components.sidebar-kasir')
@endsection

@section('content')
<div class="grid lg:grid-cols-3 gap-5">

    {{-- Left --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Header Transaksi --}}
        <div class="card p-5">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <p class="font-mono text-lg font-extrabold text-slate-800">{{ $transaksi->kode_transaksi }}</p>
                    <p class="text-sm text-slate-400">Dibuat {{ $transaksi->created_at->format('d M Y, H:i') }}</p>
                </div>
                @php $sc = ['berjalan'=>'badge-blue','selesai'=>'badge-green','dibatalkan'=>'badge-red']; @endphp
                <span class="badge {{ $sc[$transaksi->status] ?? 'badge-gray' }} text-sm px-4 py-1.5">{{ ucfirst($transaksi->status) }}</span>
            </div>
            <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm">
                @foreach([
                    ['Pelanggan',       $transaksi->booking->pelanggan->user->name ?? '-'],
                    ['Kendaraan',       ($transaksi->booking->kendaraan->nama ?? '-').' ('.(($transaksi->booking->kendaraan->plat_nomor) ?? '-').')'],
                    ['Tgl Ambil',       $transaksi->tanggal_ambil_aktual?->format('d M Y') ?? '-'],
                    ['Tgl Kembali Plan',$transaksi->booking->tanggal_selesai?->format('d M Y') ?? '-'],
                    ['Tgl Kembali Aktual', $transaksi->tanggal_kembali_aktual?->format('d M Y') ?? '-'],
                    ['Kasir',           $transaksi->kasir->name ?? '-'],
                ] as [$lbl,$val])
                <div class="flex justify-between py-1.5 border-b border-slate-50">
                    <span class="text-slate-400">{{ $lbl }}</span>
                    <span class="font-medium text-slate-700">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Kondisi Kendaraan --}}
        @if($transaksi->kondisiKendaraan->count())
        <div class="card p-5">
            <h3 class="font-bold text-slate-700 mb-4 text-sm">Kondisi Kendaraan</h3>
            <div class="grid grid-cols-2 gap-4">
                @foreach($transaksi->kondisiKendaraan as $k)
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">{{ ucfirst($k->waktu) }}</p>
                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between"><span class="text-slate-400">Bahan Bakar</span><span class="font-medium">{{ $k->bahan_bakar }}</span></div>
                        <div class="flex justify-between"><span class="text-slate-400">Odometer</span><span class="font-medium font-mono">{{ number_format($k->km_odometer, 0, ',', '.') }} km</span></div>
                        @if($k->catatan_kondisi)<p class="text-xs text-slate-500 mt-2">{{ $k->catatan_kondisi }}</p>@endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Riwayat Pembayaran --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
                <h3 class="font-bold text-slate-700 text-sm">Riwayat Pembayaran</h3>
                @if($transaksi->status === 'berjalan')
                <a href="{{ route('kasir.pembayaran.create', ['transaksi_id'=>$transaksi->id]) }}" class="btn-primary text-xs px-3 py-1.5">+ Tambah</a>
                @endif
            </div>
            <table>
                <thead>
                    <tr><th>Metode</th><th>Jumlah Bayar</th><th>Kembalian</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($transaksi->pembayaran as $p)
                    <tr>
                        <td>{{ $p->metodePembayaran->nama ?? '-' }}</td>
                        <td class="font-semibold text-slate-700">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                        <td class="{{ $p->jumlah_kembali > 0 ? 'text-green-600 font-semibold' : 'text-slate-400' }}">
                            {{ $p->jumlah_kembali > 0 ? 'Rp '.number_format($p->jumlah_kembali,0,',','.') : '—' }}
                        </td>
                        <td><span class="badge {{ $p->status==='berhasil' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($p->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-6 text-slate-400 text-sm">Belum ada pembayaran</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Right --}}
    <div class="space-y-5">
        {{-- Ringkasan Biaya --}}
        <div class="card p-5">
            <h3 class="font-bold text-slate-700 mb-4 text-sm">Ringkasan Biaya</h3>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Biaya Sewa</span>
                    <span class="font-medium">Rp {{ number_format($transaksi->total_biaya, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Denda</span>
                    <span class="{{ $transaksi->total_denda > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">
                        {{ $transaksi->total_denda > 0 ? 'Rp '.number_format($transaksi->total_denda,0,',','.') : '—' }}
                    </span>
                </div>
                <div class="border-t border-slate-100 pt-2 flex justify-between font-bold text-base">
                    <span class="text-slate-700">Total Tagihan</span>
                    <span class="text-slate-800">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</span>
                </div>
                @php $sudahBayar = $transaksi->pembayaran->where('status','berhasil')->sum('jumlah_bayar'); @endphp
                <div class="flex justify-between text-green-600 font-semibold">
                    <span>Sudah Dibayar</span>
                    <span>Rp {{ number_format($sudahBayar, 0, ',', '.') }}</span>
                </div>
                @if($sudahBayar < $transaksi->total_bayar)
                <div class="flex justify-between text-red-600 font-bold">
                    <span>Sisa Tagihan</span>
                    <span>Rp {{ number_format($transaksi->total_bayar - $sudahBayar, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Deposit --}}
        @if($transaksi->deposit)
        <div class="card p-5">
            <h3 class="font-bold text-slate-700 mb-4 text-sm">Deposit</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Jumlah Deposit</span><span class="font-medium">Rp {{ number_format($transaksi->deposit->jumlah,0,',','.') }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Dipotong</span><span class="{{ $transaksi->deposit->jumlah_dipotong > 0 ? 'text-red-600' : 'text-slate-400' }}">{{ $transaksi->deposit->jumlah_dipotong > 0 ? 'Rp '.number_format($transaksi->deposit->jumlah_dipotong,0,',','.') : '—' }}</span></div>
                <div class="flex justify-between font-semibold"><span class="text-slate-700">Dikembalikan</span><span class="text-green-600">Rp {{ number_format($transaksi->deposit->jumlah - ($transaksi->deposit->jumlah_dipotong ?? 0),0,',','.') }}</span></div>
                <div><span class="badge {{ $transaksi->deposit->status==='ditahan' ? 'badge-yellow' : 'badge-green' }}">{{ ucfirst($transaksi->deposit->status) }}</span></div>
            </div>
        </div>
        @endif

        {{-- Denda --}}
        @if($transaksi->denda->count())
        <div class="card p-5">
            <h3 class="font-bold text-slate-700 mb-3 text-sm">Denda</h3>
            @foreach($transaksi->denda as $d)
            <div class="bg-red-50 rounded-xl p-3 text-sm">
                <p class="font-semibold text-red-700 capitalize">{{ str_replace('_', ' ', $d->jenis_denda) }}</p>
                <p class="text-red-600 text-xs">{{ $d->keterangan }}</p>
                <p class="font-bold text-red-700 mt-1">Rp {{ number_format($d->total_denda, 0, ',', '.') }}</p>
            </div>
            @endforeach
        </div>
        @endif

        @if($transaksi->status === 'berjalan')
        <a href="{{ route('kasir.transaksi.form-pengembalian', $transaksi) }}" class="btn-primary w-full flex items-center justify-center gap-2">
            <i data-lucide="car" class="w-4 h-4"></i> Proses Pengembalian
        </a>
        @endif
    </div>
</div>
@endsection
