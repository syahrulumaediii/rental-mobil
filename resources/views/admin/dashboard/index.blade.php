@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
{{-- Stats Grid --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $cards = [
        ['label'=>'Booking Siap Ambil', 'value'=>$stats['booking_siap_ambil'],  'sub'=>'Butuh serah terima unit', 'icon'=>'key',          'color'=>'blue'],
        ['label'=>'Total Kendaraan',    'value'=>$stats['total_kendaraan'],    'sub'=>$stats['kendaraan_tersedia'].' Tersedia',  'icon'=>'car',          'color'=>'blue'],
        ['label'=>'Total Pelanggan',    'value'=>$stats['total_pelanggan'],    'sub'=>$stats['pelanggan_verified'].' Terverifikasi','icon'=>'users',       'color'=>'green'],
        ['label'=>'Booking Pending',    'value'=>$stats['booking_pending'],    'sub'=>'Menunggu persetujuan',                     'icon'=>'clock',        'color'=>'yellow'],
    ];
    $colorMap = ['blue'=>['bg'=>'bg-blue-50','icon'=>'text-blue-600','val'=>'text-blue-700'],
                 'green'=>['bg'=>'bg-green-50','icon'=>'text-green-600','val'=>'text-green-700'],
                 'yellow'=>['bg'=>'bg-yellow-50','icon'=>'text-yellow-600','val'=>'text-yellow-700'],
                 'purple'=>['bg'=>'bg-purple-50','icon'=>'text-purple-600','val'=>'text-purple-700']];
    @endphp
    @foreach($cards as $c)
    @php $cl = $colorMap[$c['color']]; @endphp
    <div class="card p-4 sm:p-5 flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between gap-1 mb-2">
                <p class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wide leading-tight">{{ $c['label'] }}</p>
                <div class="w-8 h-8 sm:w-9 sm:h-9 {{ $cl['bg'] }} rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="{{ $c['icon'] }}" class="w-4 h-4 {{ $cl['icon'] }}"></i>
                </div>
            </div>
            {{-- Ukuran teks responsif: text-xl di HP, text-3xl di PC --}}
            <p class="text-xl sm:text-3xl font-extrabold {{ $cl['val'] }} tracking-tight">{{ $c['value'] }}</p>
        </div>
        <p class="text-[10px] sm:text-xs text-slate-400 mt-1.5 truncate">{{ $c['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- Row Pendapatan & Informasi Transaksi Aktif --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    {{-- Total Pendapatan --}}
    <div class="card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 md:col-span-2">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center shrink-0">
                <i data-lucide="trending-up" class="w-6 h-6 text-emerald-600"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Pendapatan (Selesai)</p>
                <p class="text-xl sm:text-2xl font-extrabold text-emerald-600">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="sm:ml-auto w-full sm:w-auto">
            <a href="{{ route('admin.laporan.pendapatan') }}" class="btn-secondary text-xs block text-center sm:inline-block">Lihat Laporan →</a>
        </div>
    </div>

    {{-- Transaksi Aktif --}}
    <div class="card p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center shrink-0">
            <i data-lucide="activity" class="w-6 h-6 text-purple-600"></i>
        </div>
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Transaksi Aktif</p>
            <p class="text-xl sm:text-2xl font-extrabold text-purple-700">{{ $stats['transaksi_aktif'] }}</p>
            <p class="text-[10px] text-slate-400">Unit sedang di jalan</p>
        </div>
    </div>
</div>

{{-- DAFTAR BOOKING YANG SIAP DIAMBIL --}}
<div class="card mb-6 overflow-hidden">
    <div class="px-4 sm:px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-50/50">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse shrink-0"></div>
            <h3 class="font-bold text-sm sm:text-base text-slate-700">Unit Kendaraan Siap Diambil Pelanggan</h3>
        </div>
        <a href="{{ route('admin.booking.index', ['status' => 'disetujui']) }}" class="text-xs text-blue-600 font-semibold hover:underline flex items-center gap-1">
            Proses Pengambilan <i data-lucide="arrow-right" class="w-3 h-3"></i>
        </a>
    </div>
    
    {{-- Wrapper Pembungkus Tabel Responsif --}}
    <div class="table-responsive">
        <table class="min-w-full divide-y divide-slate-100">
            <thead>
                <tr class="bg-slate-50/30 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <th class="px-5 py-3">Kode / Pelanggan</th>
                    <th class="px-5 py-3">Kendaraan</th>
                    <th class="px-5 py-3">Jadwal Pengambilan</th>
                    <th class="px-5 py-3">Jaminan Deposit</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm">
                @forelse($booking_siap_ambil ?? [] as $ba)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-3">
                        <span class="font-mono text-xs font-bold text-slate-500 block mb-0.5">{{ $ba->kode_booking }}</span>
                        <span class="font-semibold text-slate-700">{{ $ba->pelanggan->user->name }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="font-medium text-slate-700 block">{{ $ba->kendaraan->nama }}</span>
                        <span class="text-xs text-slate-400 font-mono">{{ $ba->kendaraan->plat_nomor }}</span>
                    </td>
                    <td class="px-5 py-3 text-slate-600">
                        <span class="block font-semibold text-xs text-blue-600">@indo_datetime($ba->tanggal_mulai)</span>
                        <span class="text-xs text-slate-400">s/d @indo_datetime($ba->tanggal_selesai)</span>
                    </td>
                    <td class="px-5 py-3">
                        @if($ba->is_deposit)
                            <span class="font-semibold text-slate-700 block">Rp {{ number_format($ba->deposit, 0, ',', '.') }}</span>
                            <span class="text-[10px] text-amber-600 font-medium flex items-center gap-0.5"><i data-lucide="info" class="w-3 h-3"></i> Wajib Bayar</span>
                        @else
                            <span class="text-slate-400 text-xs">Tanpa Deposit</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.booking.show', $ba->id) }}" class="btn-secondary px-2.5 py-1 text-xs flex items-center gap-1" title="Buka Detail">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i> Detail
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-slate-400 text-xs font-medium">
                        <i data-lucide="check-circle" class="w-5 h-5 mx-auto text-slate-300 mb-1"></i>
                        Tidak ada unit kendaraan yang menunggu diambil hari ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Baris Dua Kolom Bawah --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Booking Terbaru --}}
    <div class="card">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-700 text-sm sm:text-base">Booking Terbaru</h3>
            <a href="{{ route('admin.booking.index') }}" class="text-xs text-primary-600 font-semibold hover:underline">Lihat semua</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($booking_terbaru as $b)
            <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-xs font-bold text-slate-500 shrink-0">
                        {{ strtoupper(substr($b->pelanggan->user->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-700 truncate">{{ $b->pelanggan->user->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $b->kendaraan->nama }} · <span class="font-mono text-[11px]">{{ $b->kode_booking }}</span></p>
                    </div>
                </div>
                @php
                $bs = ['pending'=>'badge-yellow','disetujui'=>'badge-blue','ditolak'=>'badge-red','berlangsung'=>'badge-green','selesai'=>'badge-gray','dibatalkan'=>'badge-red'];
                @endphp
                <span class="badge {{ $bs[$b->status] ?? 'badge-gray' }} shrink-0 text-[10px]">{{ ucfirst($b->status) }}</span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada booking</div>
            @endforelse
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="card">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-700 text-sm sm:text-base">Transaksi Terbaru</h3>
            <a href="{{ route('admin.laporan.pendapatan') }}" class="text-xs text-primary-600 font-semibold hover:underline">Lihat laporan</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($transaksi_terbaru as $t)
            <div class="px-5 py-3.5 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-700 font-mono truncate">{{ $t->kode_transaksi }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $t->booking->pelanggan->user->name ?? '-' }} · {{ $t->booking->kendaraan->nama ?? '-' }}</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm font-bold text-slate-700">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</p>
                    @php $ts = ['berjalan'=>'badge-blue','selesai'=>'badge-green','dibatalkan'=>'badge-red']; @endphp
                    <span class="badge {{ $ts[$t->status] ?? 'badge-gray' }} text-[10px]">{{ ucfirst($t->status) }}</span>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada transaksi</div>
            @endforelse
        </div>
    </div>
</div>
@endsection