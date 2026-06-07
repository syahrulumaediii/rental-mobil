@extends('layouts.app')

@section('title', 'Buat Booking')
@section('page-title', 'Buat Booking')
@section('breadcrumb', 'Katalog / Buat Booking')
@vite('resources/js/pelanggan/booking/create.js')

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')
<div class="max-w-2xl mx-auto" x-data="bookingForm()">
    {{-- Info Kendaraan --}}
    <div class="card p-5 mb-5">
        <div class="flex gap-4">
            @if($kendaraan->foto)
            <img src="{{ Storage::url($kendaraan->foto) }}" class="w-24 h-24 rounded-xl object-cover shrink-0">
            @else
            <div class="w-24 h-24 bg-slate-100 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="car" class="w-10 h-10 text-slate-300"></i>
            </div>
            @endif
            <div class="flex-1">
                <h2 class="font-extrabold text-slate-800 text-lg">{{ $kendaraan->nama }}</h2>
                <p class="text-slate-400 text-sm">{{ $kendaraan->merk }} {{ $kendaraan->model }} · {{ $kendaraan->tahun }}</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="badge badge-blue">{{ $kendaraan->kategori->nama ?? '-' }}</span>
                    <span class="badge badge-gray capitalize">{{ $kendaraan->transmisi }}</span>
                    <span class="badge badge-gray capitalize">{{ $kendaraan->bahan_bakar }}</span>
                </div>
                <p class="font-extrabold text-primary-700 text-lg mt-2">Rp {{ number_format($kendaraan->tarif_harian, 0, ',', '.') }} <span class="text-sm font-normal text-slate-400">/ hari</span></p>
            </div>
        </div>
    </div>

    {{-- Form Booking --}}
    <div class="card p-5">
        <h3 class="font-bold text-slate-700 mb-4">Detail Pemesanan</h3>
        <form method="POST" action="{{ route('pelanggan.booking.store') }}">
            @csrf
            <input type="hidden" name="kendaraan_id" value="{{ $kendaraan->id }}">

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" x-model="mulai"
                           value="{{ old('tanggal_mulai') }}" class="form-input" required
                           min="{{ date('Y-m-d') }}" @change="hitungEstimasi()">
                    @error('tanggal_mulai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" x-model="selesai"
                           value="{{ old('tanggal_selesai') }}" class="form-input" required
                           :min="mulai || '{{ date('Y-m-d', strtotime('+1 day')) }}'"
                           @change="hitungEstimasi()">
                    @error('tanggal_selesai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Estimasi --}}
            <div x-show="durasi > 0" class="bg-primary-50 border border-primary-100 rounded-xl p-4 mb-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-primary-700">Durasi: <strong x-text="durasi + ' hari'"></strong></span>
                    <span class="font-extrabold text-primary-700 text-base">
                        Estimasi: Rp <span x-text="estimasi.toLocaleString('id-ID')"></span>
                    </span>
                </div>
            </div>

            <div class="mb-5">
                <label class="form-label">Catatan (Opsional)</label>
                <textarea name="catatan" rows="2" class="form-input" placeholder="Permintaan khusus, misal: butuh kursi bayi, perjalanan jauh, dll...">{{ old('catatan') }}</textarea>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-5 text-xs text-amber-700">
                <strong>Ketentuan:</strong> Booking memerlukan persetujuan admin. Pastikan dokumen KTP & SIM Anda sudah terverifikasi. Pembayaran dilakukan saat pengambilan kendaraan.
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1">
                    <i data-lucide="calendar-plus" class="w-4 h-4 inline mr-1"></i> Ajukan Booking
                </button>
                <a href="{{ route('pelanggan.katalog') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')

@endpush
