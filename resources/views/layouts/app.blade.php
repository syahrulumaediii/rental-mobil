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

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

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
                            50:  '#f0f9ff',
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
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: .75rem 1rem; text-align: left; font-size: .75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; border-bottom: 1.5px solid #f1f5f9; }
        tbody td { padding: .875rem 1rem; font-size: .875rem; color: #334155; border-bottom: 1px solid #f8fafc; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: #f8fafc; }
    </style>

    @stack('styles')
</head>
<body class="text-ink">

{{-- Mobile overlay --}}
<div x-show="sidebarOpen" x-cloak @click="sidebarOpen=false"
     class="fixed inset-0 bg-black/40 z-20 lg:hidden" x-transition:enter="transition duration-200"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>

<div class="flex h-screen overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="fixed lg:relative z-30 h-full w-64 bg-white border-r border-slate-100 flex flex-col transition-transform duration-300 ease-in-out"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        {{-- Logo --}}
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
            <div class="w-9 h-9 bg-primary-600 rounded-xl flex items-center justify-center">
                <i data-lucide="car" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <div class="font-bold text-slate-800 leading-none">Rull Car</div>
                <div class="text-[10px] text-slate-400 font-mono mt-0.5">{{ ucfirst(auth()->user()->role ?? '') }}</div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
            @yield('sidebar-nav')
        </nav>

        {{-- User footer --}}
        <div class="px-4 py-4 border-t border-slate-100">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-primary-100 flex items-center justify-center font-bold text-primary-700 text-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-semibold text-slate-800 truncate">{{ auth()->user()->name ?? '' }}</div>
                    <div class="text-xs text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-slate-500 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="bg-white border-b border-slate-100 px-6 py-4 flex items-center gap-4">
            <button @click="sidebarOpen=!sidebarOpen" class="lg:hidden text-slate-500 hover:text-slate-800">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>
            <div class="flex-1">
                <h1 class="text-lg font-bold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                <div class="text-xs text-slate-400 mt-0.5">@yield('breadcrumb')</div>
                @endif
            </div>
            <div class="flex items-center gap-3">
                @yield('topbar-actions')
                {{-- Notifikasi (pelanggan) --}}
                @if(auth()->user() && auth()->user()->role === 'pelanggan')
                @php $unread = auth()->user()->notifikasi()->where('read_at', false)->count(); @endphp
                <a href="{{ route('pelanggan.notifikasi.index') }}" class="relative text-slate-500 hover:text-primary-600">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    @if($unread > 0)
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unread }}</span>
                    @endif
                </a>
                @endif
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="px-6 pt-4 space-y-2">
            @if(session('success'))
            <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4000)"
                 class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl text-sm font-medium">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-green-600 shrink-0"></i>
                {{ session('success') }}
                <button @click="show=false" class="ml-auto text-green-400 hover:text-green-600"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
            @endif
            @if(session('error'))
            <div x-data="{show:true}" x-show="show"
                 class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0"></i>
                {{ session('error') }}
                <button @click="show=false" class="ml-auto text-red-400 hover:text-red-600"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
            @endif
            @if($errors->any())
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0 mt-0.5"></i>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif
        </div>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
    document.addEventListener('alpine:init', () => setTimeout(() => lucide.createIcons(), 100));
</script>
@stack('scripts')
</body>
</html>
