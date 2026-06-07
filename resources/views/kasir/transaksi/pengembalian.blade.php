@extends('layouts.app')

@section('title', 'Proses Pengembalian')
@section('page-title', 'Proses Pengembalian Kendaraan')
@section('breadcrumb', 'Kasir / Transaksi / Pengembalian')

@section('sidebar-nav')
    @include('components.sidebar-kasir')
@endsection

@section('content')

@vite('resources/js/transaksi/pengembalian.js')

<div class="max-w-3xl mx-auto space-y-5 px-1 sm:px-0">

    @php
        // Status pembayaran sewa (pembayaran pertama = pelunasan sewa)
        $pembayaranSewa = $transaksi->pembayaran
            ->where('status', 'lunas')
            ->sortBy('created_at')
            ->first();
        $sewaSudahLunas = $pembayaranSewa !== null;

        // Deposit yang masih ditahan
        $jumlahDeposit  = $transaksi->deposit?->jumlah ?? 0;

        // Keterlambatan
        $batasKembali   = $transaksi->booking->tanggal_selesai;
        $sekarang       = now();
        $telat          = $sekarang->gt($batasKembali);
        $hariTelat      = $telat ? (int) $sekarang->diffInDays($batasKembali) : 0;
    @endphp

    {{-- ================================================================== --}}
    {{-- INFO TRANSAKSI                                                     --}}
    {{-- ================================================================== --}}
    <div class="card p-4 sm:p-5">
        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2 text-sm sm:text-base">
            <i data-lucide="receipt-text" class="w-5 h-5 text-primary-600"></i> Informasi Transaksi
        </h3>

        {{-- Diubah ke grid-cols-1 di HP agar data text panjang aman --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-1 text-sm">
            @foreach([
                ['Kode Transaksi',   $transaksi->kode_transaksi],
                ['Pelanggan',        $transaksi->booking->pelanggan->user->name],
                ['Kendaraan',        $transaksi->booking->kendaraan->nama.' ('.$transaksi->booking->kendaraan->plat_nomor.')'],
                ['Tgl Pengambilan',  $transaksi->tanggal_ambil_aktual?->format('d M Y')],
                ['Tgl Kembali Plan', $batasKembali?->format('d M Y')],
                ['Total Biaya Sewa', 'Rp '.number_format($transaksi->total_biaya, 0, ',', '.')],
                ['Deposit Ditahan',  'Rp '.number_format($jumlahDeposit, 0, ',', '.')],
            ] as [$lbl, $val])
            <div class="flex flex-col sm:flex-row sm:justify-between py-2 border-b border-slate-50 last:border-0 sm:last:border-b">
                <span class="text-xs text-slate-400 uppercase tracking-wider sm:normal-case sm:text-sm">{{ $lbl }}</span>
                <span class="font-medium text-slate-700 mt-0.5 sm:mt-0 break-all sm:text-right">{{ $val }}</span>
            </div>
            @endforeach

            <div class="flex flex-col sm:flex-row sm:justify-between py-2 border-b border-slate-50 sm:col-span-2">
                <span class="text-xs text-slate-400 uppercase tracking-wider sm:normal-case sm:text-sm">Status Pembayaran Sewa</span>
                @if($sewaSudahLunas)
                    <span class="inline-flex flex-wrap items-center gap-1 text-emerald-700 font-semibold mt-0.5 sm:mt-0">
                        <i data-lucide="circle-check-big" class="w-4 h-4 shrink-0"></i> LUNAS
                        <span class="font-normal text-xs text-slate-400 block sm:inline">
                            (dibayar {{ $pembayaranSewa->created_at->format('d M Y') }} via {{ $pembayaranSewa->metodePembayaran->nama }})
                        </span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-red-600 font-semibold mt-0.5 sm:mt-0">
                        <i data-lucide="circle-x" class="w-4 h-4 shrink-0"></i> BELUM LUNAS
                    </span>
                @endif
            </div>
        </div>

        @if($telat)
        <div class="mt-4 flex items-start gap-3 bg-red-50 border border-red-200 px-4 py-3 rounded-xl text-xs sm:text-sm text-red-700">
            <i data-lucide="triangle-alert" class="w-5 h-5 shrink-0 mt-0.5"></i>
            <div>
                <p class="font-bold text-red-800">Kendaraan TERLAMBAT dikembalikan!</p>
                <p class="mt-1 leading-relaxed">
                    Batas kembali: <strong>{{ $batasKembali->format('d M Y') }}</strong> &mdash; Hari ini: <strong>{{ $sekarang->format('d M Y') }}</strong><br>
                    Selisih keterlambatan: <strong class="underline">{{ $hariTelat }} hari</strong>. Pastikan denda keterlambatan dikenakan di bawah.
                </p>
            </div>
        </div>
        @else
        <div class="mt-4 flex items-center gap-2 bg-emerald-50 border border-emerald-200 px-4 py-2.5 rounded-xl text-xs sm:text-sm text-emerald-700 font-medium">
            <i data-lucide="circle-check-big" class="w-4 h-4 shrink-0"></i> Pengembalian tepat waktu. Batas maksimal: {{ $batasKembali->format('d M Y') }}.
        </div>
        @endif
    </div>

    {{-- ================================================================== --}}
    {{-- FORM PENGEMBALIAN                                                  --}}
    {{-- ================================================================== --}}
    <form method="POST" action="{{ route('kasir.transaksi.proses-pengembalian', $transaksi) }}" enctype="multipart/form-data" id="form-pengembalian">
    @csrf

    <div id="pengembalian-data" 
        class="hidden"
        data-old-dendas="{{ json_encode(old('dendas', [])) }}"
        data-hari-telat="{{ $hariTelat }}"
        data-telat="{{ $telat ? 'true' : 'false' }}"
        data-jumlah-deposit="{{ $jumlahDeposit }}">
    </div>

    <div class="space-y-5">
        {{-- Input Kondisi Fisik Kendaraan --}}
        <div class="card p-4 sm:p-5">
            <h3 class="font-semibold text-slate-700 mb-4 text-sm border-b border-slate-50 pb-2">Kondisi Kendaraan Kembali</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Tanggal Kembali Aktual</label>
                    <input type="date" name="tanggal_kembali_aktual" value="{{ old('tanggal_kembali_aktual', date('Y-m-d')) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Kondisi Bahan Bakar</label>
                    <select name="bahan_bakar_akhir" class="form-input" required>
                        @foreach(['penuh' => 'Full (F)', '3/4' => '3/4', '1/2' => '1/2', '1/4' => '1/4', 'kosong' => 'Empty (E)'] as $val => $lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">KM Odometer Akhir</label>
                    <input type="number" name="km_odometer_akhir" value="{{ old('km_odometer_akhir') }}" class="form-input" required min="0" placeholder="Contoh: 16500">
                </div>
                <div>
                    <label class="form-label">Foto Kondisi Kembali</label>
                    <input type="file" name="foto_kondisi_akhir" accept="image/*" class="form-input text-xs">
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Catatan Kondisi</label>
                    <textarea name="catatan_kondisi_akhir" rows="2" class="form-input" placeholder="Kerusakan, goresan, komponen hilang jika ada...">{{ old('catatan_kondisi_akhir') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Input Klausa Denda --}}
        <div class="card p-4 sm:p-5">
            <div class="flex items-center justify-between mb-4 border-b border-slate-50 pb-2">
                <h3 class="font-semibold text-slate-700 text-sm">Denda (jika ada)</h3>
                <button type="button" id="btn-tambah-denda" class="inline-flex items-center gap-1 text-xs font-bold text-primary-600 hover:text-primary-700 bg-primary-50 px-2.5 py-1 rounded-lg">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Tambah Denda
                </button>
            </div>
            <div id="denda-container" class="space-y-4"></div>
            <div id="denda-kosong-info" class="text-xs sm:text-sm text-slate-400 italic text-center py-6 border border-dashed border-slate-200 rounded-xl">
                Tidak ada denda baru. Klik tombol di atas jika kendaraan mengalami kerusakan atau telat.
            </div>
            <div id="subtotal-denda-box" class="mt-4 hidden justify-between items-center bg-amber-50 border border-amber-200 px-4 py-3 rounded-xl text-sm font-bold text-amber-800">
                <span>Total Seluruh Denda</span>
                <span id="text-total-denda" class="font-mono text-base">Rp 0</span>
            </div>
        </div>

        {{-- REKAPITULASI DEPOSIT --}}
        <div class="card p-4 sm:p-5">
            <h3 class="font-semibold text-slate-700 mb-4 text-sm flex items-center gap-2">
                <i data-lucide="shield-check" class="w-4 h-4 text-amber-500"></i> Rekapitulasi Pengembalian Deposit
            </h3>
            
            <div class="space-y-4 text-sm">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50 p-4 rounded-xl">
                    <div class="flex justify-between sm:flex-col sm:gap-1 py-1">
                        <span class="text-slate-500">Deposit Awal Ditahan:</span>
                        <strong class="text-slate-800 font-mono text-base" id="text-deposit-ditahan">
                            Rp {{ number_format($jumlahDeposit, 0, ',', '.') }}
                        </strong>
                    </div>
                    <div class="flex justify-between sm:flex-col sm:gap-1 py-1 border-t border-slate-200/60 sm:border-t-0 sm:border-l sm:pl-4">
                        <span class="text-slate-500">Potongan Denda (Otomatis):</span>
                        <strong class="text-red-600 font-mono text-base" id="text-deposit-terpotong">
                            Rp 0
                        </strong>
                    </div>
                </div>

                <div>
                    <label class="form-label font-medium text-slate-600 mb-1 block">Alasan Potongan / Catatan Detail Kerusakan</label>
                    <input type="text" 
                           name="alasan_potongan" 
                           value="{{ old('alasan_potongan') }}" 
                           class="form-input w-full" 
                           placeholder="Sebutkan alasan atau kronologis jika ada pemotongan kas uang jaminan...">
                </div>

                <div class="bg-emerald-50 border border-emerald-200 px-4 py-3 rounded-xl flex flex-col sm:flex-row gap-2 sm:justify-between sm:items-center">
                    <span class="text-emerald-700 font-semibold text-xs sm:text-sm">Sisa Dana Deposit Dikembalikan Ke Pelanggan</span>
                    <strong class="text-emerald-800 text-base font-mono" id="text-deposit-kembali">
                        Rp {{ number_format($jumlahDeposit, 0, ',', '.') }}
                    </strong>
                </div>
            </div>
        </div>

        {{-- RINGKASAN TAGIHAN AKHIR --}}
        <div class="card p-4 sm:p-5 border-2 border-primary-100 bg-primary-50/30">
            <h3 class="font-semibold text-slate-700 mb-4 text-sm flex items-center gap-2">
                <i data-lucide="calculator" class="w-4 h-4 text-primary-600"></i> Ringkasan Tagihan Pengembalian
            </h3>
            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                    <span class="text-slate-500 flex items-center gap-1">
                        <i data-lucide="circle-check-big" class="w-3.5 h-3.5 text-emerald-500 shrink-0"></i> Biaya Sewa (sudah lunas)
                    </span>
                    <span class="text-slate-400 font-mono line-through text-xs sm:text-sm">Rp {{ number_format($transaksi->total_biaya, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Total Denda Terpilih</span>
                    <span class="font-medium text-red-600 font-mono" id="ringkasan-total-denda">+ Rp 0</span>
                </div>
                <div class="flex justify-between items-center py-1.5 border-b border-slate-100">
                    <span class="text-slate-500">Klaim Kas Tercover Uang Deposit</span>
                    <span class="font-medium text-emerald-700 font-mono" id="ringkasan-potongan-deposit">− Rp 0</span>
                </div>
                <div class="flex flex-col sm:flex-row gap-1 sm:justify-between sm:items-center pt-3">
                    <span class="font-bold text-slate-700 text-sm sm:text-base" id="label-status-bayar">Sisa Kurang Bayar</span>
                    <span class="font-mono font-extrabold text-lg sm:text-xl text-red-600" id="text-jumlah-harus-bayar">Rp 0</span>
                </div>
            </div>
        </div>

        {{-- INPUT PEMBAYARAN KASIR (Denda Baru) --}}
        <div class="card p-4 sm:p-5 hidden" id="box-pembayaran-denda">
            <h3 class="font-semibold text-slate-700 mb-4 text-sm border-b border-slate-50 pb-2">Pembayaran Denda</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="metode_pembayaran_id" id="select-metode-pembayaran" class="form-input">
                        @foreach($metode as $m)
                        <option value="{{ $m->id }}">{{ $m->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Jumlah Bayar (Rp)</label>
                    <input type="number" name="jumlah_bayar" id="input-jumlah-bayar" value="0" class="form-input font-mono" min="0" step="1000">
                    <p class="text-xs text-slate-400 mt-1">Sisa kekurangan denda nyata yang wajib ditagih ke penyewa.</p>
                </div>
            </div>
        </div>

        {{-- INFO KONDISI IMPAS --}}
        <div class="card p-4 border border-emerald-200 bg-emerald-50" id="box-info-impas">
            <div class="flex items-center gap-2 text-xs sm:text-sm text-emerald-700 font-medium">
                <i data-lucide="circle-check-big" class="w-4 h-4 shrink-0"></i>
                <span id="text-info-impas">Selesai aman tanpa tagihan denda baru.</span>
            </div>
            <input type="hidden" name="jumlah_bayar" id="hidden-jumlah-bayar" value="0">
        </div>
    </div>

    {{-- Aksi Submit/Batal --}}
    <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-2">
        <button type="submit" class="btn-primary w-full sm:w-auto order-1 sm:order-2 flex items-center justify-center gap-2 py-3 sm:py-2" onclick="return confirm('Konfirmasi penyelesaian transaksi & pengembalian kendaraan ini?')">
            <i data-lucide="check-circle" class="w-4 h-4"></i> Selesaikan Pengembalian
        </button>
        <a href="{{ route('kasir.transaksi.show', $transaksi) }}" class="btn-secondary w-full sm:w-auto order-2 sm:order-1 text-center py-3 sm:py-2">
            Batal & Kembali
        </a>
    </div>
</form>

</div>

@endsection