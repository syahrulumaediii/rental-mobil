@extends('layouts.app')

@section('title', 'Dashboard Kasir')
@section('page-title', 'Dashboard Kasir')

@section('sidebar-nav')
    @include('components.sidebar-kasir')
@endsection

@section('content')
{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @php
    $cards = [
        ['label'=>'Booking Siap Ambil',  'value'=>$stats['booking_siap_ambil'],  'icon'=>'key',          'color'=>'blue'],
        ['label'=>'Transaksi Aktif',     'value'=>$stats['transaksi_aktif'],      'icon'=>'activity',     'color'=>'green'],
        ['label'=>'Transaksi Hari Ini',  'value'=>$stats['transaksi_hari_ini'],   'icon'=>'receipt-text', 'color'=>'yellow'],
        ['label'=>'Pendapatan Hari Ini', 'value'=>'Rp '.number_format($stats['pendapatan_hari_ini'],0,',','.'), 'icon'=>'banknote', 'color'=>'emerald'],
    ];
    $colorMap = [
        'blue'   => ['bg'=>'bg-blue-50',   'ic'=>'text-blue-600',   'v'=>'text-blue-700'],
        'green'  => ['bg'=>'bg-green-50',  'ic'=>'text-green-600',  'v'=>'text-green-700'],
        'yellow' => ['bg'=>'bg-yellow-50', 'ic'=>'text-yellow-600', 'v'=>'text-yellow-700'],
        'emerald'=> ['bg'=>'bg-emerald-50','ic'=>'text-emerald-600','v'=>'text-emerald-700'],
    ];
    @endphp
    @foreach($cards as $c)
    @php $cl = $colorMap[$c['color']]; @endphp
    <div class="card p-5">
        <div class="flex items-start justify-between mb-3">
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide leading-tight">{{ $c['label'] }}</p>
            <div class="w-full {{ $cl['bg'] }} rounded-xl flex items-center justify-center sm:w-auto text-center mt-2 sm:mt-0">
                <i data-lucide="{{ $c['icon'] }}" class="w-4 h-4 {{ $cl['ic'] }}"></i>
            </div>
        </div>
        <p class="text-xl sm:text-2xl font-extrabold {{ $cl['v'] }}">{{ $c['value'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-2 gap-6">
    {{-- Booking siap diserahkan --}}
    <div class="card">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-700">Booking Siap Diserahkan</h3>
            <a href="{{ route('kasir.transaksi.index') }}" class="text-xs text-primary-600 font-semibold hover:underline">Lihat semua</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($booking_siap as $b)
            <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3 justify-between">
                <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="car" class="w-5 h-5 text-primary-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-700">{{ $b->pelanggan->user->name }}</p>
                    <p class="text-xs text-slate-400">{{ $b->kendaraan->nama }} · {{ $b->kode_booking }}</p>
                    <p class="text-xs text-slate-400">Mulai: {{ $b->tanggal_mulai?->format('d M Y') }}</p>
                </div>
                <a href="{{ route('kasir.transaksi.serah-terima', $b) }}" class="btn-primary text-xs px-3 py-1.5 shrink-0">
                    Serah Terima
                </a>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-slate-400 text-sm">Tidak ada booking siap diserahkan</div>
            @endforelse
        </div>
    </div>

    {{-- Transaksi berjalan --}}
    <div class="card">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-700">Transaksi Sedang Berjalan</h3>
            <a href="{{ route('kasir.transaksi.index', ['status'=>'berjalan']) }}" class="text-xs text-primary-600 font-semibold hover:underline">Lihat semua</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($transaksi_berjalan as $t)
            @php
                $deadline = $t->booking->tanggal_selesai;
                $telat    = $deadline && now()->gt($deadline);
            @endphp
            <div class="px-5 py-4 flex items-center gap-3">
                <div class="w-10 h-10 {{ $telat ? 'bg-red-100' : 'bg-green-100' }} rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="{{ $telat ? 'alert-circle' : 'clock' }}" class="w-5 h-5 {{ $telat ? 'text-red-500' : 'text-green-600' }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-700">{{ $t->booking->pelanggan->user->name ?? '-' }}</p>
                    <p class="text-xs text-slate-400">{{ $t->booking->kendaraan->nama ?? '-' }}</p>
                    <p class="text-xs {{ $telat ? 'text-red-500 font-semibold' : 'text-slate-400' }}">
                        Kembali: {{ $deadline?->format('d M Y') }} {{ $telat ? '(TERLAMBAT)' : '' }}
                    </p>
                </div>
                <a href="{{ route('kasir.transaksi.form-pengembalian', $t) }}" class="text-xs font-semibold text-primary-600 hover:underline shrink-0">
                    Proses Kembali
                </a>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-slate-400 text-sm">Tidak ada transaksi aktif</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
