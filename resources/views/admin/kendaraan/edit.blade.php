@extends('layouts.app')

@section('title', isset($kendaraan) ? 'Edit Kendaraan' : 'Tambah Kendaraan')
@section('page-title', isset($kendaraan) ? 'Edit Kendaraan' : 'Tambah Kendaraan')
@section('breadcrumb', 'Admin / Kendaraan / ' . (isset($kendaraan) ? 'Edit' : 'Tambah'))

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
<div class="max-w-3xl mx-auto px-2 sm:px-0">
    <div class="card p-4 sm:p-6">
        <form method="POST"
              action="{{ isset($kendaraan) ? route('admin.kendaraan.update', $kendaraan) : route('admin.kendaraan.store') }}"
              enctype="multipart/form-data">
            @csrf
            @if(isset($kendaraan)) @method('PUT') @endif

            {{-- Grid berubah otomatis menjadi 1 kolom di mobile, dan 2 kolom di desktop --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">

                <div class="md:col-span-2">
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Kendaraan</label>
                    <input type="text" name="nama" value="{{ old('nama', $kendaraan->nama ?? '') }}" class="form-input w-full text-sm" required placeholder="Contoh: Toyota Avanza 2023">
                    @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Kategori</label>
                    <select name="kategori_id" class="form-input w-full text-sm" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategori as $k)
                        <option value="{{ $k->id }}" {{ old('kategori_id', $kendaraan->kategori_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Merk</label>
                    <input type="text" name="merk" value="{{ old('merk', $kendaraan->merk ?? '') }}" class="form-input w-full text-sm" required placeholder="Toyota, Honda, dll">
                    @error('merk')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Model</label>
                    <input type="text" name="model" value="{{ old('model', $kendaraan->model ?? '') }}" class="form-input w-full text-sm" required placeholder="Avanza, Jazz, dll">
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Tahun</label>
                    <input type="number" name="tahun" value="{{ old('tahun', $kendaraan->tahun ?? date('Y')) }}" class="form-input w-full text-sm" required min="1990" max="{{ date('Y') }}">
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Plat Nomor</label>
                    <input type="text" name="plat_nomor" value="{{ old('plat_nomor', $kendaraan->plat_nomor ?? '') }}" class="form-input w-full text-sm font-mono uppercase tracking-wider" required placeholder="B 1234 ABC">
                    @error('plat_nomor')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Warna</label>
                    <input type="text" name="warna" value="{{ old('warna', $kendaraan->warna ?? '') }}" class="form-input w-full text-sm" required placeholder="Putih, Hitam, dll">
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Kapasitas (Orang)</label>
                    <input type="number" name="kapasitas" value="{{ old('kapasitas', $kendaraan->kapasitas ?? '') }}" class="form-input w-full text-sm" required min="1">
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Transmisi</label>
                    <select name="transmisi" class="form-input w-full text-sm" required>
                        <option value="">-- Pilih --</option>
                        @foreach(['manual'=>'Manual','otomatis'=>'Otomatis'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ old('transmisi', $kendaraan->transmisi ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Bahan Bakar</label>
                    <select name="bahan_bakar" class="form-input w-full text-sm" required>
                        <option value="">-- Pilih --</option>
                        @foreach(['bensin'=>'Bensin','solar'=>'Solar','listrik'=>'Listrik','hybrid'=>'Hybrid'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ old('bahan_bakar', $kendaraan->bahan_bakar ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Tarif Harian (Rp)</label>
                    <input type="number" name="tarif_harian" value="{{ old('tarif_harian', $kendaraan->tarif_harian ?? '') }}" class="form-input w-full text-sm font-semibold text-slate-700" required min="0" step="1000" placeholder="300000">
                    @error('tarif_harian')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- 🌟 BARU: Input Nilai Denda Keterlambatan Per Jam --}}
                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Denda Terlambat / Jam (Rp)</label>
                    <input type="number" name="denda_per_jam" value="{{ old('denda_per_jam', $kendaraan->denda_per_jam ?? '') }}" class="form-input w-full text-sm font-semibold text-amber-700 border-amber-200 bg-amber-50/10 focus:border-amber-400 focus:ring-amber-400" required min="0" step="1000" placeholder="25000">
                    @error('denda_per_jam')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Status</label>
                    <select name="status" class="form-input w-full text-sm" required>
                        {{-- 🌟 Status 'rusak' dipetakan masuk --}}
                        @foreach(['aktif'=>'Aktif','non-aktif'=>'Non-Aktif','servis'=>'Service','rusak'=>'Rusak'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ old('status', $kendaraan->status ?? 'tersedia') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Foto Kendaraan</label>
                    @if(isset($kendaraan) && $kendaraan->foto)
                    <div class="mb-3">
                        <img src="{{ Storage::url($kendaraan->foto) }}" class="h-24 sm:h-28 rounded-xl object-cover border border-slate-100 shadow-xs">
                        <p class="text-[11px] text-slate-400 mt-1.5">Upload baru untuk mengganti foto armada</p>
                    </div>
                    @endif
                    <input type="file" name="foto" accept="image/*" class="form-input w-full text-sm bg-slate-50/50 p-2 file:mr-3 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('foto')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="form-input w-full text-sm" placeholder="Deskripsi singkat tentang kelayakan, nomor mesin, atau fasilitas tambahan kendaraan...">{{ old('deskripsi', $kendaraan->deskripsi ?? '') }}</textarea>
                </div>
            </div>

            {{-- Kelompok tombol adaptif: Bertumpuk di HP, mendatar di komputer --}}
            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-6 pt-5 border-t border-slate-100">
                <a href="{{ route('admin.kendaraan.index') }}" class="btn-secondary w-full sm:w-auto flex items-center justify-center text-sm font-semibold h-10 px-5">
                    Batal
                </a>
                <button type="submit" class="btn-primary w-full sm:w-auto justify-center text-sm font-semibold h-10 px-5 sm:ml-auto">
                    {{ isset($kendaraan) ? 'Simpan Perubahan' : 'Tambah Kendaraan' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection