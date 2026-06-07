@extends('layouts.app')

@section('title', 'Daftar Pembayaran')
@section('page-title', 'Riwayat Pembayaran')
@section('breadcrumb', 'Kasir / Pembayaran')

@section('sidebar-nav')
    @include('components.sidebar-kasir')
@endsection

@section('content')
<div class="space-y-5">
    {{-- Rentetan Stat Sederhana / Ringkasan Mini --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="card p-4 flex items-center gap-4">
            <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                <i data-lucide="banknote" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium">Total Dana Masuk</p>
                <p class="text-lg font-bold text-slate-800">Rp {{ number_format($transaksi->where('status', 'selesai')->sum('total_bayar'), 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-4">
            <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                <i data-lucide="refresh-cw" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium">Transaksi Berjalan</p>
                <p class="text-lg font-bold text-slate-800">{{ $transaksi->where('status', 'berjalan')->count() }} Unit</p>
            </div>
        </div>
        <div class="card p-4 flex items-center gap-4">
            <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                <i data-lucide="files" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-slate-400 font-medium">Total Dokumen Invoice</p>
                <p class="text-lg font-bold text-slate-800">{{ $transaksi->count() }} Transaksi</p>
            </div>
        </div>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="card p-5">
        <form action="{{ route('kasir.pembayaran.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 justify-between items-end md:items-center">
            <div class="flex flex-1 flex-col md:flex-row gap-3 w-full">
                {{-- Search Input --}}
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="form-input pl-10 w-full" placeholder="Cari Kode Transaksi atau Nama Pelanggan...">
                </div>

                {{-- Status Filter --}}
                <div class="w-full md:w-48">
                    <select name="status" class="form-select w-full" onchange="this.form.submit()">
                        <option value="">Semua Status Sewa</option>
                        <option value="berjalan" {{ request('status') === 'berjalan' ? 'selected' : '' }}>Berjalan (Aktif)</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai (Lunas)</option>
                        <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
            </div>

            @if(request()->filled('search') || request()->filled('status'))
            <a href="{{ route('kasir.pembayaran.index') }}" class="text-xs text-red-500 hover:underline font-semibold whitespace-nowrap">
                Reset Filter
            </a>
            @endif
        </form>
    </div>

    {{-- Tabel Utama Data Riwayat Keuangan --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-100">
                        <th class="px-5 py-3.5 font-semibold">Kode Transaksi</th>
                        <th class="px-5 py-3.5 font-semibold">Pelanggan</th>
                        <th class="px-5 py-3.5 font-semibold">Kendaraan</th>
                        <th class="px-5 py-3.5 font-semibold">Omset Pokok</th>
                        <th class="px-5 py-3.5 font-semibold">Akumulasi Denda</th>
                        <th class="px-5 py-3.5 font-semibold">Total Tagihan</th>
                        <th class="px-5 py-3.5 font-semibold text-center">Status Sewa</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-50 text-slate-700">
                    @forelse($transaksi as $t)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        {{-- Kode Transaksi --}}
                        <td class="px-5 py-4">
                            <span class="font-mono font-bold text-slate-800">{{ $t->kode_transaksi }}</span>
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $t->created_at->format('d M Y, H:i') }}</p>
                        </td>
                        
                        {{-- Pelanggan --}}
                        <td class="px-5 py-4">
                            <div class="font-medium text-slate-800">{{ $t->booking->pelanggan->user->name ?? '-' }}</div>
                            <p class="text-xs text-slate-400">{{ $t->booking->pelanggan->no_telepon ?? '-' }}</p>
                        </td>

                        {{-- Kendaraan --}}
                        <td class="px-5 py-4">
                            <div class="font-medium text-slate-700">{{ $t->booking->kendaraan->nama ?? '-' }}</div>
                            <span class="inline-block mt-0.5 text-xs bg-slate-100 text-slate-600 px-2 py-0.5 font-mono rounded-md border border-slate-200/40">
                                {{ $t->booking->kendaraan->plat_nomor ?? '-' }}
                            </span>
                        </td>

                        {{-- Omset Pokok --}}
                        <td class="px-5 py-4 font-medium text-slate-600">
                            Rp {{ number_format($t->total_biaya, 0, ',', '.') }}
                        </td>

                        {{-- Akumulasi Denda --}}
                        <td class="px-5 py-4">
                            @if($t->total_denda > 0)
                                <span class="font-semibold text-red-600">Rp {{ number_format($t->total_denda, 0, ',', '.') }}</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>

                        {{-- Total Pembayaran Bersih Akhir --}}
                        <td class="px-5 py-4 font-bold text-slate-900 font-mono">
                            @php
                                $potonganDeposit = $t->deposit->jumlah_dipotong ?? 0;
                                // Menghitung keselarasan total tagihan bersih akhir di kasir
                                $tagihanBersih = ($t->total_biaya + $t->total_denda) - $potonganDeposit;
                            @endphp
                            Rp {{ number_format(max(0, $tagihanBersih), 0, ',', '.') }}
                        </td>

                        {{-- Status Transaksi Induk --}}
                        <td class="px-5 py-4 text-center">
                            @php
                                $statusColors = [
                                    'berjalan' => 'badge-blue',
                                    'selesai' => 'badge-green',
                                    'dibatalkan' => 'badge-red'
                                ];
                                $labelStatus = [
                                    'berjalan' => 'Aktif/Sewa',
                                    'selesai' => 'Selesai Lunas',
                                    'dibatalkan' => 'Dibatalkan'
                                ];
                            @endphp
                            <span class="badge {{ $statusColors[$t->status] ?? 'badge-gray' }} text-xs">
                                {{ $labelStatus[$t->status] ?? ucfirst($t->status) }}
                            </span>
                        </td>

                        {{-- Tombol Aksi Detail / Nota --}}
                        <td class="px-5 py-4 text-center">
                            <div class="flex justify-center items-center gap-2">
                                <a href="{{ route('kasir.transaksi.show', $t->id) }}" 
                                   class="p-1.5 text-slate-400 hover:text-blue-600 bg-white hover:bg-blue-50 rounded-lg border border-slate-200 transition-all" 
                                   title="Lihat Detail Neraca Transaksi">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                
                                @if($t->status === 'selesai')
                                <a href="#" 
                                   class="p-1.5 text-slate-400 hover:text-emerald-600 bg-white hover:bg-emerald-50 rounded-lg border border-slate-200 transition-all" 
                                   title="Cetak Nota Pembayaran Invoice">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-slate-400 italic">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i data-lucide="folder-open" class="w-8 h-8 text-slate-300"></i>
                                <span>Tidak ada rekaman transaksi pembayaran yang ditemukan</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($transaksi->hasPages())
        <div class="px-5 py-4 border-t border-slate-50 bg-slate-50/50">
            {{ $transaksi->links() }}
        </div>
        @endif
    </div>
</div>
@endsection