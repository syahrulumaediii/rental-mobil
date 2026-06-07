@extends('layouts.app')

@section('title', 'Serah Terima Kendaraan (Admin Mode)')
@section('page-title', 'Serah Terima Kendaraan')
@section('breadcrumb', 'Admin / Transaksi / Serah Terima')

@section('sidebar-nav')
    @include('components.sidebar-admin')
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
            ] as $info)
            <div>
                <p class="text-slate-400 text-xs">{{ $info[0] }}</p>
                <p class="font-semibold text-slate-700 mt-0.5">{{ $info[1] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Form Proses --}}
    <form action="{{ route('admin.transaksi.proses-serah-terima', $booking) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        
        <div class="card p-5 space-y-4">
            <h3 class="font-bold text-slate-700 mb-2 flex items-center gap-2">
                <i data-lucide="shield-alert" class="w-5 h-5 text-primary-600"></i> Pengecekan Fisik & Odometer awal
            </h3>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Tanggal Ambil Aktual</label>
                    <input type="datetime-local" name="tanggal_ambil_aktual" value="{{ old('tanggal_ambil_aktual', now()->format('Y-m-d\TH:i')) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Bahan Bakar Awal</label>
                    <input type="text" name="bahan_bakar_awal" value="{{ old('bahan_bakar_awal') }}" class="form-input" placeholder="Contoh: Full, 3/4, Berkedip">
                </div>
                <div>
                    <label class="form-label">KM Odometer Awal</label>
                    <input type="number" name="km_odometer_awal" value="{{ old('km_odometer_awal') }}" class="form-input" placeholder="0">
                </div>
                <div>
                    <label class="form-label">Foto Kondisi Fisik</label>
                    <input type="file" name="foto_kondisi" class="form-input">
                </div>
            </div>
            <div>
                <label class="form-label">Catatan Kondisi / Kerusakan Eksisting</label>
                <textarea name="catatan_kondisi" class="form-input h-20" placeholder="Catat jika ada lecet atau kendala bawaan kompartemen armada..."></textarea>
            </div>
        </div>

        <div class="card p-5 space-y-4">
            <h3 class="font-bold text-slate-700 mb-2 flex items-center gap-2">
                <i data-lucide="wallet" class="w-5 h-5 text-primary-600"></i> Jaminan & Administrasi Kas
            </h3>
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Jumlah Uang Deposit (Rp)</label>
                    <input type="number" name="jumlah_deposit" id="jumlah_deposit" value="{{ old('jumlah_deposit', 0) }}" class="form-input" min="0">
                </div>
                <div>
                    <label class="form-label">Metode Pembayaran</label>
                    <select name="metode_pembayaran_id" class="form-input">
                        @foreach($metode as $m)
                        <option value="{{ $m->id }}">{{ $m->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Jumlah Bayar Total (Sewa + Deposit)</label>
                    <input type="number" name="jumlah_bayar" id="jumlah_bayar" class="form-input" readonly>
                </div>
            </div>
            <div>
                <label class="form-label">Catatan Kasir / Memo Lapangan</label>
                <textarea name="catatan_kasir" class="form-input h-20" placeholder="Tambahkan catatan instruksi tambahan jika ada..."></textarea>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Mulai Transaksi Sewa (Admin)
            </button>
            <a href="{{ route('admin.transaksi.index') }}" class="btn-secondary">Kembali</a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const estimasiBiaya = {{ $booking->estimasi_biaya ?? 0 }};
        const inputDeposit = document.getElementById('jumlah_deposit');
        const inputJumlahBayar = document.getElementById('jumlah_bayar');

        if (inputDeposit && inputJumlahBayar) {
            function hitungTotalPembayaran() {
                let nilaiDeposit = inputDeposit.value.trim();
                let nominalDeposit = parseFloat(nilaiDeposit);
                if (isNaN(nominalDeposit) || nominalDeposit < 0) {
                    nominalDeposit = 0;
                }
                inputJumlahBayar.value = estimasiBiaya + nominalDeposit;
            }
            hitungTotalPembayaran();
            inputDeposit.addEventListener('input', hitungTotalPembayaran);
        }
    });
</script>
@endsection