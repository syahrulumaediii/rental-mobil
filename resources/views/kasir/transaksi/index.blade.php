@extends('layouts.app')

@section('title', 'Transaksi Sewa')
@section('page-title', 'Transaksi Sewa')

@section('sidebar-nav')
    @include('components.sidebar-kasir')
@endsection

@section('content')
<div class="card p-4 mb-5">
    {{-- Perubahan: Mengatur form agar elemennya flex-col di HP dan flex-row di tablet ke atas --}}
    <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-end">
        <div class="flex-1 min-w-0 sm:min-w-44">
            <label class="form-label">Cari Kode / Pelanggan</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full" placeholder="TRX-... atau nama...">
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
        
        {{-- Tombol aksi otomatis menyesuaikan lebar layar --}}
        <div class="flex gap-2 w-full sm:w-auto mt-2 sm:mt-0">
            <button type="submit" class="btn-primary flex-1 sm:flex-none justify-center">Filter</button>
            @if(request()->anyFilled(['search','status']))
            <a href="{{ route('kasir.transaksi.index') }}" class="btn-secondary flex-1 sm:flex-none justify-center text-center">Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- Perubahan: Menambahkan overflow-x-auto agar tabel bisa di-scroll ke samping di HP/Tablet --}}
<div class="card overflow-hidden">
    <div class="overflow-x-auto whitespace-nowrap">
        <table class="min-w-full">
            <thead>
                <tr>
                    <th class="text-left">Kode Transaksi</th>
                    <th class="text-left">Pelanggan</th>
                    <th class="text-left">Kendaraan</th>
                    <th class="text-left">Tgl Ambil</th>
                    <th class="text-left">Tgl Kembali</th>
                    <th class="text-left">Total</th>
                    <th class="text-left">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi as $t)
                <tr class="border-b border-slate-50 last:border-0">
                    <td><span class="font-mono text-xs font-bold text-slate-600">{{ $t->kode_transaksi }}</span></td>
                    <td>
                        <p class="font-medium text-slate-700 max-w-[180px] truncate" title="{{ $t->booking->pelanggan->user->name ?? '-' }}">
                            {{ $t->booking->pelanggan->user->name ?? '-' }}
                        </p>
                    </td>
                    <td>
                        <p class="text-slate-600 max-w-[150px] truncate" title="{{ $t->booking->kendaraan->nama ?? '-' }}">
                            {{ $t->booking->kendaraan->nama ?? '-' }}
                        </p>
                    </td>
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
                    <td class="px-4">
                        <div class="flex items-center gap-1.5 justify-end">
                            <a href="{{ route('kasir.transaksi.show', $t) }}" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            @if($t->status === 'berjalan')
                            <a href="{{ route('kasir.transaksi.form-pengembalian', $t) }}" class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Proses Pengembalian">
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
    </div>
    
    @if($transaksi->hasPages())
    <div class="px-5 py-4 border-t border-slate-50">{{ $transaksi->withQueryString()->links() }}</div>
    @endif
</div>
@endsection