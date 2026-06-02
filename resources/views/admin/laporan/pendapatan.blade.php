@extends('layouts.app')

@section('title', 'Laporan Pendapatan')
@section('page-title', 'Laporan Pendapatan')
@section('breadcrumb', 'Admin / Laporan / Pendapatan')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
{{-- Filter --}}
<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-input w-44">
        </div>
        <div>
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-input w-44">
        </div>
        <button type="submit" class="btn-primary">Tampilkan</button>
    </form>
</div>

{{-- Summary --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    @foreach([
        ['Transaksi Selesai', $summary['total_transaksi'], 'receipt-text', 'blue'],
        ['Total Pendapatan',  'Rp '.number_format($summary['total_pendapatan'],0,',','.'), 'trending-up', 'green'],
        ['Total Denda',       'Rp '.number_format($summary['total_denda'],0,',','.'),      'alert-triangle', 'orange'],
    ] as [$lbl, $val, $icon, $color])
    @php $cl = ['blue'=>['bg'=>'bg-blue-50','ic'=>'text-blue-600','v'=>'text-blue-700'],'green'=>['bg'=>'bg-green-50','ic'=>'text-green-600','v'=>'text-green-700'],'orange'=>['bg'=>'bg-orange-50','ic'=>'text-orange-600','v'=>'text-orange-700']][$color]; @endphp
    <div class="card p-5 flex items-center gap-4">
        <div class="w-11 h-11 {{ $cl['bg'] }} rounded-xl flex items-center justify-center shrink-0">
            <i data-lucide="{{ $icon }}" class="w-5 h-5 {{ $cl['ic'] }}"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ $lbl }}</p>
            <p class="text-xl font-extrabold {{ $cl['v'] }}">{{ $val }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Tabel --}}
<div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
        <h3 class="font-bold text-slate-700">Rincian Transaksi</h3>
        <span class="text-xs text-slate-400">{{ $dari }} s/d {{ $sampai }}</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Kode Transaksi</th>
                <th>Pelanggan</th>
                <th>Kendaraan</th>
                <th>Tgl Kembali</th>
                <th>Biaya Sewa</th>
                <th>Denda</th>
                <th>Total</th>
                <th>Kasir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $t)
            <tr>
                <td><span class="font-mono text-xs font-bold text-slate-600">{{ $t->kode_transaksi }}</span></td>
                <td class="font-medium text-slate-700">{{ $t->booking->pelanggan->user->name ?? '-' }}</td>
                <td class="text-slate-600">{{ $t->booking->kendaraan->nama ?? '-' }}</td>
                <td class="text-slate-500 text-sm">{{ $t->tanggal_kembali_aktual?->format('d M Y') ?? '-' }}</td>
                <td class="font-medium text-slate-700">Rp {{ number_format($t->total_biaya, 0, ',', '.') }}</td>
                <td class="{{ $t->total_denda > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">
                    {{ $t->total_denda > 0 ? 'Rp '.number_format($t->total_denda,0,',','.') : '—' }}
                </td>
                <td class="font-bold text-slate-800">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                <td class="text-slate-500 text-sm">{{ $t->kasir->name ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-12 text-slate-400">Tidak ada transaksi pada periode ini</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
