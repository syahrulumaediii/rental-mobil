@extends('layouts.app')

@section('title', 'Detail Kendaraan')
@section('page-title', 'Detail Informasi Armada')
@section('breadcrumb', 'Admin / Kendaraan / Detail')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0">
    {{-- Tombol Kembali & Aksi Cepat --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <a href="{{ route('admin.kendaraan.index') }}" class="btn-secondary flex items-center justify-center gap-2 h-10 px-4 text-sm font-semibold w-full sm:w-auto">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar
        </a>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('admin.kendaraan.edit', $kendaraan) }}" class="btn-warning flex-1 sm:flex-none flex items-center justify-center gap-2 h-10 px-4 text-sm font-semibold bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition">
                <i data-lucide="pencil" class="w-4 h-4"></i> Edit Aset
            </a>
            <form method="POST" action="{{ route('admin.kendaraan.destroy', $kendaraan) }}" onsubmit="return confirm('Hapus permanen kendaraan ini dari sistem?')" class="flex-1 sm:flex-none">
                @csrf @method('DELETE')
                <button type="submit" class="btn-danger flex items-center justify-center gap-2 h-10 px-4 text-sm font-semibold bg-red-600 hover:bg-red-700 text-white rounded-lg transition w-full">
                    <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- Grid Layout Utama --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Kolom Kiri: Foto Utama & Status Ringkas --}}
        <div class="lg:col-span-1 space-y-5">
            <div class="card p-4 flex flex-col items-center">
                <div class="w-full aspect-video rounded-xl overflow-hidden bg-slate-100 border border-slate-200/60 relative">
                    @if($kendaraan->foto)
                        <img src="{{ Storage::url($kendaraan->foto) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                            <i data-lucide="car" class="w-12 h-12 mb-2 stroke-1"></i>
                            <span class="text-xs">Foto belum diunggah</span>
                        </div>
                    @endif
                </div>
                
                <div class="w-full text-center mt-4 pt-4 border-t border-slate-100">
                    <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight">{{ $kendaraan->merk }} {{ $kendaraan->nama }}</h2>
                    <span class="font-mono bg-slate-100 text-slate-700 font-extrabold px-2.5 py-1 rounded text-xs inline-block mt-2 uppercase tracking-widest border border-slate-200/60">
                        {{ $kendaraan->plat_nomor }}
                    </span>
                </div>
            </div>

            {{-- Card Status Operasional & Harga --}}
            <div class="card p-4 space-y-4">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status Fisik Saat Ini</label>
                    @php 
                        $statusClasses = [
                            'tersedia' => 'bg-green-50 text-green-700 border-green-200',
                            'disewa' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'perawatan' => 'bg-red-50 text-red-700 border-red-200'
                        ];
                    @endphp
                    <span class="px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider border inline-block {{ $statusClasses[$kendaraan->status] ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
                        {{ $kendaraan->status }}
                    </span>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Tarif Sewa Harian</label>
                    <p class="text-2xl font-black text-blue-600 font-mono">Rp {{ number_format($kendaraan->tarif_harian, 0, ',', '.') }}<span class="text-xs text-slate-400 font-sans font-normal"> /hari</span></p>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Spesifikasi Teknis Lengkap --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-5 sm:p-6">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-3 mb-4 flex items-center gap-2">
                    <i data-lucide="sliders" class="w-4 h-4 text-slate-400"></i> Spesifikasi & Karakteristik Unit
                </h3>
                
                {{-- Detail Grid Atribut --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                    <div class="flex justify-between sm:block border-b sm:border-b-0 border-slate-50 pb-2 sm:pb-0">
                        <span class="text-slate-400 font-medium sm:block text-xs sm:mb-1">Kategori Fleet</span>
                        <span class="font-semibold text-slate-800 bg-blue-50 text-blue-700 px-2 py-0.5 rounded text-xs inline-block sm:mt-0.5">
                            {{ $kendaraan->kategori->nama ?? 'Tanpa Kategori' }}
                        </span>
                    </div>

                    <div class="flex justify-between sm:block border-b sm:border-b-0 border-slate-50 pb-2 sm:pb-0">
                        <span class="text-slate-400 font-medium sm:block text-xs sm:mb-1">Model / Varian</span>
                        <span class="font-bold text-slate-700 block">{{ $kendaraan->model ?? '—' }}</span>
                    </div>

                    <div class="flex justify-between sm:block border-b sm:border-b-0 border-slate-50 pb-2 sm:pb-0">
                        <span class="text-slate-400 font-medium sm:block text-xs sm:mb-1">Tahun Perakitan</span>
                        <span class="font-bold text-slate-700 block">{{ $kendaraan->tahun }}</span>
                    </div>

                    <div class="flex justify-between sm:block border-b sm:border-b-0 border-slate-50 pb-2 sm:pb-0">
                        <span class="text-slate-400 font-medium sm:block text-xs sm:mb-1">Warna Eksterior</span>
                        <span class="font-bold text-slate-700 block capitalize">{{ $kendaraan->warna }}</span>
                    </div>

                    <div class="flex justify-between sm:block border-b sm:border-b-0 border-slate-50 pb-2 sm:pb-0">
                        <span class="text-slate-400 font-medium sm:block text-xs sm:mb-1">Sistem Transmisi</span>
                        <span class="font-bold text-slate-700 block capitalize">{{ $kendaraan->transmisi }}</span>
                    </div>

                    <div class="flex justify-between sm:block border-b sm:border-b-0 border-slate-50 pb-2 sm:pb-0">
                        <span class="text-slate-400 font-medium sm:block text-xs sm:mb-1">Jenis Bahan Bakar</span>
                        <span class="font-bold text-slate-700 block capitalize">{{ $kendaraan->bahan_bakar }}</span>
                    </div>

                    <div class="flex justify-between sm:block border-b sm:border-b-0 border-slate-50 pb-2 sm:pb-0">
                        <span class="text-slate-400 font-medium sm:block text-xs sm:mb-1">Kapasitas Maksimal</span>
                        <span class="font-bold text-slate-700 block">{{ $kendaraan->kapasitas }} Penumpang</span>
                    </div>
                </div>

                {{-- Deskripsi Tambahan --}}
                <div class="mt-6 pt-5 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Catatan Internal / Deskripsi</h4>
                    <div class="bg-slate-50 rounded-xl p-3.5 text-slate-600 text-xs leading-relaxed whitespace-pre-line border border-slate-200/40">
                        {{ $kendaraan->deskripsi ?? 'Tidak ada catatan deskripsi tambahan untuk unit armada ini.' }}
                    </div>
                </div>
            </div>
            
            {{-- Info Tambahan Waktu Log Logistik --}}
            <div class="text-[11px] text-slate-400 flex flex-col sm:flex-row sm:justify-between px-2 gap-1 font-medium">
                <span>Didaftarkan pada: {{ $kendaraan->created_at?->format('d F Y H:i') ?? '—' }}</span>
                <span>Pembaruan terakhir: {{ $kendaraan->updated_at?->format('d F Y H:i') ?? '—' }}</span>
            </div>
        </div>

    </div>
</div>
@endsection