@extends('layouts.app')

@section('title', 'Audit Log Sistem')
@section('page-title', 'Audit Log Sistem')
@section('breadcrumb', 'Admin / Laporan / Audit Log')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
{{-- Filter Pencarian --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-slate-100">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="w-56">
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">Operator (Staf Admin/Kasir)</label>
            <select name="user_id" class="w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">-- Semua Pengguna --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                @endforeach
            </select>
        </div>
        <div class="w-48">
            <label class="block text-xs font-semibold text-slate-500 uppercase mb-2">Kata Kunci Aksi</label>
            <input type="text" name="aksi" value="{{ request('aksi') }}" placeholder="Contoh: Create, Update" class="w-full rounded-lg border-slate-200 text-sm focus:border-blue-500 focus:ring-blue-500">
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm px-4 py-2 rounded-lg transition duration-200 h-10">
            Cari Log
        </button>
        @if(request()->has('user_id') || request()->has('aksi'))
            <a href="{{ route('admin.laporan.audit-log') }}" class="text-sm text-red-500 hover:underline mb-2.5">Reset</a>
        @endif
    </form>
</div>

{{-- Tabel Logs --}}
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
        <h3 class="font-bold text-slate-700">Catatan Aktivitas Sistem (Audit Trail)</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-100 text-xs font-bold uppercase text-slate-400 bg-slate-50/70">
                    <th class="py-3 px-4 w-40">Waktu Kejadian</th>
                    <th class="py-3 px-4 w-44">Pengguna</th>
                    <th class="py-3 px-4 w-36">Aksi</th>
                    <th class="py-3 px-4 w-44">Referensi Objek</th>
                    <th class="py-3 px-4">Detail Perubahan Data (JSON)</th>
                    <th class="py-3 px-4 w-36">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50/40 transition valign-top">
                    <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                        @indo_datetime($log->created_at)
                    </td>
                    <td class="py-3 px-4">
                        <span class="font-semibold text-slate-800 block">{{ $log->user->name ?? 'System/Visitor' }}</span>
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $log->user->role ?? 'Guest' }}</span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide
                            {{ str_contains(strtolower($log->aksi), 'create') || str_contains(strtolower($log->aksi), 'tambah') ? 'bg-green-50 text-green-700 border border-green-200' : '' }}
                            {{ str_contains(strtolower($log->aksi), 'update') || str_contains(strtolower($log->aksi), 'ubah') ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                            {{ str_contains(strtolower($log->aksi), 'delete') || str_contains(strtolower($log->aksi), 'hapus') ? 'bg-red-50 text-red-700 border border-red-200' : '' }}
                            {{ !str_contains(strtolower($log->aksi), 'create') && !str_contains(strtolower($log->aksi), 'update') && !str_contains(strtolower($log->aksi), 'delete') ? 'bg-slate-100 text-slate-700' : '' }}
                        ">
                            {{ $log->aksi }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-slate-700 font-medium">
                        @if($log->model)
                            <span class="block text-slate-500 font-mono text-[11px]">{{ $log->model }}</span>
                            <span class="text-slate-400 font-normal">ID Referensi: #{{ $log->model_id ?? '-' }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="py-3 px-4 font-mono text-[10px] space-y-1 max-w-xs md:max-w-md overflow-hidden">
                        @if($log->data_lama)
                            <div class="p-1.5 bg-red-50/60 rounded text-red-800 border border-red-100/50">
                                <strong class="block text-[9px] uppercase tracking-wider text-red-500 mb-0.5">Sebelum Perubahan:</strong>
                                {{ json_encode($log->data_lama) }}
                            </div>
                        @endif
                        @if($log->data_baru)
                            <div class="p-1.5 bg-green-50/60 rounded text-green-800 border border-green-100/50">
                                <strong class="block text-[9px] uppercase tracking-wider text-green-500 mb-0.5">Sesudah Perubahan:</strong>
                                {{ json_encode($log->data_baru) }}
                            </div>
                        @endif
                        @if(!$log->data_lama && !$log->data_baru)
                            <span class="text-slate-400 italic">Tidak ada rekaman modifikasi variabel.</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 font-mono text-slate-500 text-[11px]">
                        {{ $log->ip_address ?? '0.0.0.0' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-slate-400 text-sm">Tidak ditemukan riwayat log audit yang cocok.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination Control --}}
    @if($logs->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/30">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection