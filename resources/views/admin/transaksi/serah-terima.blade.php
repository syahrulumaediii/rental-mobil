@extends('layouts.app')

@section('title', 'Serah Terima Kendaraan (Admin Mode)')
@section('page-title', 'Serah Terima Kendaraan')
@section('breadcrumb', 'Admin / Transaksi / Serah Terima')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="max-w-3xl mx-auto space-y-5 px-2 sm:px-0">

    {{-- Info Booking --}}
    <div class="card p-4 sm:p-5">
        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2 text-base sm:text-lg">
            <i data-lucide="calendar-check" class="w-5 h-5 text-primary-600"></i> Informasi Booking
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-1 text-sm">
            @foreach([
                ['Kode Booking',  $booking->kode_booking],
                ['Pelanggan',     $booking->pelanggan->user->name],
                ['No. Telepon',   $booking->pelanggan->user->phone],
                ['Kendaraan',     $booking->kendaraan->nama.' ('.$booking->kendaraan->plat_nomor.')'],
                ['Kategori',      $booking->kendaraan->kategori->nama ?? '-'],
                ['Transmisi',      ucfirst($booking->kendaraan->transmisi)],
                ['Tanggal Mulai', $booking->tanggal_mulai ? $booking->tanggal_mulai->locale('id')->translatedFormat('l, d F Y H:i') : '-'],
                ['Tanggal Selesai', $booking->tanggal_selesai ? $booking->tanggal_selesai->locale('id')->translatedFormat('l, d F Y H:i') : '-'],
                ['Durasi',        $booking->durasi_hari.' hari'],
                ['Estimasi Biaya','Rp '.number_format($booking->estimasi_biaya,0,',','.')],
            ] as [$lbl,$val])
            <div class="flex flex-col sm:flex-row sm:justify-between py-2 border-b border-slate-50 last:border-0 sm:last:border-b">
                <span class="text-xs text-slate-400 uppercase tracking-wider sm:normal-case sm:text-sm">{{ $lbl }}</span>
                <span class="font-medium text-slate-700 sm:text-right mt-0.5 sm:mt-0 wrap-break-word">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Form Serah Terima --}}
    <div class="card p-4 sm:p-5">
        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2 text-base sm:text-lg">
            <i data-lucide="clipboard-list" class="w-5 h-5 text-primary-600"></i> Form Serah Terima
        </h3>
        <form method="POST" action="{{ route('admin.transaksi.proses-serah-terima', $booking) }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="form-label">Tanggal Pengambilan Aktual</label>
                    <input type="text" 
                        id="tanggal_ambil_aktual"
                        name="tanggal_ambil_aktual" 
                        value="{{ old('tanggal_ambil_aktual', now()->timezone('Asia/Jakarta')->format('d-m-Y H:i')) }}" 
                        class="form-input w-full bg-white" 
                        placeholder="Pilih Tanggal & Waktu"
                        required>
                </div>

                <div>
                    <label class="form-label">Kondisi Bahan Bakar</label>
                    <select name="bahan_bakar_awal" class="form-input w-full" required>
                        @foreach(['penuh'=>'Full (F)','3/4'=>'3/4','1/2'=>'1/2','1/4'=>'1/4','kosong'=>'Empty (E)'] as $val=>$lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">KM Odometer Awal</label>
                    <input type="number" name="km_odometer_awal" value="{{ old('km_odometer_awal') }}" class="form-input w-full" required min="0" placeholder="Contoh: 15000">
                </div>

                @php
                    $depositAwal = $booking->nominal_deposit ?? 0;
                @endphp

                <div>
                    <label class="form-label font-semibold text-slate-700">Uang Jaminan (Deposit)</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-slate-400 text-sm">Rp</span>
                        </div>
                        <input type="number" 
                               name="jumlah_deposit" 
                               id="jumlah_deposit"
                               value="{{ old('jumlah_deposit', $depositAwal) }}" 
                               class="form-input pl-9 w-full font-semibold text-green-700 bg-emerald-50/20 focus:bg-white" 
                               min="0" 
                               step="1000"
                               placeholder="Tentukan nominal jaminan...">
                    </div>
                    @if($depositAwal > 0)
                        <p class="text-[11px] text-slate-400 mt-1 italic leading-relaxed">
                            * Rekomendasi awal dari booking: <strong>Rp {{ number_format($depositAwal, 0, ',', '.') }}</strong> (Bisa diedit/ditambah).
                        </p>
                    @else
                        <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">
                            Isi jika admin ingin menerapkan wajib deposit di tempat.
                        </p>
                    @endif
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Foto Kondisi Kendaraan</label>
                    <input type="file" name="foto_kondisi" accept="image/*" class="form-input w-full">
                    <p class="text-xs text-slate-400 mt-1">Foto kendaraan sebelum diserahkan (opsional)</p>
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Catatan Kondisi</label>
                    <textarea name="catatan_kondisi" rows="2" class="form-input w-full" placeholder="Catatan kondisi kendaraan saat penyerahan...">{{ old('catatan_kondisi') }}</textarea>
                </div>

                <div class="sm:col-span-2 border-t border-slate-100 pt-4">
                    <h4 class="font-semibold text-slate-700 mb-3 text-sm">Pembayaran Awal (Opsional)</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Metode Pembayaran</label>
                            <select name="metode_pembayaran_id" class="form-input w-full" required>
                                @foreach($metode as $m)
                                <option value="{{ $m->id }}">{{ $m->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Jumlah Bayar (Rp)</label>
                            <input type="number" 
                                   name="jumlah_bayar" 
                                   id="jumlah_bayar" 
                                   value="{{ old('jumlah_bayar', 0) }}" 
                                   class="form-input w-full font-bold text-slate-800 bg-slate-50" 
                                   min="0" 
                                   step="1000">
                        </div>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Catatan Kasir</label>
                    <textarea name="catatan_kasir" rows="2" class="form-input w-full" placeholder="Catatan admin...">{{ old('catatan_kasir') }}</textarea>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-5 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.booking.index') }}" class="btn-secondary w-full sm:w-auto text-center justify-center py-2.5 sm:py-2">Batal</a>
                <button type="submit" class="btn-primary w-full sm:w-auto flex items-center justify-center gap-2 py-2.5 sm:py-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> Konfirmasi Serah Terima
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const estimasiBiaya = Number({!! json_encode($booking->estimasi_biaya ?? 0) !!}) || 0;
        const inputDeposit = document.getElementById('jumlah_deposit');
        const inputJumlahBayar = document.getElementById('jumlah_bayar');

        if (inputDeposit && inputJumlahBayar) {
            function hitungTotalPembayaran() {
                let nilaiDeposit = inputDeposit.value.trim();
                let nominalDeposit = parseFloat(nilaiDeposit);
                if (isNaN(nominalDeposit) || nominalDeposit < 0) {
                    nominalDeposit = 0;
                }
                const totalBayarOtomatis = estimasiBiaya + nominalDeposit;
                inputJumlahBayar.value = totalBayarOtomatis;
            }

            hitungTotalPembayaran();
            inputDeposit.addEventListener('input', hitungTotalPembayaran);
            inputDeposit.addEventListener('change', hitungTotalPembayaran);
        } else {
            console.error('Kritikal: Elemen input_deposit atau jumlah_bayar tidak ditemukan di halaman.');
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        flatpickr("#tanggal_ambil_aktual", {
            enableTime: true,
            time_24hr: true,
            dateFormat: "d-m-Y H:i",
            allowInput: true
        });
    });
</script>
@endsection