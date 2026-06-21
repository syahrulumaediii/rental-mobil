<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rull Car') — Sistem Rental Kendaraan</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        primary: {
                            50:   '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        surface: '#f8fafc',
                        ink: '#0f172a',
                    },
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; }
        .sidebar-link { transition: all .2s ease; }
        .sidebar-link.active { background: #0ea5e9; color: white; }
        .sidebar-link:not(.active):hover { background: #e0f2fe; color: #0369a1; }
        .card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04); }
        .btn-primary { background: #0ea5e9; color: white; padding: .5rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: .875rem; transition: all .2s; }
        .btn-primary:hover { background: #0284c7; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(14,165,233,.35); }
        .btn-secondary { background: #f1f5f9; color: #475569; padding: .5rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: .875rem; transition: all .2s; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-danger { background: #fee2e2; color: #dc2626; padding: .5rem 1.25rem; border-radius: 10px; font-weight: 600; font-size: .875rem; transition: all .2s; }
        .btn-danger:hover { background: #fecaca; }
        .form-input { width: 100%; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: .625rem .875rem; font-size: .875rem; transition: border-color .2s; outline: none; background: white; }
        .form-input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,.12); }
        .form-label { font-size: .8rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .35rem; display: block; }
        .badge { display: inline-flex; align-items: center; padding: .2rem .65rem; border-radius: 999px; font-size: .75rem; font-weight: 600; }
        .badge-green  { background: #dcfce7; color: #16a34a; }
        .badge-blue   { background: #dbeafe; color: #2563eb; }
        .badge-yellow { background: #fef9c3; color: #ca8a04; }
        .badge-red    { background: #fee2e2; color: #dc2626; }
        .badge-gray   { background: #f1f5f9; color: #64748b; }
        .badge-orange { background: #ffedd5; color: #ea580c; }
        
        /* Proteksi Pembungkus Tabel Responsif Otomatis */
        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        thead th { padding: .75rem 1rem; text-align: left; font-size: .75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; border-bottom: 1.5px solid #f1f5f9; }
        tbody td { padding: .875rem 1rem; font-size: .875rem; color: #334155; border-bottom: 1px solid #f8fafc; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #f8fafc; }
    </style>

    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-ink antialiased">

{{-- Mobile Overlay (Hanya aktif di layar HP/Tablet) --}}
<div x-show="sidebarOpen" 
     x-cloak 
     @click="sidebarOpen = false"
     class="fixed inset-0 z-60 bg-black/40 backdrop-blur-sm lg:hidden" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0">
</div>

<div class="flex h-screen overflow-hidden relative">

    {{-- SIDEBAR - Transisi Geser Halus & Responsif --}}
    <aside class="fixed lg:relative z-50 h-full bg-white border-r border-slate-100 flex flex-col transition-all duration-300 ease-in-out"
        :class="sidebarOpen ? 'w-64 translate-x-0' : '-translate-x-full lg:translate-x-0 w-64 lg:w-0 lg:border-r-0 overflow-hidden'">

        {{-- 1. Logo & Nama Aplikasi --}}
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3 min-w-[256px]">
            <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="car" class="w-5 h-5 text-white"></i>
            </div>
            <div class="truncate" x-show="sidebarOpen" x-transition>
                <div class="font-bold text-slate-800 leading-none">Rull Car</div>
            </div>
            
            {{-- Tombol Tutup Mobile --}}
            <button @click="sidebarOpen=false" class="lg:hidden ml-auto p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- 2. Area Navigasi --}}
        <div class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5 min-w-[256px]">
            @yield('sidebar-nav')
        </div>

        {{-- 3. Tombol Keluar --}}
        <div class="p-4 border-t border-slate-100 min-w-[256px]">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors font-medium">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- KONTEN UTAMA KANAN --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- TOPBAR --}}
        <header class="bg-white border-b border-slate-100 px-4 sm:px-6 py-4 flex items-center justify-between gap-4 relative z-30">
            <div class="flex items-center gap-3 min-w-0">
                {{-- Tombol Hamburger Utama --}}
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-slate-800 p-2 rounded-lg hover:bg-slate-50 cursor-pointer shrink-0">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                
                {{-- Detail Judul & Jam Real-Time --}}
            <div class="min-w-0">
                <h1 class="text-base sm:text-lg font-bold text-slate-800 truncate leading-tight">@yield('page-title', 'Dashboard')</h1>
                
                    {{-- Fitur Jam Real-Time Berjalan (Alpine.js) --}}
                    <div x-data="{ 
                            currentTime: '',
                            updateTime() {
                                constNow = new Date();

                                const options = { 
                                    weekday: 'short', 
                                    year: 'numeric', 
                                    month: 'short', 
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit',
                                };

                                {{-- Contoh hasil: 'Kam, 11 Jun 2026 22.08.11' --}}
                                let rawString = new Date().toLocaleString('id-ID', options);
                                let parts = rawString.split(' ');
                                let jam = parts.pop().replace(/\./g, ':'); // Ambil bagian jam saja
                                let tanggal = parts.join(' ');
                                this.currentTime = `${tanggal} - Pukul ${jam} WIB`;
                            }
                        }"
                        x-init="updateTime(); setInterval(() => updateTime(), 1000)"
                        class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1 font-medium flex-wrap">
                        <i data-lucide="clock" class="w-3 h-3 text-slate-400 shrink-0"></i>
                        <span x-text="currentTime">Memuat waktu...</span>
                    </div>

                    @hasSection('breadcrumb')
                    <div class="text-[10px] sm:text-xs text-slate-400 font-medium tracking-wide mt-0.5 truncate hidden md:block">@yield('breadcrumb')</div>
                    @endif
                </div>
            </div>


            {{-- Sisi Kanan Topbar --}}
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                @yield('topbar-actions')
                
                @if(auth()->check())
                    {{-- LOGIKA UNTUK PELANGGAN --}}
                    @if(auth()->user()->role === 'pelanggan')
                        @php 
                            $unread = \Illuminate\Support\Facades\DB::table('notifikasi')
                                        ->where('user_id', auth()->id())
                                        ->whereNull('read_at')
                                        ->count(); 
                        @endphp
                        
                        <a href="{{ route('pelanggan.notifikasi.index') }}" class="relative p-2 text-slate-500 hover:text-primary-600 hover:bg-slate-50 rounded-lg transition-colors">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            @if($unread > 0)
                                <span class="absolute top-0.5 right-0.5 min-w-4 h-4 bg-red-500 text-white text-[9px] font-extrabold rounded-full flex items-center justify-center px-1 border border-white shadow-sm">
                                    {{ $unread > 99 ? '99+' : $unread }}
                                </span>
                            @endif
                        </a>

                    {{-- LOGIKA UNTUK KASIR (DIPISAH DENGAN DIV) --}}
                    @elseif(auth()->user()->role === 'kasir')
                        <div class="flex items-center gap-2">
                            {{-- Tombol Lonceng (Total Gabungan) --}}
                            <button @click="sidebarOpen = true" 
                                    class="relative p-2 text-slate-500 hover:text-primary-600 hover:bg-slate-50 rounded-lg transition-colors">
                                <i data-lucide="bell" class="w-5 h-5"></i>
                                
                                {{-- Kita gunakan $totalNotif untuk badge lonceng --}}
                                @if($totalNotif > 0)
                                    <span class="absolute top-0.5 right-0.5 min-w-4 h-4 bg-red-500 text-white text-[9px] font-extrabold rounded-full flex items-center justify-center px-1 border border-white shadow-sm">
                                        {{ $totalNotif > 99 ? '99+' : $totalNotif }}
                                    </span>
                                    {{-- Animasi ping tetap bisa dipertahankan --}}
                                    <span class="absolute top-0.5 right-0.5 min-w-4 h-4 bg-red-500 rounded-full animate-ping opacity-25"></span>
                                @endif
                            </button>

                            {{-- Alert Transaksi Telat (Link A, terpisah dari Button) --}}
                            @if($telatCount > 0)
                                <a href="{{ route('kasir.transaksi.index', ['status' => 'berjalan']) }}" 
                                class="flex items-center gap-1 bg-amber-50 text-amber-700 px-2 py-1 rounded-lg text-xs font-bold border border-amber-200 animate-pulse transition-all hover:bg-amber-100">
                                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                                    {{ $telatCount }} Telat
                                </a>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        </header>

        {{-- FLASH SESSION MESSAGES --}}
        <div class="px-4 sm:px-6 pt-4 space-y-2 shrink-0 max-w-4xl">
            @if(session('success'))
            <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)"
                 class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-medium">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-green-600 shrink-0"></i>
                <span class="flex-1">{{ session('success') }}</span>
                <button @click="show=false" class="text-green-400 hover:text-green-600"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
            @endif
            
            @if(session('error'))
            <div x-data="{show:true}" x-show="show"
                 class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-xs sm:text-sm font-medium">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0"></i>
                <span class="flex-1">{{ session('error') }}</span>
                <button @click="show=false" class="text-red-400 hover:text-red-600"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
            @endif
            
            @if($errors->any())
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-xs sm:text-sm">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0 mt-0.5"></i>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif
        </div>

        {{-- AREA HALAMAN DINAMIS --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 focus:outline-none">
            @yield('content')
        </main>
    </div>
</div>

{{-- Skrip Lucide Icons & Penunjang Interaktivitas --}}
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    document.addEventListener('alpine:init', () => setTimeout(() => lucide.createIcons(), 100));
</script>
@stack('scripts')
</body>
</html>