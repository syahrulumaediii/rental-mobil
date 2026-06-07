<!-- {{-- Admin Sidebar Nav - include in admin views via @section('sidebar-nav') --}}
@php
$nav = [
    ['route' => 'admin.dashboard',               'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
    ['route' => 'admin.users.index',              'icon' => 'users',            'label' => 'Manajemen User'],
    ['route' => 'admin.kendaraan.index',          'icon' => 'car',              'label' => 'Kendaraan'],
    ['route' => 'admin.kategori-kendaraan.index', 'icon' => 'tag',              'label' => 'Kategori Kendaraan'],
    ['route' => 'admin.booking.index',            'icon' => 'calendar-check',   'label' => 'Booking'],
    ['route' => 'admin.pelanggan.index',          'icon' => 'user-check',       'label' => 'Pelanggan'],
    ['route' => 'admin.metode-pembayaran.index',  'icon' => 'credit-card',      'label' => 'Metode Pembayaran'],
];
$laporan = [
    ['route' => 'admin.laporan.pendapatan', 'icon' => 'bar-chart-2', 'label' => 'Laporan Pendapatan'],
    ['route' => 'admin.laporan.kendaraan',  'icon' => 'pie-chart',   'label' => 'Laporan Kendaraan'],
    ['route' => 'admin.laporan.audit-log',  'icon' => 'scroll-text', 'label' => 'Audit Log'],
];
@endphp

@foreach($nav as $item)
<a href="{{ route($item['route']) }}"
   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 shrink-0"></i>
    {{ $item['label'] }}
</a>
@endforeach

<div class="pt-4 pb-1 px-3">
    <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Laporan</p>
</div>
@foreach($laporan as $item)
<a href="{{ route($item['route']) }}"
   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 shrink-0"></i>
    {{ $item['label'] }}
</a>
@endforeach -->


{{-- Admin Sidebar Nav - include in admin views via @section('sidebar-nav') --}}
@php
$nav = [
['route' => 'admin.dashboard',               'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
    ['route' => 'admin.users.index',              'icon' => 'users',            'label' => 'Manajemen User'],
    ['route' => 'admin.kendaraan.index',          'icon' => 'car',              'label' => 'Kendaraan'],
    ['route' => 'admin.kategori-kendaraan.index', 'icon' => 'tag',              'label' => 'Kategori Kendaraan'],
    ['route' => 'admin.transaksi.index',   'icon' => 'receipt-text',     'label' => 'Monitor Transaksi'],
    ['route' => 'admin.booking.index',            'icon' => 'calendar-check',   'label' => 'Booking'],
    ['route' => 'admin.pelanggan.index',          'icon' => 'user-check',       'label' => 'Pelanggan'],
    
    // TAMBAHKAN MENU BARU INI DI SINI
    ['route' => 'admin.pesan.index',              'icon' => 'message-square-plus', 'label' => 'Kirim Pesan Berkas'],
    
    ['route' => 'admin.metode-pembayaran.index',  'icon' => 'credit-card',      'label' => 'Metode Pembayaran'],
];
$laporan = [
    ['route' => 'admin.laporan.pendapatan', 'icon' => 'bar-chart-2', 'label' => 'Laporan Pendapatan'],
    ['route' => 'admin.laporan.kendaraan',  'icon' => 'pie-chart',   'label' => 'Laporan Kendaraan'],
    ['route' => 'admin.laporan.audit-log',  'icon' => 'scroll-text', 'label' => 'Audit Log'],
];
@endphp

@foreach($nav as $item)
<a href="{{ route($item['route']) }}"
   class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
    
    {{-- Bungkus Icon dan Label teks agar tetap rapat di sisi kiri --}}
    <div class="flex items-center gap-3">
        <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 shrink-0"></i>
        <span>{{ $item['label'] }}</span>
    </div>

    {{-- LOGIKA BARU: Munculkan lingkaran merah jika menu ini adalah 'Pelanggan' dan ada data pending --}}
    @if($item['route'] === 'admin.pelanggan.index' && isset($pendingPelangganCount) && $pendingPelangganCount > 0)
        <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white shadow-sm ring-2 ring-white animate-pulse">
            {{ $pendingPelangganCount }}
        </span>
    @endif
    
    <!-- Logika Untuk Notifikasi Booking Pending    -->
    @if($item['route'] === 'admin.booking.index' && isset($bookingPelangganCount) && $bookingPelangganCount > 0)
        <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white shadow-sm ring-2 ring-white animate-pulse">
            {{ $bookingPelangganCount }}
        </span>
    @endif

</a>
@endforeach

<div class="pt-4 pb-1 px-3">
    <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Laporan</p>
</div>
@foreach($laporan as $item)
<a href="{{ route($item['route']) }}"
   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 shrink-0"></i>
    {{ $item['label'] }}
</a>
@endforeach