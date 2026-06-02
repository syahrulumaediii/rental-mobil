@extends('layouts.app')

@section('title', isset($kendaraan) ? 'Edit Kendaraan' : 'Tambah Kendaraan')
@section('page-title', isset($kendaraan) ? 'Edit Kendaraan' : 'Tambah Kendaraan')
@section('breadcrumb', 'Admin / Kendaraan / ' . (isset($kendaraan) ? 'Edit' : 'Tambah'))

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card p-6">
        <form method="POST"
              action="{{ isset($kendaraan) ? route('admin.kendaraan.update', $kendaraan) : route('admin.kendaraan.store') }}"
              enctype="multipart/form-data">
            @csrf
            @if(isset($kendaraan)) @method('PUT') @endif

            <div class="grid grid-cols-2 gap-5">

                <div class="col-span-2">
                    <label class="form-label">Nama Kendaraan</label>
                    <input type="text" name="nama" value="{{ old('nama', $kendaraan->nama ?? '') }}" class="form-input" required placeholder="Contoh: Toyota Avanza 2023">
                    @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">Kategori</label>
                    <select name="kategori_id" class="form-input" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategori as $k)
                        <option value="{{ $k->id }}" {{ old('kategori_id', $kendaraan->kategori_id ?? '') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">Merk</label>
                    <input type="text" name="merk" value="{{ old('merk', $kendaraan->merk ?? '') }}" class="form-input" required placeholder="Toyota, Honda, dll">
                    @error('merk')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">Model</label>
                    <input type="text" name="model" value="{{ old('model', $kendaraan->model ?? '') }}" class="form-input" required placeholder="Avanza, Jazz, dll">
                </div>

                <div>
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" value="{{ old('tahun', $kendaraan->tahun ?? date('Y')) }}" class="form-input" required min="1990" max="{{ date('Y') }}">
                </div>

                <div>
                    <label class="form-label">Plat Nomor</label>
                    <input type="text" name="plat_nomor" value="{{ old('plat_nomor', $kendaraan->plat_nomor ?? '') }}" class="form-input font-mono" required placeholder="B 1234 ABC">
                </div>

                <div>
                    <label class="form-label">Warna</label>
                    <input type="text" name="warna" value="{{ old('warna', $kendaraan->warna ?? '') }}" class="form-input" required placeholder="Putih, Hitam, dll">
                </div>

                <div>
                    <label class="form-label">Kapasitas (Orang)</label>
                    <input type="number" name="kapasitas" value="{{ old('kapasitas', $kendaraan->kapasitas ?? '') }}" class="form-input" required min="1">
                </div>

                <div>
                    <label class="form-label">Transmisi</label>
                    <select name="transmisi" class="form-input" required>
                        <option value="">-- Pilih --</option>
                        @foreach(['manual'=>'Manual','otomatis'=>'Otomatis'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ old('transmisi', $kendaraan->transmisi ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Bahan Bakar</label>
                    <select name="bahan_bakar" class="form-input" required>
                        <option value="">-- Pilih --</option>
                        @foreach(['bensin'=>'Bensin','solar'=>'Solar','listrik'=>'Listrik','hybrid'=>'Hybrid'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ old('bahan_bakar', $kendaraan->bahan_bakar ?? '') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Tarif Harian (Rp)</label>
                    <input type="number" name="tarif_harian" value="{{ old('tarif_harian', $kendaraan->tarif_harian ?? '') }}" class="form-input" required min="0" step="1000" placeholder="300000">
                </div>

                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input" required>
                        @foreach(['tersedia'=>'Tersedia','disewa'=>'Disewa','perawatan'=>'Perawatan'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ old('status', $kendaraan->status ?? 'tersedia') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="form-label">Foto Kendaraan</label>
                    @if(isset($kendaraan) && $kendaraan->foto)
                    <div class="mb-2">
                        <img src="{{ Storage::url($kendaraan->foto) }}" class="h-24 rounded-xl object-cover">
                        <p class="text-xs text-slate-400 mt-1">Upload baru untuk mengganti foto</p>
                    </div>
                    @endif
                    <input type="file" name="foto" accept="image/*" class="form-input">
                    @error('foto')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="col-span-2">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="form-input" placeholder="Deskripsi singkat tentang kendaraan...">{{ old('deskripsi', $kendaraan->deskripsi ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-5 border-t border-slate-100">
                <button type="submit" class="btn-primary">
                    {{ isset($kendaraan) ? 'Simpan Perubahan' : 'Tambah Kendaraan' }}
                </button>
                <a href="{{ route('admin.kendaraan.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
