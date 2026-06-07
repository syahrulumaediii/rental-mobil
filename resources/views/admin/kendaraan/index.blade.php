@extends('layouts.app')

@section('title', 'Kendaraan')
@section('page-title', 'Manajemen Kendaraan')
@section('breadcrumb', 'Admin / Kendaraan')

@section('topbar-actions')
<a href="{{ route('admin.kendaraan.create') }}" class="btn-primary flex items-center justify-center gap-2 w-full sm:w-auto">
    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kendaraan
</a>
@endsection

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
{{-- Filter --}}
<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 sm:items-end">
        <div class="w-full sm:flex-1 sm:min-w-[200px]">
            <label class="form-label mb-1.5 block text-xs font-semibold text-slate-500 uppercase">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full text-sm" placeholder="Nama, plat, merk...">
        </div>
        <div class="w-full sm:w-auto sm:min-w-[160px]">
            <label class="form-label mb-1.5 block text-xs font-semibold text-slate-500 uppercase">Status</label>
            <select name="status" class="form-input w-full text-sm">
                <option value="">Semua Status</option>
                @foreach(['tersedia','disewa','perawatan'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-auto sm:min-w-[180px]">
            <label class="form-label mb-1.5 block text-xs font-semibold text-slate-500 uppercase">Kategori</label>
            <select name="kategori_id" class="form-input w-full text-sm">
                <option value="">Semua Kategori</option>
                @foreach($kategori as $k)
                <option value="{{ $k->id }}" {{ request('kategori_id')==$k->id?'selected':'' }}>{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center gap-2 pt-2 sm:pt-0 w-full sm:w-auto">
            <button type="submit" class="btn-primary justify-center flex-1 sm:flex-none px-4 py-2 h-10 text-sm font-semibold">Filter</button>
            @if(request()->anyFilled(['search','status','kategori_id']))
            <a href="{{ route('admin.kendaraan.index') }}" class="btn-secondary justify-center flex-1 sm:flex-none px-4 py-2 h-10 text-sm font-semibold">Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- Main Data Card --}}
<div class="card overflow-hidden">
    {{-- Responsive Table Wrapper Container --}}
    <div class="overflow-x-auto w-full">
        <table class="min-w-full text-left border-collapse divide-y divide-slate-100">
            <thead>
                <tr class="bg-slate-50/70 text-xs font-bold uppercase text-slate-400 tracking-wider">
                    <th class="py-3.5 px-5">Kendaraan</th>
                    <th class="py-3.5 px-5">Kategori</th>
                    <th class="py-3.5 px-5">Plat Nomor</th>
                    <th class="py-3.5 px-5">Tarif / Hari</th>
                    <th class="py-3.5 px-5">Status</th>
                    <th class="py-3.5 px-5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-xs text-slate-600 whitespace-nowrap">
                @forelse($kendaraan as $k)
                <tr class="hover:bg-slate-50/30 transition-colors">
                    {{-- Deskripsi Unit --}}
                    <td class="py-3.5 px-5">
                        <div class="flex items-center gap-3">
                            @if($k->foto)
                            <img src="{{ Storage::url($k->foto) }}" class="w-10 h-10 rounded-lg object-cover border border-slate-100 shrink-0">
                            @else
                            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center shrink-0 border border-slate-200">
                                <i data-lucide="car" class="w-5 h-5 text-slate-400"></i>
                            </div>
                            @endif
                            <div>
                                <p class="font-bold text-slate-800 text-sm">{{ $k->nama }}</p>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">{{ $k->merk }} {{ $k->model }} · {{ $k->tahun }}</p>
                            </div>
                        </div>
                    </td>
                    {{-- Label Kategori --}}
                    <td class="py-3.5 px-5">
                        <span class="badge badge-blue">{{ $k->kategori->nama ?? '-' }}</span>
                    </td>
                    {{-- Kode Plat --}}
                    <td class="py-3.5 px-5">
                        <span class="font-mono text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200/50 px-2 py-0.5 rounded uppercase tracking-wider">{{ $k->plat_nomor }}</span>
                    </td>
                    {{-- Nilai Sewa --}}
                    <td class="py-3.5 px-5 font-semibold text-slate-700 text-sm">
                        Rp {{ number_format($k->tarif_harian, 0, ',', '.') }}
                    </td>
                    {{-- Status badge mapping --}}
                    <td class="py-3.5 px-5">
                        @php $sc = ['tersedia'=>'badge-green','disewa'=>'badge-blue','perawatan'=>'badge-yellow']; @endphp
                        <span class="badge {{ $sc[$k->status] ?? 'badge-gray' }} font-bold uppercase text-[10px] tracking-wide">{{ $k->status }}</span>
                    </td>
                    {{-- Panel Tombol Aksi --}}
                    <td class="py-3.5 px-5">
                        <div class="flex items-center gap-1.5 justify-end">
                            <a href="{{ route('admin.kendaraan.show', $k) }}" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Lihat Detail">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route('admin.kendaraan.edit', $k) }}" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit Aset">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.kendaraan.destroy', $k) }}" onsubmit="return confirm('Hapus data kendaraan ini dari database?')">
                                @csrf @method('DELETE')
                                <button class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Aset">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-16 text-slate-400 font-medium">
                        <i data-lucide="alert-circle" class="w-6 h-6 mx-auto mb-2 text-slate-300"></i>
                        Tidak ada rekaman data armada kendaraan yang terindeks.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination Bar --}}
    @if($kendaraan->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/40">
        {{ $kendaraan->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection