@extends('layouts.app')

@section('title', 'Ganti Password')
@section('page-title', 'Ganti Password')

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')
<div class="max-w-md mx-auto">
    <div class="card p-6">
        <form method="POST" action="{{ route('pelanggan.profil.update-password') }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="password_lama" class="form-input" required placeholder="••••••••">
                    @error('password_lama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-input" required placeholder="Min. 8 karakter">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-input" required placeholder="Ulangi password baru">
                </div>
            </div>
            <div class="flex gap-3 mt-5 pt-4 border-t border-slate-100">
                <button type="submit" class="btn-primary">Ganti Password</button>
                <a href="{{ route('pelanggan.profil.show') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
