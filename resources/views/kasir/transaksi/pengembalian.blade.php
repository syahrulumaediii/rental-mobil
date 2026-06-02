@extends('layouts.app')

@section('title', 'Proses Pengembalian')
@section('page-title', 'Proses Pengembalian Kendaraan')
@section('breadcrumb', 'Kasir / Transaksi / Pengembalian')

@section('sidebar-nav')
    @include('components.sidebar-kasir')
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Info Transaksi --}}
    <div class="card p-5">
        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
            <i data-lucide="receipt-text" class="w-5 h-5 text-primary-600"></i> Informasi Transaksi
        </h3>
        <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm">
            @foreach([
                ['Kode Transaksi',  $transaksi->kode_transaksi],
                ['Pelanggan',       $transaksi->booking->pelanggan->user->name],
                ['Kendaraan',       $transaksi->booking->kendaraan->nama.' ('.$transaksi->booking->kendaraan->plat_nomor.')'],
                ['Tgl Pengambilan', $transaksi->tanggal_ambil_aktual?->format('d M Y')],
                ['Tgl Kembali Plan',$transaksi->booking->tanggal_selesai?->format('d M Y')],
                ['Total Biaya Sewa','Rp '.number_format($transaksi->total_biaya,0,',','.')],
                ['Deposit Ditahan', 'Rp '.number_format($transaksi->deposit?->jumlah ?? 0,0,',','.')],
            ] as [$lbl,$val])
            <div class="flex justify-between py-1.5 border-b border-slate-50">
                <span class="text-slate-400">{{ $lbl }}</span>
                <span class="font-medium text-slate-700">{{ $val }}</span>
            </div>
            @endforeach
        </div>
        @php
            $telat = now()->gt($transaksi->booking->tanggal_selesai);
            $hariTelat = $telat ? now()->diffInDays($transaksi->booking->tanggal_selesai) : 0;
        @endphp
        @if($telat)
        <div class="mt-3 flex items-center gap-2 bg-red-50 border border-red-200 px-4 py-2.5 rounded-xl text-sm text-red-700 font-medium">
            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
            Terlambat {{ $hariTelat }} hari dari jadwal pengembalian!
        </div>
        @endif
    </div>

    {{-- Form Pengembalian --}}
    <form method="POST" action="{{ route('kasir.transaksi.proses-pengembalian', $transaksi) }}" enctype="multipart/form-data">
        @csrf
        <div class="space-y-5">
            <div class="card p-5">
                <h3 class="font-semibold text-slate-700 mb-4 text-sm">Kondisi Kendaraan Kembali</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Tanggal Kembali Aktual</label>
                        <input type="date" name="tanggal_kembali_aktual" value="{{ old('tanggal_kembali_aktual', date('Y-m-d')) }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Kondisi Bahan Bakar</label>
                        <select name="bahan_bakar_akhir" class="form-input" required>
                            @foreach(['Full'=>'Full (F)','3/4'=>'3/4','1/2'=>'1/2','1/4'=>'1/4','Empty'=>'Empty (E)'] as $val=>$lbl)
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
                        <input type="file" name="foto_kondisi_akhir" accept="image/*" class="form-input">
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Catatan Kondisi</label>
                        <textarea name="catatan_kondisi_akhir" rows="2" class="form-input" placeholder="Kerusakan, goresan, dll...">{{ old('catatan_kondisi_akhir') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Denda --}}
            <div class="card p-5" x-data="{ adaDenda: {{ old('jenis_denda') ? 'true' : 'false' }} }">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-slate-700 text-sm">Denda (jika ada)</h3>
                    <button type="button" @click="adaDenda=!adaDenda" class="text-xs font-semibold text-primary-600 hover:underline">
                        <span x-text="adaDenda ? 'Hapus Denda' : '+ Tambah Denda'"></span>
                    </button>
                </div>
                <div x-show="adaDenda" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Jenis Denda</label>
                        <select name="jenis_denda" class="form-input">
                            <option value="">-- Pilih --</option>
                            <option value="keterlambatan"  {{ old('jenis_denda')==='keterlambatan'?'selected':'' }}>Keterlambatan</option>
                            <option value="kerusakan"      {{ old('jenis_denda')==='kerusakan'?'selected':'' }}>Kerusakan</option>
                            <option value="bahan_bakar"    {{ old('jenis_denda')==='bahan_bakar'?'selected':'' }}>Kurang Bahan Bakar</option>
                            <option value="lainnya"        {{ old('jenis_denda')==='lainnya'?'selected':'' }}>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Jam Keterlambatan</label>
                        <input type="number" name="jumlah_jam_telat" value="{{ old('jumlah_jam_telat', 0) }}" class="form-input" min="0">
                    </div>
                    <div>
                        <label class="form-label">Tarif Denda (Rp/jam)</label>
                        <input type="number" name="tarif_denda" value="{{ old('tarif_denda', 0) }}" class="form-input" min="0" step="1000">
                    </div>
                    <div>
                        <label class="form-label">Total Denda (Rp)</label>
                        <input type="number" name="total_denda" value="{{ old('total_denda', 0) }}" class="form-input" min="0" step="1000">
                    </div>
                    <div class="col-span-2">
                        <label class="form-label">Keterangan</label>
                        <textarea name="keterangan_denda" rows="2" class="form-input" placeholder="Detail denda...">{{ old('keterangan_denda') }}</textarea>
                    </div>
                </div>
                <div x-show="!adaDenda" class="text-sm text-slate-400 italic">Tidak ada denda</div>
            </div>

            {{-- Deposit --}}
            <div class="card p-5">
                <h3 class="font-semibold text-slate-700 mb-4 text-sm">Pengembalian Deposit</h3>
                <div class="bg-slate-50 rounded-xl px-4 py-3 mb-4 text-sm">
                    Deposit ditahan: <strong class="text-slate-800">Rp {{ number_format($transaksi->deposit?->jumlah ?? 0, 0, ',', '.') }}</strong>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Potongan Deposit (Rp)</label>
                        <input type="number" name="potongan_deposit" value="{{ old('potongan_deposit', 0) }}" class="form-input" min="0" step="1000">
                    </div>
                    <div>
                        <label class="form-label">Alasan Potongan</label>
                        <input type="text" name="alasan_potongan" value="{{ old('alasan_potongan') }}" class="form-input" placeholder="Jika ada potongan...">
                    </div>
                </div>
            </div>

            {{-- Pembayaran Pelunasan --}}
            <div class="card p-5">
                <h3 class="font-semibold text-slate-700 mb-4 text-sm">Pembayaran Pelunasan</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Metode Pembayaran</label>
                        <select name="metode_pembayaran_id" class="form-input" required>
                            @foreach($metode as $m)
                            <option value="{{ $m->id }}">{{ $m->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Jumlah Bayar (Rp)</label>
                        <input type="number" name="jumlah_bayar" value="{{ old('jumlah_bayar', $transaksi->total_biaya) }}" class="form-input" required min="0" step="1000">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3 mt-5">
            <button type="submit" class="btn-primary flex items-center gap-2" onclick="return confirm('Konfirmasi pengembalian kendaraan ini?')">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Selesaikan Pengembalian
            </button>
            <a href="{{ route('kasir.transaksi.show', $transaksi) }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
