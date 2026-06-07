@extends('layouts.app')

@section('title', 'Laporan Pendapatan Komprehensif')
@section('page-title', 'Analisis Pendapatan & Arus Kas')
@section('breadcrumb', 'Admin / Laporan / Pendapatan')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
{{-- Filter Periode Laporan --}}
<div class="card p-4 mb-6">
    <form method="GET" class="flex flex-col sm:flex-row sm:items-end gap-4">
        <div class="flex-1 sm:flex-none">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Mulai Tanggal</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-input w-full sm:w-44 text-sm">
        </div>
        <div class="flex-1 sm:flex-none">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-input w-full sm:w-44 text-sm">
        </div>
        <button type="submit" class="btn-primary w-full sm:w-auto justify-center h-10 px-5 text-sm font-semibold flex items-center gap-2 mt-2 sm:mt-0">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i> Perbarui Data
        </button>
    </form>
</div>

{{-- Grid Ringkasan Finansial --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Card 1: Omset Bersih --}}
    <div class="card p-4 sm:p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
            <i data-lucide="wallet" class="w-6 h-6 text-emerald-600"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Net Omset Riil</p>
            <p class="text-lg sm:text-xl font-extrabold text-emerald-600 mt-0.5">Rp {{ number_format($summary['net_pendapatan'],0,',','.') }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">{{ $summary['transaksi_selesai'] }} Transaksi Selesai</p>
        </div>
    </div>

    {{-- Card 2: Biaya Sewa Murni --}}
    <div class="card p-4 sm:p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
            <i data-lucide="car" class="w-6 h-6 text-blue-600"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Pendapatan Sewa</p>
            <p class="text-lg sm:text-xl font-extrabold text-slate-800 mt-0.5">Rp {{ number_format($summary['total_biaya_sewa'],0,',','.') }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">Nilai kontrak berjalan/selesai</p>
        </div>
    </div>

    {{-- Card 3: Total Denda --}}
    <div class="card p-4 sm:p-5 flex items-center gap-4">
        <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center shrink-0">
            <i data-lucide="gavel" class="w-6 h-6 text-red-600"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Denda Terwujud</p>
            <p class="text-lg sm:text-xl font-extrabold text-red-600 mt-0.5">Rp {{ number_format($summary['total_denda'],0,',','.') }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">Keterlambatan & Kerusakan Dll</p>
        </div>
    </div>

    {{-- Card 4: Arus Kas Jaminan / Deposit --}}
{{-- Container utama dikunci agar selalu mendatar (flex-row/items-center) di semua ukuran layar --}}
<div class="card p-4 sm:p-5 flex items-center gap-4">
    
    {{-- Bagian Ikon Indikator --}}
    <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center shrink-0 border border-amber-100">
        <i data-lucide="shield-alert" class="w-6 h-6 text-amber-600"></i>
    </div>

    {{-- Konten Utama (Sekarang posisinya sejajar di samping ikon, bahkan di layar HP) --}}
    <div class="flex-1 min-w-0">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">
            Uang Jaminan / Deposit
        </p>

            {{-- Sub Grid Konten Data Status Keuangan --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1.5 text-xs font-semibold">
                <div class="flex justify-between items-center text-slate-700 py-0.5 border-b border-slate-50 sm:border-b-0">
                    <span class="text-slate-400 font-medium">Titipan</span>
                    <span class="font-bold">Rp {{ number_format($summary['total_deposit_masuk'] ?? 0, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between items-center text-red-600 py-0.5 border-b border-slate-50 sm:border-b-0">
                    <span class="text-slate-400 font-medium">Potong</span>
                    <span class="font-bold">- Rp {{ number_format($summary['total_deposit_potong'] ?? 0, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between items-center text-blue-600 py-0.5 border-b border-slate-50 sm:border-b-0">
                    <span class="text-slate-400 font-medium">Kembali</span>
                    <span class="font-bold">Rp {{ number_format($summary['total_deposit_dikembalikan'] ?? 0, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between items-center text-emerald-600 py-0.5 pt-1 sm:pt-0">
                    <span class="text-emerald-500 font-bold uppercase text-[10px] tracking-wider">Sisa Bersih</span>
                    <span class="font-black bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">Rp {{ number_format($summary['sisa_deposit'] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>


</div>

{{-- Tabel Utama Buku Kas Pendapatan Lengkap --}}
<div class="card mb-6 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
        <div>
            <h3 class="font-bold text-sm sm:text-base text-slate-700">Rincian Buku Kas Masuk & Deposit</h3>
            <p class="text-xs text-slate-400 mt-0.5">Menampilkan breakdown data finansial setiap order sewa</p>
        </div>
        <div class="self-start sm:self-center">
            <span class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 block sm:inline text-center">
                Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}
            </span>
        </div>
    </div>
    
    {{-- Responsive Table Wrapper --}}
    <div class="overflow-x-auto w-full">
        <table class="min-w-full divide-y divide-slate-100 text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/70 text-[11px] font-bold uppercase text-slate-400 tracking-wider">
                    <th class="py-3.5 px-5">Kode Ref</th>
                    <th class="py-3.5 px-5">Pelanggan / Unit</th>
                    <th class="py-3.5 px-5 text-center">Status</th>
                    <th class="py-3.5 px-5 text-right">Biaya Sewa</th>
                    <th class="py-3.5 px-5 text-right">Titipan Deposit</th>
                    <th class="py-3.5 px-5 text-right">Denda Terbuku</th>
                    <th class="py-3.5 px-5 text-right bg-emerald-50/50 text-emerald-700 font-extrabold">Total Bayar</th>
                    <th class="py-3.5 px-5 text-center">Kasir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-xs text-slate-600 whitespace-nowrap">
                @forelse($transaksi as $t)
                <tr class="hover:bg-slate-50/30 transition-colors">
                    {{-- Kode Ref --}}
                    <td class="py-3.5 px-5 font-mono font-bold text-blue-600">
                        {{ $t->kode_transaksi }}
                        <span class="block text-[10px] text-slate-400 font-normal mt-0.5 tracking-tight">{{ $t->created_at->format('d/m/Y H:i') }}</span>
                    </td>
                    {{-- Pelanggan / Kendaraan --}}
                    <td class="py-3.5 px-5">
                        <span class="font-semibold text-slate-700 block text-sm">{{ $t->booking->pelanggan->user->name ?? '-' }}</span>
                        <span class="text-xs text-slate-400 block mt-0.5 font-medium">{{ $t->booking->kendaraan->merk ?? '' }} {{ $t->booking->kendaraan->nama ?? '-' }}</span>
                    </td>
                    {{-- Status --}}
                    <td class="py-3.5 px-5 text-center">
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider
                            {{ $t->status == 'selesai' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            {{ $t->status }}
                        </span>
                    </td>
                    {{-- Biaya Sewa --}}
                    <td class="py-3.5 px-5 text-right font-semibold text-slate-700">
                        Rp {{ number_format($t->total_biaya, 0, ',', '.') }}
                    </td>
                    
                    {{-- Titipan Deposit --}}
                    <td class="py-3.5 px-5 text-right font-mono text-slate-500">
                        @if($t->deposit)
                            <span class="block text-slate-700 font-semibold" title="Uang jaminan awal">
                                M: Rp {{ number_format($t->deposit->jumlah, 0, ',', '.') }}
                            </span>
                            
                            @if($t->deposit->status == 'dipotong')
                                <span class="block text-[10px] text-red-600 font-bold mt-0.5" title="Alasan: {{ $t->deposit->alasan_potongan }}">
                                    ✂️ Potong: Rp {{ number_format($t->deposit->jumlah_dipotong, 0, ',', '.') }}
                                </span>
                            @elseif($t->deposit->status == 'dikembalikan')
                                <span class="block text-[10px] text-green-600 font-bold mt-0.5">
                                    ↩️ Dikembalikan
                                </span>
                            @else
                                <span class="block text-[10px] text-amber-600 font-bold mt-0.5">
                                    🔒 Ditahan (Aktif)
                                </span>
                            @endif
                        @else
                            <span class="text-slate-400 italic font-sans">Tanpa Deposit</span>
                        @endif
                    </td>

                    {{-- Denda --}}
                    <td class="py-3.5 px-5 text-right font-semibold {{ $t->total_denda > 0 ? 'text-red-600 font-bold' : 'text-slate-400 font-normal' }}">
                        {{ $t->total_denda > 0 ? 'Rp '.number_format($t->total_denda, 0, ',', '.') : '—' }}
                    </td>
                    {{-- Total Pendapatan Riil --}}
                    <td class="py-3.5 px-5 text-right font-extrabold text-emerald-700 bg-emerald-50/20">
                        Rp {{ number_format($t->total_bayar, 0, ',', '.') }}
                    </td>
                    {{-- Operator Kasir --}}
                    <td class="py-3.5 px-5 text-center text-slate-500 font-medium">
                        {{ $t->kasir->name ?? 'System' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-16 text-slate-400 font-medium text-xs">
                        <i data-lucide="folder-open" class="w-6 h-6 mx-auto mb-2 text-slate-300"></i>
                        Tidak ditemukan rekap transaksi keluar masuk pada periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection