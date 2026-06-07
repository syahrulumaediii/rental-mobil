@extends('layouts.app')

@section('title', 'Notifikasi & Pesan')
@section('page-title', 'Notifikasi')

@section('topbar-actions')
@if($notifikasi->count() > 0)
<form method="POST" action="{{ route('pelanggan.notifikasi.read-all') }}">
    @csrf @method('PATCH')
    <button class="btn-secondary text-xs px-3 py-1.5 flex items-center gap-1.5 cursor-pointer">
        <i data-lucide="check-check" class="w-3.5 h-3.5"></i> Tandai semua dibaca
    </button>
</form>
@endif
@endsection

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse($notifikasi as $n)
            @php 
                $belumDibaca = is_null($n->read_at); 
                
                // Deteksi Kategori Tipe dari String [TIPE] di Judul
                $tipe = 'sistem'; 
                $judulTeks = $n->judul;
                
                if (preg_match('/^\[(.*?)\]\s*(.*)$/', $n->judul, $matches)) {
                    $tipe = strtolower($matches[1]);
                    $judulTeks = $matches[2]; // Judul asli tanpa text [TIPE]
                }

                // Pengaturan Gaya UI (Ikon & Warna) berdasarkan 5 Tipe Rekomendasi
                $config = match($tipe) {
                    'booking'    => ['calendar-check', 'text-blue-600', 'bg-blue-50', 'badge-blue'],
                    'denda'      => ['alert-triangle', 'text-red-600', 'bg-red-50', 'badge-red'],
                    'dokumen'    => ['file-warning', 'text-amber-600', 'bg-amber-50', 'badge-yellow'],
                    'pembayaran' => ['credit-card', 'text-green-600', 'bg-green-50', 'badge-green'],
                    default      => ['bell', 'text-slate-600', 'bg-slate-50', 'badge-gray'], // sistem
                };
            @endphp
            
            <a href="{{ route('pelanggan.notifikasi.read', $n->id) }}"
               class="flex items-start gap-4 px-5 py-4 hover:bg-slate-50/80 transition-colors {{ $belumDibaca ? 'bg-slate-50/30' : '' }}">
                
                {{-- Lingkaran Kotak Ikon Sesuai Tipe --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5 transition-colors {{ $belumDibaca ? $config[2] : 'bg-slate-100' }}">
                    <i data-lucide="{{ $config[0] }}" class="w-4 h-4 {{ $belumDibaca ? $config[1] : 'text-slate-400' }}"></i>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Label Badge Kategori Tipe --}}
                        <span class="badge {{ $belumDibaca ? $config[3] : 'badge-gray' }} text-[9px] uppercase tracking-wider px-1.5 py-0.5 rounded font-bold">
                            {{ $tipe }}
                        </span>
                        
                        <p class="text-sm {{ $belumDibaca ? 'font-bold text-slate-800' : 'font-medium text-slate-600' }} truncate flex-1">
                            {{ $judulTeks }}
                        </p>
                    </div>
                    
                    {{-- Teks Pesan Asli dari DB --}}
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">{{ $n->pesan }}</p>
                    
                    <p class="text-[10px] text-slate-400 mt-1.5 flex items-center gap-1 font-mono">
                        <i data-lucide="clock" class="w-3 h-3 text-slate-300"></i>
                        {{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}
                    </p>
                </div>
                
                {{-- Dot Pelingkar Notif Baru --}}
                @if($belumDibaca)
                <div class="w-2 h-2 {{ $config[1] == 'text-slate-600' ? 'bg-primary-500' : str_replace('text', 'bg', $config[1]) }} rounded-full shrink-0 mt-2"></div>
                @endif
            </a>
            @empty
            <div class="py-16 text-center">
                <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-slate-100">
                    <i data-lucide="bell-off" class="w-5 h-5 text-slate-300"></i>
                </div>
                <p class="text-slate-400 font-medium text-sm">Tidak ada pesan masuk</p>
            </div>
            @endforelse
        </div>
    </div>

    @if($notifikasi->hasPages())
    <div class="mt-5">{{ $notifikasi->links() }}</div>
    @endif
</div>
@endsection