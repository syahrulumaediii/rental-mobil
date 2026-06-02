@extends('layouts.app')

@section('title', 'Upload Dokumen')
@section('page-title', 'Upload Dokumen')

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')
<div class="max-w-md mx-auto">
    <div class="card p-6">
        <form method="POST" action="{{ route('pelanggan.dokumen.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="form-label">Jenis Dokumen</label>
                    <select name="jenis_dokumen" class="form-input" required>
                        <option value="">-- Pilih jenis dokumen --</option>
                        @foreach(['ktp'=>'KTP (Kartu Tanda Penduduk)','sim'=>'SIM (Surat Izin Mengemudi)','paspor'=>'Paspor','lainnya'=>'Lainnya'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ (request('jenis') ?? old('jenis_dokumen')) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('jenis_dokumen')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">File Dokumen</label>
                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center hover:border-primary-400 transition-colors cursor-pointer" onclick="document.getElementById('fileInput').click()">
                        <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-500">Klik untuk memilih file</p>
                        <p class="text-xs text-slate-400 mt-1">JPG, PNG, atau PDF · Maks. 2MB</p>
                    </div>
                    <input id="fileInput" type="file" name="file" accept=".jpg,.jpeg,.png,.pdf" class="hidden" required
                           onchange="document.getElementById('namaFile').textContent = this.files[0]?.name ?? ''">
                    <p id="namaFile" class="text-xs text-primary-600 font-medium mt-2"></p>
                    @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="bg-amber-50 border border-amber-100 rounded-xl p-3 text-xs text-amber-700">
                    Pastikan foto/scan dokumen jelas dan tidak terpotong. Admin akan memverifikasi dalam 1x24 jam.
                </div>
            </div>

            <div class="flex gap-3 mt-5 pt-4 border-t border-slate-100">
                <button type="submit" class="btn-primary">Upload Dokumen</button>
                <a href="{{ route('pelanggan.dokumen.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
