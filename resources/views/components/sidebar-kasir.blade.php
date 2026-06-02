{{-- Kasir Sidebar Nav --}}
@php
$nav = [
    ['route' => 'kasir.dashboard',        'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
    ['route' => 'kasir.transaksi.index',  'icon' => 'receipt-text',     'label' => 'Transaksi Sewa'],
    ['route' => 'kasir.booking.index',    'icon' => 'calendar-check',   'label' => 'Booking'],
    ['route' => 'kasir.pembayaran.index', 'icon' => 'banknote',         'label' => 'Pembayaran'],
];
@endphp

@foreach($nav as $item)
<a href="{{ route($item['route']) }}"
   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 shrink-0"></i>
    {{ $item['label'] }}
</a>
@endforeach
