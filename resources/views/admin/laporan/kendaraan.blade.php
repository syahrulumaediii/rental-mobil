@extends('layouts.app')

@section('title', 'Laporan Analisis Kendaraan')
@section('page-title', 'Analisis Produktivitas & Utilitas Armada')
@section('breadcrumb', 'Admin / Laporan / Kendaraan')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
{{-- Filter Periode & Karakteristik Unit --}}
<div class="card p-4 sm:p-5 mb-6">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:flex lg:flex-wrap gap-4 items-end">
        <div class="w-full lg:w-auto flex-1 min-w-[160px]">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Mulai Kontrak</label>
            <input type="date" name="dari" value="{{ $dari }}" class="form-input w-full text-sm">
        </div>
        <div class="w-full lg:w-auto flex-1 min-w-[160px]">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Sampai Kontrak</label>
            <input type="date" name="sampai" value="{{ $sampai }}" class="form-input w-full text-sm">
        </div>
        <div class="w-full lg:w-auto flex-1 min-w-[160px]">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Kategori</label>
            <select name="kategori_id" class="form-input w-full text-sm">
                <option value="">Semua Kategori</option>
                @foreach($kategori as $kat)
                    <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full lg:w-auto flex-1 min-w-[160px]">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Status Unit</label>
            <select name="status" class="form-input w-full text-sm">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="non-aktif" {{ request('status') == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                <option value="disewa" {{ request('status') == 'disewa' ? 'selected' : '' }}>Disewa</option>
                <option value="servis" {{ request('status') == 'servis' ? 'selected' : '' }}>Servis</option>
            </select>
        </div>
        <div class="w-full lg:w-auto pt-2 lg:pt-0">
            <button type="submit" class="btn-primary w-full justify-center h-10 px-5 text-sm font-semibold flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i> Filter Data
            </button>
        </div>
    </form>
</div>

{{-- Grid Summary Status Fisik & Kontribusi Omset --}}
<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    {{-- Card 1: Total Aset --}}
    <div class="card p-4 flex items-center gap-3 sm:gap-4">
        <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center shrink-0">
            <i data-lucide="layers" class="w-5 h-5 text-slate-500"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Total Armada</p>
            <p class="text-base sm:text-lg font-extrabold text-slate-800 mt-0.5">{{ $summary['total_unit'] }} Unit</p>
        </div>
    </div>

    {{-- Card 2: Tersedia --}}
    <div class="card p-4 flex items-center gap-3 sm:gap-4">
        <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center shrink-0">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Siap Jalan</p>
            <p class="text-base sm:text-lg font-extrabold text-green-600 mt-0.5">{{ $summary['unit_aktif'] }} Unit</p>
        </div>
    </div>

    {{-- Card 3: Sedang Operasional --}}
    <div class="card p-4 flex items-center gap-3 sm:gap-4">
        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
            <i data-lucide="navigation" class="w-5 h-5 text-blue-600"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Sedang Jalan</p>
            <p class="text-base sm:text-lg font-extrabold text-blue-600 mt-0.5">{{ $summary['unit_disewa'] }} Unit</p>
        </div>
    </div>

    {{-- Card 4: Masuk Bengkel --}}
    <div class="card p-4 flex items-center gap-3 sm:gap-4">
        <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center shrink-0">
            <i data-lucide="wrench" class="w-5 h-5 text-red-600"></i>
        </div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Perawatan</p>
            <p class="text-base sm:text-lg font-extrabold text-red-600 mt-0.5">{{ $summary['unit_perawatan'] }} Unit</p>
        </div>
    </div>

    {{-- Card 5: Total Akumulasi Nilai Sewa --}}
    <div class="card p-4 flex items-center gap-3 sm:gap-4 col-span-2 sm:col-span-2 lg:col-span-1 bg-gradient-to-br from-emerald-50/60 to-white border-emerald-100">
        <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shrink-0 shadow-xs">
            <i data-lucide="banknote" class="w-5 h-5 text-white"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-[10px] font-bold text-emerald-700 uppercase tracking-wide truncate">Omset Kontrak</p>
            <p class="text-sm sm:text-base font-black text-emerald-700 mt-0.5 truncate">Rp {{ number_format($summary['total_omset_sewa'], 0, ',', '.') }}</p>
        </div>
    </div>
</div>

{{-- Tabel Analisis Kontribusi & Utilitas Setiap Mobil --}}
<div class="card overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
        <div>
            <h3 class="font-bold text-slate-700 text-sm sm:text-base">Peringkat Utilitas & Yield Performa Unit</h3>
            <p class="text-xs text-slate-400 mt-0.5">Menilai efektivitas perputaran sewa unit armada dan nilai ekonomis yang dihasilkan</p>
        </div>
        <div class="self-start sm:self-center">
            <span class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 border border-slate-200/60 block sm:inline text-center whitespace-nowrap">
                Rentang Analisis: {{ \Carbon\Carbon::parse($dari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($sampai)->format('d/m/Y') }}
            </span>
        </div>
    </div>

    {{-- Responsive Table Wrapper --}}
    <div class="overflow-x-auto w-full">
        <table class="min-w-full divide-y divide-slate-100 text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/70 text-[11px] font-bold uppercase text-slate-400 tracking-wider">
                    <th class="py-3.5 px-5">Info Kendaraan</th>
                    <th class="py-3.5 px-5">Kategori / Spek</th>
                    <th class="py-3.5 px-5 text-center">Status Fisik</th>
                    <th class="py-3.5 px-5 text-right">Tarif Harian</th>
                    <th class="py-3.5 px-5 text-center">Frekuensi Disewa</th>
                    <th class="py-3.5 px-5 text-right bg-blue-50/30 text-blue-800 font-extrabold">Total Omset Sewa</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-xs text-slate-600 whitespace-nowrap">
                @forelse($kendaraan as $k)
                <tr class="hover:bg-slate-50/30 transition-colors">
                    {{-- Detail Identitas Unit --}}
                    <td class="py-3.5 px-5">
                        <div class="flex items-center gap-3">
                            @if($k->foto)
                                <img src="{{ asset('storage/' . $k->foto) }}" class="w-12 h-9 rounded-lg object-cover border border-slate-100 bg-slate-50 shrink-0">
                            @else
                                <div class="w-12 h-9 rounded-lg bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200 text-slate-400">
                                    <i data-lucide="image" class="w-4 h-4"></i>
                                </div>
                            @endif

                            <div>
                                <span class="font-bold text-slate-800 block text-sm">
                                    {{ $k->merk }} {{ $k->nama }}
                                </span>
                                <span class="font-mono bg-slate-100 text-slate-600 font-bold px-1.5 py-0.5 rounded text-[10px] inline-block mt-1 uppercase tracking-wider border border-slate-200/40">
                                    {{ $k->plat_nomor }}
                                </span>
                            </div>
                        </div>
                    </td>

                    {{-- Spesifikasi & Kategori --}}
                    <td class="py-3.5 px-5">
                        <span class="font-semibold text-slate-700 block text-sm">
                            {{ $k->kategori->nama ?? '—' }}
                        </span>
                        <span class="text-slate-400 text-xs block mt-0.5 capitalize font-medium">
                            {{ $k->transmisi }} • {{ $k->bahan_bakar }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td class="py-3.5 px-5 text-center">
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider inline-block
                            @if($k->status == 'aktif')
                                bg-green-50 text-green-700 border border-green-200
                            @elseif($k->status == 'disewa')
                                bg-blue-50 text-blue-700 border border-blue-200
                            @else
                                bg-red-50 text-red-700 border border-red-200
                            @endif">
                            {{ $k->status }}
                        </span>
                    </td>

                    {{-- Tarif --}}
                    <td class="py-3.5 px-5 text-right font-semibold text-slate-700">
                        Rp {{ number_format($k->tarif_harian, 0, ',', '.') }}
                    </td>

                    {{-- Total Disewa --}}
                    <td class="py-3.5 px-5 text-center">
                        <span class="px-3 py-1 rounded-md font-bold bg-slate-50 text-slate-700 border border-slate-200/60 text-xs">
                            {{ $k->total_disewa }} Kali
                        </span>
                    </td>

                    {{-- Omset --}}
                    <td class="py-3.5 px-5 text-right font-mono font-extrabold text-blue-700 bg-blue-50/20 text-sm">
                        Rp {{ number_format($k->omset_kendaraan ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-16 text-slate-400 font-medium text-xs">
                        <i data-lucide="folder-open" class="w-6 h-6 mx-auto mb-2 text-slate-300"></i>
                        Tidak ditemukan rekaman performa utilitas armada untuk kriteria filter ini.
                    </td>
                </tr>
                @endforelse
            </tbody>


        </table>
    </div>
</div>
@endsection