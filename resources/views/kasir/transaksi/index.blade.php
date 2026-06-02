@extends('layouts.app')

@section('title', 'Transaksi Sewa')
@section('page-title', 'Transaksi Sewa')

@section('sidebar-nav')
    @include('components.sidebar-kasir')
@endsection

@section('content')
<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-44">
            <label class="form-label">Cari Kode / Pelanggan</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="TRX-... atau nama...">
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input w-36">
                <option value="">Semua</option>
                @foreach(['berjalan'=>'Berjalan','selesai'=>'Selesai','dibatalkan'=>'Dibatalkan'] as $val=>$lbl)
                <option value="{{ $val }}" {{ request('status')===$val?'selected':'' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->anyFilled(['search','status']))
        <a href="{{ route('kasir.transaksi.index') }}" class="btn-secondary">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <table>
        <thead>
            <tr>
                <th>Kode Transaksi</th>
                <th>Pelanggan</th>
                <th>Kendaraan</th>
                <th>Tgl Ambil</th>
                <th>Tgl Kembali</th>
                <th>Total</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksi as $t)
            <tr>
                <td><span class="font-mono text-xs font-bold text-slate-600">{{ $t->kode_transaksi }}</span></td>
                <td>
                    <p class="font-medium text-slate-700">{{ $t->booking->pelanggan->user->name ?? '-' }}</p>
                </td>
                <td class="text-slate-600">{{ $t->booking->kendaraan->nama ?? '-' }}</td>
                <td class="text-sm text-slate-500">{{ $t->tanggal_ambil_aktual?->format('d M Y') ?? '-' }}</td>
                <td class="text-sm">
                    @php $telat = $t->status === 'berjalan' && $t->booking->tanggal_selesai && now()->gt($t->booking->tanggal_selesai); @endphp
                    <span class="{{ $telat ? 'text-red-600 font-semibold' : 'text-slate-500' }}">
                        {{ $t->booking->tanggal_selesai?->format('d M Y') ?? '-' }}
                        @if($telat) <span class="badge badge-red ml-1">TELAT</span> @endif
                    </span>
                </td>
                <td class="font-semibold text-slate-700">Rp {{ number_format($t->total_bayar, 0, ',', '.') }}</td>
                <td>
                    @php $sc = ['berjalan'=>'badge-blue','selesai'=>'badge-green','dibatalkan'=>'badge-red']; @endphp
                    <span class="badge {{ $sc[$t->status] ?? 'badge-gray' }}">{{ ucfirst($t->status) }}</span>
                </td>
                <td>
                    <div class="flex items-center gap-1.5 justify-end">
                        <a href="{{ route('kasir.transaksi.show', $t) }}" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        @if($t->status === 'berjalan')
                        <a href="{{ route('kasir.transaksi.form-pengembalian', $t) }}" class="p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Proses Pengembalian">
                            <i data-lucide="car" class="w-4 h-4"></i>
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-12 text-slate-400">Tidak ada data transaksi</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($transaksi->hasPages())
    <div class="px-5 py-4 border-t border-slate-50">{{ $transaksi->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
