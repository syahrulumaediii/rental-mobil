@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title')
    Halo, {{ auth()->user()->name }} 👋
@endsection

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')

{{-- Verifikasi Warning --}}
@if(!$stats['is_verified'])
<div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 mb-5 flex items-start gap-3">
    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-500 shrink-0 mt-0.5"></i>
    <div>
        <p class="font-semibold text-amber-800 text-sm">Akun belum terverifikasi</p>
        <p class="text-amber-700 text-xs mt-0.5">Unggah dokumen KTP dan SIM untuk bisa melakukan booking kendaraan.</p>
        <a href="{{ route('pelanggan.dokumen.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-amber-700 hover:underline mt-1.5">
            Upload Dokumen <i data-lucide="arrow-right" class="w-3 h-3"></i>
        </a>
    </div>
</div>
@endif

@if($stats['is_blacklisted'])
<div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 mb-5 flex items-center gap-3">
    <i data-lucide="ban" class="w-5 h-5 text-red-500 shrink-0"></i>
    <div>
        <p class="font-semibold text-red-800 text-sm">Akun Anda diblokir</p>
        <p class="text-red-600 text-xs">Hubungi admin untuk informasi lebih lanjut.</p>
    </div>
</div>
@endif

{{-- Stats Grid (🌟 Diubah menjadi sm:grid-cols-2 lg:grid-cols-4 untuk menampung 4 data statistik) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['Total Booking', $stats['total_booking'], 'calendar', 'blue'],
        ['Menunggu Konfirmasi', $stats['booking_konfirmasi'], 'clock', 'yellow'],
        ['Sewa Aktif (Dipakai)', $stats['sewa_aktif'], 'car', 'indigo'],
        ['Sewa Selesai', $stats['sewa_selesai'], 'check-circle', 'green'],
    ] as [$lbl,$val,$icon,$color])
    @php 
        $cl = [
            'blue'   => ['bg'=>'bg-blue-50',   'ic'=>'text-blue-600',   'v'=>'text-blue-700'],
            'yellow' => ['bg'=>'bg-yellow-50', 'ic'=>'text-yellow-600', 'v'=>'text-yellow-700'],
            'indigo' => ['bg'=>'bg-indigo-50', 'ic'=>'text-indigo-600', 'v'=>'text-indigo-700'], 
            'green'  => ['bg'=>'bg-green-50',  'ic'=>'text-green-600',  'v'=>'text-green-700']
        ][$color]; 
    @endphp
    <div class="card p-5 text-center flex flex-col justify-center items-center">
        <div class="w-10 h-10 {{ $cl['bg'] }} rounded-xl flex items-center justify-center mb-2">
            <i data-lucide="{{ $icon }}" class="w-5 h-5 {{ $cl['ic'] }}"></i>
        </div>
        <p class="text-2xl font-extrabold {{ $cl['v'] }}">{{ $val }}</p>
        <p class="text-xs text-slate-400 mt-0.5 font-medium">{{ $lbl }}</p>
    </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-3 gap-5">
    {{-- Booking Terbaru --}}
    <div class="lg:col-span-2 card">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-700">Booking Terbaru</h3>
            <a href="{{ route('pelanggan.booking.index') }}" class="text-xs text-primary-600 font-semibold hover:underline">Lihat semua</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($booking_terbaru ?? [] as $b)
            <div class="px-5 py-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="car" class="w-5 h-5 text-slate-500"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-700 truncate">{{ $b->kendaraan->nama }}</p>
                    <p class="text-xs text-slate-400 font-mono">{{ $b->kode_booking }}</p>
                    <p class="text-xs text-slate-400">{{ $b->tanggal_mulai?->format('d M H:i') }} – {{ $b->tanggal_selesai?->format('d M Y H:i') }}</p>
                </div>
                @php 
                    $sc = [
                        'pending'     => 'badge-yellow',
                        'disetujui'   => 'badge-blue',
                        'aktif'       => 'badge-indigo', 
                        'berlangsung' => 'badge-green',
                        'selesai'     => 'badge-gray',
                        'ditolak'     => 'badge-red',
                        'dibatalkan'  => 'badge-red'
                    ]; 
                @endphp
                <span class="badge {{ $sc[$b->status] ?? 'badge-gray' }} font-bold uppercase text-[10px] tracking-wide">{{ $b->status }}</span>
            </div>
            @empty
            <div class="px-5 py-10 text-center">
                <i data-lucide="calendar-x" class="w-10 h-10 text-slate-200 mx-auto mb-3"></i>
                <p class="text-slate-400 text-sm">Belum ada booking</p>
                <a href="{{ route('pelanggan.katalog') }}" class="btn-primary text-xs mt-3 inline-block">Lihat Kendaraan</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Notifikasi --}}
    <div class="card">
        <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between">
            <h3 class="font-bold text-slate-700">Notifikasi</h3>
            <a href="{{ route('pelanggan.notifikasi.index') }}" class="text-xs text-primary-600 font-semibold hover:underline">Semua</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($notifikasi as $n)
            <a href="{{ route('pelanggan.notifikasi.read', $n) }}" class="block px-5 py-3.5 hover:bg-slate-50 {{ !$n->is_read ? 'bg-primary-50/50' : '' }}">
                <p class="text-sm text-slate-700 {{ !$n->is_read ? 'font-semibold' : '' }}">{{ $n->judul }}</p>
                <p class="text-xs text-slate-400 mt-0.5 line-clamp-2">{{ $n->isi }}</p>
                <p class="text-[10px] text-slate-300 mt-1">{{ $n->created_at->diffForHumans() }}</p>
            </a>
            @empty
            <div class="px-5 py-8 text-center text-slate-400 text-sm">Tidak ada notifikasi</div>
            @endforelse
        </div>
    </div>
</div>
@endsection