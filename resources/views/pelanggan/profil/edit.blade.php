@extends('layouts.app')

@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-6">
        <form method="POST" action="{{ route('pelanggan.profil.update') }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" required>
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">NIK (16 digit)</label>
                    <input type="text" name="nik" value="{{ old('nik', $pelanggan->nik) }}" class="form-input font-mono" required maxlength="16" minlength="16">
                    @error('nik')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $pelanggan->tempat_lahir) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $pelanggan->tanggal_lahir?->format('Y-m-d')) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-input" required>
                        <option value="">-- Pilih --</option>
                        <option value="laki-laki" {{ old('jenis_kelamin', $pelanggan->jenis_kelamin) === 'laki-laki' ? 'selected':'' }}>Laki-laki</option>
                        <option value="perempuan" {{ old('jenis_kelamin', $pelanggan->jenis_kelamin) === 'perempuan' ? 'selected':'' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Kota</label>
                    <input type="text" name="kota" value="{{ old('kota', $pelanggan->kota) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $pelanggan->pekerjaan) }}" class="form-input" placeholder="Karyawan, Wiraswasta, dll">
                </div>
                <div class="col-span-2">
                    <label class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" class="form-input" required>{{ old('alamat', $pelanggan->alamat) }}</textarea>
                </div>
            </div>
            <div class="flex gap-3 mt-5 pt-4 border-t border-slate-100">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                <a href="{{ route('pelanggan.profil.show') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
