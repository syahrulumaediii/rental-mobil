@extends('layouts.app')

@section('title', isset($user) ? 'Edit User' : 'Tambah User')
@section('page-title', isset($user) ? 'Edit User' : 'Tambah User')
@section('breadcrumb', 'Admin / User / ' . (isset($user) ? 'Edit' : 'Tambah'))

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
<div class="max-w-lg mx-auto">
    <div class="card p-6">
        <form method="POST"
              action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            <div class="space-y-4">
                <div>
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="form-input" required placeholder="Nama lengkap">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="form-input" required placeholder="email@domain.com">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="form-input" placeholder="08xxxxxxxxxx">
                </div>

                <div>
                    <label class="form-label">Role</label>
                    <select name="role" class="form-input" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="kasir" {{ old('role', $user->role ?? '') === 'kasir' ? 'selected' : '' }}>Kasir</option>
                    </select>
                    @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <label class="form-label">Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '' }}</label>
                    <input type="password" name="password" class="form-input" {{ isset($user) ? '' : 'required' }} placeholder="{{ isset($user) ? '••••••••' : 'Min. 8 karakter' }}">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                @if(!isset($user) || request()->filled('password'))
                <div>
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password">
                </div>
                @endif
            </div>

            <div class="flex gap-3 mt-6 pt-5 border-t border-slate-100">
                <button type="submit" class="btn-primary">
                    {{ isset($user) ? 'Simpan Perubahan' : 'Buat User' }}
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
