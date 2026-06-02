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
        ['label'=>'Total Kendaraan',    'value'=>$stats['total_kendaraan'],    'sub'=>$stats['kendaraan_tersedia'].' tersedia',  'icon'=>'car',          'color'=>'blue'],
        ['label'=>'Total Pelanggan',    'value'=>$stats['total_pelanggan'],    'sub'=>$stats['pelanggan_verified'].' terverifikasi','icon'=>'users',       'color'=>'green'],
        ['label'=>'Booking Pending',    'value'=>$stats['booking_pending'],    'sub'=>'Menunggu persetujuan',                     'icon'=>'clock',        'color'=>'yellow'],
        ['label'=>'Transaksi Aktif',    'value'=>$stats['transaksi_aktif'],    'sub'=>'Sedang berjalan',                          'icon'=>'activity',     'color'=>'purple'],
    ];
    $colorMap = ['blue'=>['bg'=>'bg-blue-50','icon'=>'text-blue-600','val'=>'text-blue-700'],
                 'green'=>['bg'=>'bg-green-50','icon'=>'text-green-600','val'=>'text-green-700'],
                 'yellow'=>['bg'=>'bg-yellow-50','icon'=>'text-yellow-600','val'=>'text-yellow-700'],
                 'purple'=>['bg'=>'bg-purple-50','icon'=>'text-purple-600','val'=>'text-purple-700']];
    @endphp
    @foreach($cards as $c)
    @php $cl = $colorMap[$c['color']]; @endphp
    <div class="card p-5">
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">{{ $c['label'] }}</p>
            <div class="w-9 h-9 {{ $cl['bg'] }} rounded-xl flex items-center justify-center">
                <i data-lucide="{{ $c['icon'] }}" class="w-4 h-4 {{ $cl['icon'] }}"></i>
            </div>
        </div>
        <p class="text-3xl font-extrabold {{ $cl['val'] }}">{{ $c['value'] }}</p>
        <p class="text-xs text-slate-400 mt-1">{{ $c['sub'] }}</p>
    </div>
    @endforeach
</div>

{{-- Pendapatan total --}}
<div class="card p-5 mb-6 flex items-center gap-4">
    <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center shrink-0">
        <i data-lucide="trending-up" class="w-6 h-6 text-emerald-600"></i>
    </div>
    <div>
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Pendapatan (Selesai)</p>
        <p class="text-2xl font-extrabold text-emerald-600">Rp {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</p>
    </div>
    <div class="ml-auto">
        <a href="{{ route('admin.laporan.pendapatan') }}" class="btn-secondary text-xs">Lihat Laporan →</a>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    {{-- Booking Terbaru --}}
    <div class="card">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-700">Booking Terbaru</h3>
            <a href="{{ route('admin.booking.index') }}" class="text-xs text-primary-600 font-semibold hover:underline">Lihat semua</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($booking_terbaru as $b)
            <div class="px-5 py-3.5 flex items-center gap-3">
                <div class="w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center text-xs font-bold text-slate-500 shrink-0">
                    {{ strtoupper(substr($b->pelanggan->user->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 truncate">{{ $b->pelanggan->user->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $b->kendaraan->nama }} · {{ $b->kode_booking }}</p>
                </div>
                @php
                $bs = ['pending'=>'badge-yellow','disetujui'=>'badge-blue','ditolak'=>'badge-red','berlangsung'=>'badge-green','selesai'=>'badge-gray','dibatalkan'=>'badge-red'];
                @endphp
                <span class="badge {{ $bs[$b->status] ?? 'badge-gray' }}">{{ ucfirst($b->status) }}</span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada booking</div>
            @endforelse
        </div>
    </div>

    {{-- Transaksi Terbaru --}}
    <div class="card">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-700">Transaksi Terbaru</h3>
            <a href="{{ route('admin.laporan.pendapatan') }}" class="text-xs text-primary-600 font-semibold hover:underline">Lihat laporan</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($transaksi_terbaru as $t)
            <div class="px-5 py-3.5 flex items-center gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-700 font-mono">{{ $t->kode_transaksi }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $t->booking->pelanggan->user->name ?? '-' }} · {{ $t->booking->kendaraan->nama ?? '-' }}</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm font-bold text-slate-700">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</p>
                    @php $ts = ['berjalan'=>'badge-blue','selesai'=>'badge-green','dibatalkan'=>'badge-red']; @endphp
                    <span class="badge {{ $ts[$t->status] ?? 'badge-gray' }}">{{ ucfirst($t->status) }}</span>
                </div>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada transaksi</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
