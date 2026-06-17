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


{{-- Foto Profile dan Nama  --}}
<div class="px-3 mb-6 flex flex-col items-center gap-3">
    @if(auth()->user()->pelanggan && auth()->user()->pelanggan->foto_profil)
        <img src="{{ asset('storage/' . auth()->user()->pelanggan->foto_profil) }}" 
             class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md shrink-0">
    @else
        <div class="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center font-bold text-primary-700 text-2xl uppercase shrink-0">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
    @endif
    
    <div class="text-center overflow-hidden" x-show="sidebarOpen" x-transition>
        <p class="text-sm font-bold text-slate-800 wrap-break-word">{{ auth()->user()->name }}</p>
    </div>
</div>



@foreach($nav as $item)
<a href="{{ route($item['route']) }}"
   {{-- Tambahkan baris di bawah ini --}}
   @click="if (window.innerWidth < 1024) sidebarOpen = false" 
   
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
