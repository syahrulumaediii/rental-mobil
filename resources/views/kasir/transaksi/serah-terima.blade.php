@extends('layouts.app')

@section('title', 'Serah Terima Kendaraan')
@section('page-title', 'Serah Terima Kendaraan')
@section('breadcrumb', 'Kasir / Transaksi / Serah Terima')

@section('sidebar-nav')
    @include('components.sidebar-kasir')
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Info Booking --}}
    <div class="card p-5">
        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
            <i data-lucide="calendar-check" class="w-5 h-5 text-primary-600"></i> Informasi Booking
        </h3>
        <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm">
            @foreach([
                ['Kode Booking',  $booking->kode_booking],
                ['Pelanggan',     $booking->pelanggan->user->name],
                ['No. Telepon',   $booking->pelanggan->user->phone],
                ['Kendaraan',     $booking->kendaraan->nama.' ('.$booking->kendaraan->plat_nomor.')'],
                ['Kategori',      $booking->kendaraan->kategori->nama ?? '-'],
                ['Transmisi',     ucfirst($booking->kendaraan->transmisi)],
                ['Tanggal Mulai', $booking->tanggal_mulai?->format('d M Y')],
                ['Tanggal Selesai',$booking->tanggal_selesai?->format('d M Y')],
                ['Durasi',        $booking->durasi_hari.' hari'],
                ['Estimasi Biaya','Rp '.number_format($booking->estimasi_biaya,0,',','.')],
            ] as [$lbl,$val])
            <div class="flex justify-between py-1.5 border-b border-slate-50">
                <span class="text-slate-400">{{ $lbl }}</span>
                <span class="font-medium text-slate-700 text-right">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Form Serah Terima --}}
    <div class="card p-5">
        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2">
            <i data-lucide="clipboard-list" class="w-5 h-5 text-primary-600"></i> Form Serah Terima
        </h3>
        <form method="POST" action="{{ route('kasir.transaksi.proses-serah-terima', $booking) }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="form-label">Tanggal Pengambilan Aktual</label>
                    <input type="date" name="tanggal_ambil_aktual" value="{{ old('tanggal_ambil_aktual', date('Y-m-d')) }}" class="form-input" required>
                </div>

                <div>
                    <label class="form-label">Kondisi Bahan Bakar</label>
                    <select name="bahan_bakar_awal" class="form-input" required>
                        @foreach(['Full'=>'Full (F)','3/4'=>'3/4','1/2'=>'1/2','1/4'=>'1/4','Empty'=>'Empty (E)'] as $val=>$lbl)
                        <option value="{{ $val }}">{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">KM Odometer Awal</label>
                    <input type="number" name="km_odometer_awal" value="{{ old('km_odometer_awal') }}" class="form-input" required min="0" placeholder="Contoh: 15000">
                </div>

                <div>
                    <label class="form-label">Jumlah Deposit (Rp)</label>
                    <input type="number" name="jumlah_deposit" value="{{ old('jumlah_deposit', 500000) }}" class="form-input" required min="0" step="10000">
                </div>

                <div class="col-span-2">
                    <label class="form-label">Foto Kondisi Kendaraan</label>
                    <input type="file" name="foto_kondisi" accept="image/*" class="form-input">
                    <p class="text-xs text-slate-400 mt-1">Foto kendaraan sebelum diserahkan (opsional)</p>
                </div>

                <div class="col-span-2">
                    <label class="form-label">Catatan Kondisi</label>
                    <textarea name="catatan_kondisi" rows="2" class="form-input" placeholder="Catatan kondisi kendaraan saat penyerahan...">{{ old('catatan_kondisi') }}</textarea>
                </div>

                <div class="col-span-2 border-t border-slate-100 pt-4">
                    <h4 class="font-semibold text-slate-700 mb-3 text-sm">Pembayaran Awal (Opsional)</h4>
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
                            <input type="number" name="jumlah_bayar" value="{{ old('jumlah_bayar', 0) }}" class="form-input" min="0" step="1000">
                        </div>
                    </div>
                </div>

                <div class="col-span-2">
                    <label class="form-label">Catatan Kasir</label>
                    <textarea name="catatan_kasir" rows="2" class="form-input" placeholder="Catatan kasir...">{{ old('catatan_kasir') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-5 pt-4 border-t border-slate-100">
                <button type="submit" class="btn-primary flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> Konfirmasi Serah Terima
                </button>
                <a href="{{ route('kasir.dashboard') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
