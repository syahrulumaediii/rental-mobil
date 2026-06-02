@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('topbar-actions')
<form method="POST" action="{{ route('pelanggan.notifikasi.read-all') }}">
    @csrf @method('PATCH')
    <button class="btn-secondary text-xs px-3 py-1.5 flex items-center gap-1.5">
        <i data-lucide="check-check" class="w-3.5 h-3.5"></i> Tandai semua dibaca
    </button>
</form>
@endsection

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card overflow-hidden">
        <div class="divide-y divide-slate-50">
            @forelse($notifikasi as $n)
            <a href="{{ route('pelanggan.notifikasi.read', $n) }}"
               class="flex items-start gap-4 px-5 py-4 hover:bg-slate-50 transition-colors {{ !$n->is_read ? 'bg-primary-50/50' : '' }}">
                <div class="w-9 h-9 {{ !$n->is_read ? 'bg-primary-100' : 'bg-slate-100' }} rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <i data-lucide="bell" class="w-4 h-4 {{ !$n->is_read ? 'text-primary-600' : 'text-slate-400' }}"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm {{ !$n->is_read ? 'font-bold text-slate-800' : 'font-medium text-slate-600' }}">{{ $n->judul }}</p>
                    <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $n->isi }}</p>
                    <p class="text-[11px] text-slate-300 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                @if(!$n->is_read)
                <div class="w-2 h-2 bg-primary-500 rounded-full shrink-0 mt-2"></div>
                @endif
            </a>
            @empty
            <div class="py-16 text-center">
                <i data-lucide="bell-off" class="w-10 h-10 text-slate-200 mx-auto mb-3"></i>
                <p class="text-slate-400 font-medium">Tidak ada notifikasi</p>
            </div>
            @endforelse
        </div>
    </div>

    @if($notifikasi->hasPages())
    <div class="mt-5">{{ $notifikasi->links() }}</div>
    @endif
</div>
@endsection
