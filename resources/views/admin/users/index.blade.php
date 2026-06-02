@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('breadcrumb', 'Admin / User')

@section('topbar-actions')
<a href="{{ route('admin.users.create') }}" class="btn-primary flex items-center gap-2">
    <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah User
</a>
@endsection

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-44">
            <label class="form-label">Cari</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Nama atau email...">
        </div>
        <div>
            <label class="form-label">Role</label>
            <select name="role" class="form-input w-36">
                <option value="">Semua Role</option>
                <option value="admin"  {{ request('role')==='admin'  ? 'selected':'' }}>Admin</option>
                <option value="kasir"  {{ request('role')==='kasir'  ? 'selected':'' }}>Kasir</option>
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->anyFilled(['search','role']))
        <a href="{{ route('admin.users.index') }}" class="btn-secondary">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Role</th>
                <th>No. Telepon</th>
                <th>Status</th>
                <th>Bergabung</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full {{ $u->role === 'admin' ? 'bg-purple-100' : 'bg-amber-100' }} flex items-center justify-center font-bold text-sm {{ $u->role === 'admin' ? 'text-purple-700' : 'text-amber-700' }} shrink-0">
                            {{ strtoupper(substr($u->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-700">{{ $u->name }}</p>
                            <p class="text-xs text-slate-400">{{ $u->email }}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="badge {{ $u->role === 'admin' ? 'badge-blue' : 'badge-orange' }}">{{ ucfirst($u->role) }}</span>
                </td>
                <td class="text-slate-600">{{ $u->phone ?? '-' }}</td>
                <td>
                    @if($u->is_active)
                        <span class="badge badge-green">Aktif</span>
                    @else
                        <span class="badge badge-red">Nonaktif</span>
                    @endif
                </td>
                <td class="text-slate-400 text-sm">{{ $u->created_at->format('d M Y') }}</td>
                <td>
                    <div class="flex items-center gap-1.5 justify-end">
                        <a href="{{ route('admin.users.show', $u) }}" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('admin.users.edit', $u) }}" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.users.toggle-active', $u) }}">
                            @csrf @method('PATCH')
                            <button class="p-1.5 text-slate-400 hover:text-{{ $u->is_active ? 'red' : 'green' }}-500 hover:bg-{{ $u->is_active ? 'red' : 'green' }}-50 rounded-lg transition-colors" title="{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <i data-lucide="{{ $u->is_active ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                            </button>
                        </form>
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus user ini?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-12 text-slate-400">Tidak ada data user</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($users->hasPages())
    <div class="px-5 py-4 border-t border-slate-50">{{ $users->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
