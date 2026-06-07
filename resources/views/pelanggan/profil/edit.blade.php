@extends('layouts.app')

@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')
@section('breadcrumb', 'Pelanggan / Edit Profil')

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-2 sm:px-0">
    <div class="card p-4 sm:p-6">
        <form method="POST" action="{{ route('pelanggan.profil.update') }}" enctype="multipart/form-data">
            @csrf 
            @method('PUT')
            
            {{-- Bagian Unggah Foto Profil --}}
            <div class="flex flex-col items-center gap-3 mb-6 pb-6 border-b border-slate-100">
                <div class="relative group">
                    @if($pelanggan->foto_profil)
                        <img src="{{ asset('storage/' . $pelanggan->foto_profil) }}" class="w-24 h-24 rounded-full object-cover border-2 border-slate-200 shadow-xs" id="preview-avatar">
                    @else
                        <div class="w-24 h-24 rounded-full bg-slate-100 border-2 border-slate-200 flex items-center justify-center font-bold text-slate-700 text-2xl uppercase tracking-wider" id="placeholder-avatar">
                            {{-- Mengamankan inisial nama jika mengandung tanda petik --}}
                            {{ strtoupper(substr(e($user->name), 0, 2)) }}
                        </div>
                        <img class="w-24 h-24 rounded-full object-cover border-2 border-slate-200 hidden shadow-xs" id="preview-avatar">
                    @endif
                </div>
                
                <div class="text-center">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1">Foto Profil</label>
                    <input type="file" name="foto_profil" id="foto_profil" class="text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" accept="image/png, image/jpeg, image/jpg">
                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, JPEG, atau PNG. Maksimal 2MB.</p>
                    @error('foto_profil')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Grid Responsif: 1 Kolom di HP, 2 Kolom di Laptop --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Nama Lengkap</label>
                    {{-- Beri tanda petik ganda luar pada atribut value agar input menerima tanda petik tunggal dengan aman --}}
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input w-full text-sm" required placeholder="Masukkan nama lengkap sesuai identitas">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input w-full text-sm" required>
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">NIK (16 digit)</label>
                    <input type="text" name="nik" value="{{ old('nik', $pelanggan->nik) }}" class="form-input w-full text-sm font-mono tracking-wider" required maxlength="16" minlength="16">
                    @error('nik')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $pelanggan->tempat_lahir) }}" class="form-input w-full text-sm" required>
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $pelanggan->tanggal_lahir ? $pelanggan->tanggal_lahir->format('Y-m-d') : '') }}" class="form-input w-full text-sm" required>
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-input w-full text-sm" required>
                        <option value="">-- Pilih --</option>
                        <option value="laki-laki" {{ old('jenis_kelamin', $pelanggan->jenis_kelamin) === 'laki-laki' ? 'selected':'' }}>Laki-laki</option>
                        <option value="perempuan" {{ old('jenis_kelamin', $pelanggan->jenis_kelamin) === 'perempuan' ? 'selected':'' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Kota</label>
                    <input type="text" name="kota" value="{{ old('kota', $pelanggan->kota) }}" class="form-input w-full text-sm" required>
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Pekerjaan</label>
                    <input type="text" name="pekerjaan" value="{{ old('pekerjaan', $pelanggan->pekerjaan) }}" class="form-input w-full text-sm" placeholder="Karyawan, Wiraswasta, dll">
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" class="form-input w-full text-sm py-2" required>{{ old('alamat', $pelanggan->alamat) }}</textarea>
                </div>
            </div>

            {{-- Tombol Aksi Mobile Friendly --}}
            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-6 pt-5 border-t border-slate-100">
                <a href="{{ route('pelanggan.profil.show') }}" class="btn-secondary w-full sm:w-auto flex items-center justify-center text-sm font-semibold h-10 px-5">
                    Batal
                </a>
                <button type="submit" class="btn-primary w-full sm:w-auto flex items-center justify-center text-sm font-semibold h-10 px-5 sm:ml-auto">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('foto_profil').onchange = evt => {
        const [file] = document.getElementById('foto_profil').files;
        if (file) {
            const preview = document.getElementById('preview-avatar');
            const placeholder = document.getElementById('placeholder-avatar');
            
            preview.src = URL.createObjectURL(file);
            preview.classList.remove('hidden');
            if (placeholder) {
                placeholder.classList.add('hidden');
            }
        }
    }
</script>
@endsection