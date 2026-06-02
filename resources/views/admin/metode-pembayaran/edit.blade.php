@extends('layouts.app')

@section('title', isset($metodePembayaran) ? 'Edit Metode' : 'Tambah Metode')
@section('page-title', isset($metodePembayaran) ? 'Edit Metode Pembayaran' : 'Tambah Metode Pembayaran')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
<div class="max-w-md mx-auto">
    <div class="card p-6">
        <form method="POST"
              action="{{ isset($metodePembayaran) ? route('admin.metode-pembayaran.update', $metodePembayaran) : route('admin.metode-pembayaran.store') }}">
            @csrf
            @if(isset($metodePembayaran)) @method('PUT') @endif

            <div class="space-y-4">
                <div>
                    <label class="form-label">Nama Metode</label>
                    <input type="text" name="nama" value="{{ old('nama', $metodePembayaran->nama ?? '') }}" class="form-input" required placeholder="Contoh: BCA Transfer">
                    @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Tipe</label>
                    <select name="tipe" class="form-input" required>
                        @foreach(['tunai'=>'Tunai','transfer'=>'Transfer Bank','e-wallet'=>'E-Wallet','kartu'=>'Kartu Kredit/Debit'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ old('tipe', $metodePembayaran->tipe ?? '') === $val ? 'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1" class="w-4 h-4 accent-primary-600"
                           {{ old('is_active', $metodePembayaran->is_active ?? true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-medium text-slate-700">Aktif</label>
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-5 border-t border-slate-100">
                <button type="submit" class="btn-primary">{{ isset($metodePembayaran) ? 'Simpan' : 'Tambah' }}</button>
                <a href="{{ route('admin.metode-pembayaran.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
