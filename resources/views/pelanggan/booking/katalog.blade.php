@extends('layouts.app')

@section('title', 'Katalog Kendaraan')
@section('page-title', 'Katalog Kendaraan')

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')
{{-- Filter --}}
<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-input w-44">
                <option value="">Semua Kategori</option>
                @foreach(\App\Models\KategoriKendaraan::all() as $k)
                <option value="{{ $k->id }}" {{ request('kategori_id')==$k->id?'selected':'' }}>{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Transmisi</label>
            <select name="transmisi" class="form-input w-36">
                <option value="">Semua</option>
                <option value="manual"   {{ request('transmisi')==='manual'?'selected':'' }}>Manual</option>
                <option value="otomatis" {{ request('transmisi')==='otomatis'?'selected':'' }}>Otomatis</option>
            </select>
        </div>
        <div>
            <label class="form-label">Maks. Tarif/Hari (Rp)</label>
            <input type="number" name="max_tarif" value="{{ request('max_tarif') }}" class="form-input w-44" placeholder="Contoh: 500000" step="50000">
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->anyFilled(['kategori_id','transmisi','max_tarif']))
        <a href="{{ route('pelanggan.katalog') }}" class="btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Grid Kendaraan --}}
<div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
    @forelse($kendaraan as $k)
    <div class="card overflow-hidden group hover:shadow-md transition-shadow">
        <div class="relative h-44 bg-slate-100 overflow-hidden">
            @if($k->foto)
            <img src="{{ Storage::url($k->foto) }}" alt="{{ $k->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
            <div class="w-full h-full flex items-center justify-center">
                <i data-lucide="car" class="w-12 h-12 text-slate-300"></i>
            </div>
            @endif
            <div class="absolute top-3 left-3">
                <span class="badge badge-green text-[11px]">Tersedia</span>
            </div>
            <div class="absolute top-3 right-3">
                <span class="badge badge-blue text-[11px]">{{ $k->kategori->nama ?? '-' }}</span>
            </div>
        </div>
        <div class="p-4">
            <h3 class="font-bold text-slate-800 truncate">{{ $k->nama }}</h3>
            <p class="text-xs text-slate-400 mb-3">{{ $k->merk }} {{ $k->model }} · {{ $k->tahun }}</p>

            <div class="flex flex-wrap gap-2 mb-3">
                @foreach([[$k->kapasitas.' Orang','users'],[$k->transmisi,'settings'],[$k->bahan_bakar,'fuel']] as [$val,$ic])
                <span class="flex items-center gap-1 text-xs text-slate-500 bg-slate-50 px-2 py-1 rounded-lg">
                    <i data-lucide="{{ $ic }}" class="w-3 h-3"></i> {{ ucfirst($val) }}
                </span>
                @endforeach
            </div>

            <div class="flex items-end justify-between border-t border-slate-50 pt-3">
                <div>
                    <p class="text-xs text-slate-400">Mulai dari</p>
                    <p class="font-extrabold text-primary-700">Rp {{ number_format($k->tarif_harian, 0, ',', '.') }}</p>
                    <p class="text-[10px] text-slate-400">per hari</p>
                </div>
                <a href="{{ route('pelanggan.booking.create', $k) }}" class="btn-primary text-xs px-4 py-2">
                    Pesan
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-16">
        <i data-lucide="car-off" class="w-12 h-12 text-slate-200 mx-auto mb-3"></i>
        <p class="text-slate-400">Tidak ada kendaraan tersedia saat ini</p>
    </div>
    @endforelse
</div>

@if($kendaraan->hasPages())
<div class="mt-6">{{ $kendaraan->withQueryString()->links() }}</div>
@endif
@endsection
