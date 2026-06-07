@extends('layouts.app')

@section('title', 'Monitor Transaksi Sewa')
@section('page-title', 'Monitor Transaksi Sewa')
@section('breadcrumb', 'Admin / Transaksi / Monitor')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
{{-- Filter Section - Responsif dari HP ke Desktop --}}
<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-end">
        <div class="flex-1 min-w-0 sm:min-w-44">
            <label class="form-label">Cari Kode / Pelanggan / Kasir</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full" placeholder="TRX-..., nama pelanggan, atau kasir...">
        </div>
        <div class="w-full sm:w-36">
            <label class="form-label">Status</label>
            <select name="status" class="form-input w-full">
                <option value="">Semua</option>
                @foreach(['berjalan'=>'Berjalan','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'] as $val=>$lbl)
                <option value="{{ $val }}" {{ request('status')===$val?'selected':'' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <button type="submit" class="btn-primary flex-1 sm:flex-none justify-center">Filter</button>
            @if(request()->anyFilled(['search','status']))
            <a href="{{ route('admin.transaksi.index') }}" class="btn-secondary flex-1 sm:flex-none justify-center text-center">Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- Responsive Table Container --}}
<div class="card overflow-hidden">
    <div class="w-full overflow-x-auto block">
        <table class="min-w-full divide-y divide-slate-100">
            <thead>
                <tr>
                    <th class="whitespace-nowrap text-left">Kode Transaksi</th>
                    <th class="whitespace-nowrap text-left">Pelanggan</th>
                    <th class="whitespace-nowrap text-left">Kendaraan</th>
                    <th class="whitespace-nowrap text-left">Kasir PJ</th>
                    <th class="whitespace-nowrap text-left">Tgl Ambil</th>
                    <th class="whitespace-nowrap text-left">Total Biaya</th>
                    <th class="whitespace-nowrap text-left">Status</th>
                    <th class="whitespace-nowrap text-end">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($transaksi as $t)
                <tr>
                    <td class="font-mono font-bold text-slate-700 whitespace-nowrap">{{ $t->kode_transaksi }}</td>
                    <td class="whitespace-nowrap">
                        <p class="font-medium text-slate-800">{{ $t->booking->pelanggan->user->name ?? '-' }}</p>
                        <p class="text-xs text-slate-400">{{ $t->booking->pelanggan->user->phone ?? '-' }}</p>
                    </td>
                    <td class="whitespace-nowrap">
                        <p class="font-medium text-slate-800">{{ $t->booking->kendaraan->nama ?? '-' }}</p>
                        <p class="text-xs font-mono text-slate-400">{{ $t->booking->kendaraan->plat_nomor ?? '-' }}</p>
                    </td>
                    <td class="whitespace-nowrap">
                        <span class="text-sm text-slate-600 font-medium">{{ $t->kasir->name ?? 'Sistem' }}</span>
                    </td>
                    <td class="whitespace-nowrap">{{ $t->tanggal_ambil_aktual ? \Carbon\Carbon::parse($t->tanggal_ambil_aktual)->format('d M Y') : '-' }}</td>
                    <td class="font-mono font-bold text-slate-700 whitespace-nowrap">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                    <td class="whitespace-nowrap">
                        @php $sc = ['berjalan'=>'badge-blue','selesai'=>'badge-green','dibatalkan'=>'badge-red']; @endphp
                        <span class="badge {{ $sc[$t->status] ?? 'badge-gray' }}">{{ ucfirst($t->status) }}</span>
                    </td>
                    <td class="whitespace-nowrap">
                        <div class="flex items-center gap-1.5 justify-end">
                            <a href="{{ route('admin.transaksi.show', $t) }}" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors" title="Lihat Detail">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('admin.transaksi.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Transaksi">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-12 text-slate-400">Tidak ada data transaksi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transaksi->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $transaksi->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection