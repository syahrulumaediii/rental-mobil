{{-- Pelanggan Sidebar Nav --}}
@php
$nav = [
    ['route' => 'pelanggan.dashboard',     'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
    ['route' => 'pelanggan.katalog',       'icon' => 'car',              'label' => 'Katalog Kendaraan'],
    ['route' => 'pelanggan.booking.index', 'icon' => 'calendar-check',   'label' => 'Booking Saya'],
    ['route' => 'pelanggan.dokumen.index', 'icon' => 'file-text',        'label' => 'Dokumen Saya'],
    ['route' => 'pelanggan.profil.show',   'icon' => 'user-circle',      'label' => 'Profil Saya'],
    ['route' => 'pelanggan.notifikasi.index','icon'=> 'bell',            'label' => 'Notifikasi'],
];
@endphp

@foreach($nav as $item)
<a href="{{ route($item['route']) }}"
   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 shrink-0"></i>
    {{ $item['label'] }}
    @if($item['route'] === 'pelanggan.notifikasi.index')
        @php $unread = auth()->user()?->notifikasi()->whereNull('read_at')->count() ?? 0; @endphp
        @if($unread > 0)
        <span class="ml-auto w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unread }}</span>
        @endif
    @endif
</a>
@endforeach
