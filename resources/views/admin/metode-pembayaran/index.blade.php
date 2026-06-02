@extends('layouts.app')

@section('title', 'Metode Pembayaran')
@section('page-title', 'Metode Pembayaran')
@section('breadcrumb', 'Admin / Metode Pembayaran')

@section('topbar-actions')
<a href="{{ route('admin.metode-pembayaran.create') }}" class="btn-primary flex items-center gap-2">
    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Metode
</a>
@endsection

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
<div class="card overflow-hidden">
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Tipe</th>
                <th>Digunakan</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($metode as $m)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        @php $icons = ['tunai'=>'banknote','transfer'=>'building-2','e-wallet'=>'smartphone','kartu'=>'credit-card']; @endphp
                        <div class="w-9 h-9 bg-primary-50 rounded-xl flex items-center justify-center shrink-0">
                            <i data-lucide="{{ $icons[$m->tipe] ?? 'credit-card' }}" class="w-4 h-4 text-primary-600"></i>
                        </div>
                        <span class="font-semibold text-slate-700">{{ $m->nama }}</span>
                    </div>
                </td>
                <td><span class="badge badge-blue capitalize">{{ $m->tipe }}</span></td>
                <td class="text-slate-600">{{ $m->pembayaran_count ?? 0 }} transaksi</td>
                <td>
                    @if($m->is_active)
                    <span class="badge badge-green">Aktif</span>
                    @else
                    <span class="badge badge-gray">Nonaktif</span>
                    @endif
                </td>
                <td>
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('admin.metode-pembayaran.edit', $m) }}" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.metode-pembayaran.destroy', $m) }}" onsubmit="return confirm('Hapus metode ini?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-12 text-slate-400">Belum ada metode pembayaran</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($metode->hasPages())
    <div class="px-5 py-4 border-t border-slate-50">{{ $metode->links() }}</div>
    @endif
</div>
@endsection
