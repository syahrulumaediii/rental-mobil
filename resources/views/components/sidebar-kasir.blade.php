{{-- Kasir Sidebar Nav --}}
@php
$nav = [
    ['route' => 'kasir.dashboard',        'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
    ['route' => 'kasir.transaksi.index',  'icon' => 'receipt-text',     'label' => 'Transaksi Sewa'],
    ['route' => 'kasir.booking.index',    'icon' => 'calendar-check',   'label' => 'Booking'],
    ['route' => 'kasir.walkin.step1',     'icon' => 'user-plus',        'label' => 'Walk-In'],
    ['route' => 'kasir.pembayaran.index', 'icon' => 'banknote',         'label' => 'Pembayaran'],
];
@endphp


@foreach($nav as $item)
    <a href="{{ route($item['route']) }}"
       class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 {{ request()->routeIs($item['route'].'*') ? 'active' : '' }}">
        
        {{-- Kiri: Ikon & Label --}}
        <div class="flex items-center gap-3">
            <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 shrink-0"></i>
            <span>{{ $item['label'] }}</span>
        </div>

        {{-- Kanan: Badge Notifikasi (Semua dibungkus dalam link) --}}
        <div class="flex items-center gap-1">
            
            {{-- Notif Booking --}}
            @if($item['route'] === 'kasir.booking.index' && isset($bookingPelangganCount) && $bookingPelangganCount > 0)
                <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white shadow-sm ring-2 ring-white animate-pulse">
                    {{ $bookingPelangganCount }}
                </span>
            @endif

            {{-- Notif Telat --}}
            @if($item['route'] === 'kasir.transaksi.index' && isset($telatCount) && $telatCount > 0)
                <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white shadow-sm ring-2 ring-white animate-pulse">
                    {{ $telatCount }}
                </span>
            @endif
            
        </div>
    </a>
@endforeach
