@extends('layouts.app')

@section('title', 'Kendaraan')
@section('page-title', 'Manajemen Kendaraan')
@section('breadcrumb', 'Admin / Kendaraan')

@section('topbar-actions')
<a href="{{ route('admin.kendaraan.create') }}" class="btn-primary flex items-center gap-2">
    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Kendaraan
</a>
@endsection

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
{{-- Filter --}}
<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-44">
            <label class="form-label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Nama, plat, merk...">
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input w-40">
                <option value="">Semua Status</option>
                @foreach(['tersedia','disewa','perawatan'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-input w-44">
                <option value="">Semua Kategori</option>
                @foreach($kategori as $k)
                <option value="{{ $k->id }}" {{ request('kategori_id')==$k->id?'selected':'' }}>{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->anyFilled(['search','status','kategori_id']))
        <a href="{{ route('admin.kendaraan.index') }}" class="btn-secondary">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <table>
        <thead>
            <tr>
                <th>Kendaraan</th>
                <th>Kategori</th>
                <th>Plat Nomor</th>
                <th>Tarif/Hari</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($kendaraan as $k)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        @if($k->foto)
                        <img src="{{ Storage::url($k->foto) }}" class="w-10 h-10 rounded-lg object-cover shrink-0">
                        @else
                        <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center shrink-0">
                            <i data-lucide="car" class="w-5 h-5 text-slate-400"></i>
                        </div>
                        @endif
                        <div>
                            <p class="font-semibold text-slate-700">{{ $k->nama }}</p>
                            <p class="text-xs text-slate-400">{{ $k->merk }} {{ $k->model }} · {{ $k->tahun }}</p>
                        </div>
                    </div>
                </td>
                <td><span class="badge badge-blue">{{ $k->kategori->nama ?? '-' }}</span></td>
                <td><span class="font-mono text-sm font-semibold text-slate-600">{{ $k->plat_nomor }}</span></td>
                <td class="font-semibold text-slate-700">Rp {{ number_format($k->tarif_harian, 0, ',', '.') }}</td>
                <td>
                    @php $sc = ['tersedia'=>'badge-green','disewa'=>'badge-blue','perawatan'=>'badge-yellow']; @endphp
                    <span class="badge {{ $sc[$k->status] ?? 'badge-gray' }}">{{ ucfirst($k->status) }}</span>
                </td>
                <td>
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('admin.kendaraan.show', $k) }}" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"><i data-lucide="eye" class="w-4 h-4"></i></a>
                        <a href="{{ route('admin.kendaraan.edit', $k) }}" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"><i data-lucide="pencil" class="w-4 h-4"></i></a>
                        <form method="POST" action="{{ route('admin.kendaraan.destroy', $k) }}" onsubmit="return confirm('Hapus kendaraan ini?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-12 text-slate-400">Tidak ada data kendaraan</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($kendaraan->hasPages())
    <div class="px-5 py-4 border-t border-slate-50">{{ $kendaraan->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
