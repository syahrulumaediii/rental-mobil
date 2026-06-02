@extends('layouts.app')

@section('title', 'Pelanggan')
@section('page-title', 'Manajemen Pelanggan')
@section('breadcrumb', 'Admin / Pelanggan')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-44">
            <label class="form-label">Cari Nama / Email / NIK</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Cari pelanggan...">
        </div>
        <div>
            <label class="form-label">Status Verifikasi</label>
            <select name="status_verifikasi" class="form-input w-48">
                <option value="">Semua</option>
                @foreach(['belum_verifikasi'=>'Belum Verifikasi','pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $val=>$lbl)
                <option value="{{ $val }}" {{ request('status_verifikasi')===$val?'selected':'' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->anyFilled(['search','status_verifikasi']))
        <a href="{{ route('admin.pelanggan.index') }}" class="btn-secondary">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <table>
        <thead>
            <tr>
                <th>Pelanggan</th>
                <th>NIK</th>
                <th>Kota</th>
                <th>Verifikasi</th>
                <th>Blacklist</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($pelanggan as $p)
            <tr>
                <td>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-primary-100 rounded-full flex items-center justify-center font-bold text-primary-700 text-sm shrink-0">
                            {{ strtoupper(substr($p->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-700">{{ $p->user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $p->user->email }}</p>
                        </div>
                    </div>
                </td>
                <td><span class="font-mono text-sm">{{ $p->nik ?? '-' }}</span></td>
                <td>{{ $p->kota ?? '-' }}</td>
                <td>
                    @php $sv = ['belum_verifikasi'=>'badge-gray','pending'=>'badge-yellow','verified'=>'badge-green','rejected'=>'badge-red']; @endphp
                    <span class="badge {{ $sv[$p->status_verifikasi] ?? 'badge-gray' }}">{{ ucwords(str_replace('_',' ',$p->status_verifikasi)) }}</span>
                </td>
                <td>
                    @if($p->isBlacklisted())
                    <span class="badge badge-red"><i data-lucide="ban" class="w-3 h-3 mr-1"></i>Blacklist</span>
                    @else
                    <span class="text-slate-400 text-xs">—</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.pelanggan.show', $p) }}" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors inline-flex"><i data-lucide="eye" class="w-4 h-4"></i></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-12 text-slate-400">Tidak ada data pelanggan</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($pelanggan->hasPages())
    <div class="px-5 py-4 border-t border-slate-50">{{ $pelanggan->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
