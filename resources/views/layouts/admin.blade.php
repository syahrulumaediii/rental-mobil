<div class="min-h-screen bg-slate-100 flex">
    
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:inset-auto">
        
        <div class="flex items-center justify-between h-16 px-6 border-b border-slate-100">
            <span class="text-lg font-bold text-slate-800">AdminPanel</span>
            <button onclick="toggleSidebar()" class="lg:hidden p-1 rounded-lg text-slate-600 hover:bg-slate-100">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-4rem)]">
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
               class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 {{ request()->routeIs($item['route'].'*') ? 'bg-slate-100 text-slate-900 font-semibold' : '' }}">
                
                <div class="flex items-center gap-3">
                    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 shrink-0"></i>
                    <span>{{ $item['label'] }}</span>
                </div>

                @if($item['route'] === 'admin.pelanggan.index' && isset($pendingPelangganCount) && $pendingPelangganCount > 0)
                    <span class="flex h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white shadow-sm ring-2 ring-white animate-pulse">
                        {{ $pendingPelangganCount }}
                    </span>
                @endif
            </a>
            @endforeach

            <div class="pt-4 pb-1 px-3">
                <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Laporan</p>
            </div>
            
            @foreach($laporan as $item)
            <a href="{{ route($item['route']) }}"
               class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 {{ request()->routeIs($item['route'].'*') ? 'bg-slate-100 text-slate-900 font-semibold' : '' }}">
                <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 shrink-0"></i>
                {{ $item['label'] }}
            </a>
            @endforeach
        </nav>
    </aside>

    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 z-40 bg-slate-900/40 hidden lg:hidden"></div>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <header class="bg-white border-b border-slate-200 h-16 flex items-center px-4 lg:px-6">
            <button onclick="toggleSidebar()" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
            
            <div class="ml-4 font-semibold text-slate-700">
                @yield('header-title', 'Dashboard Admin')
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        // Toggle class translate untuk membuka/tutup sidebar
        sidebar.classList.toggle('-translate-x-full');
        // Toggle overlay hitam transparan di mobile
        overlay.classList.toggle('hidden');
    }
</script>