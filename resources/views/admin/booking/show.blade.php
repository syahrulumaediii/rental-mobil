@extends('layouts.app')

@section('title', 'Detail Booking - ' . $booking->kode_booking)
@section('page-title', 'Detail Booking')
@section('breadcrumb', 'Admin / Booking / Detail')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
{{-- Tombol Kembali --}}
<div class="mb-5">
    <a href="{{ route('admin.booking.index') }}" class="btn-secondary inline-flex items-center gap-2 text-xs sm:text-sm px-4 py-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
</div>

{{-- Layout Grid Utama --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Kolom Kiri (Detail Utama Sewa & Kendaraan) --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Card Informasi Rental --}}
        <div class="card p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4 mb-4">
                <div>
                    <p class="text-[10px] text-slate-400 font-mono uppercase tracking-wider">Kode Booking</p>
                    <h2 class="text-lg sm:text-xl font-mono font-bold text-slate-800">{{ $booking->kode_booking }}</h2>
                </div>
                <div class="sm:text-right">
                    @php 
                        $statusClasses = [
                            'pending' => 'badge-yellow',
                            'disetujui' => 'badge-blue',
                            'ditolak' => 'badge-red',
                            'aktif' => 'badge-green',
                            'selesai' => 'badge-gray',
                            'dibatalkan' => 'badge-red'
                        ]; 
                    @endphp
                    <span class="badge {{ $statusClasses[$booking->status] ?? 'badge-gray' }} text-xs px-3 py-1 inline-block">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>

            {{-- Grid Info Waktu Sewa --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-400">Tanggal Mulai Sewa</p>
                    <p class="font-semibold text-slate-700 text-sm flex items-center gap-2 mt-1">
                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400 shrink-0"></i>
                        {{ $booking->tanggal_mulai->format('d M Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Tanggal Selesai Sewa</p>
                    <p class="font-semibold text-slate-700 text-sm flex items-center gap-2 mt-1">
                        <i data-lucide="calendar-check" class="w-4 h-4 text-slate-400 shrink-0"></i>
                        {{ $booking->tanggal_selesai->format('d M Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Durasi Sewa</p>
                    <p class="font-bold text-slate-700 text-sm mt-1 bg-slate-50 px-2.5 py-1 rounded-md inline-block">{{ $booking->durasi_hari }} Hari</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Tanggal Pengajuan</p>
                    <p class="font-medium text-slate-600 text-xs sm:text-sm mt-1">{{ $booking->created_at->format('d M Y · H:i') }}</p>
                </div>
            </div>

            @if($booking->catatan)
            <div class="mt-5 p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-wide mb-1">Catatan Pelanggan:</p>
                <p class="text-xs sm:text-sm text-slate-600 italic">"{{ $booking->catatan }}"</p>
            </div>
            @endif
        </div>

        {{-- Card Informasi Detail Mobil/Kendaraan --}}
        <div class="card p-4 sm:p-6">
            <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i data-lucide="car" class="w-4 h-4 text-blue-500"></i> Detail Kendaraan
            </h3>
            <div class="flex flex-col sm:flex-row gap-4 items-start">
                @if($booking->kendaraan->foto)
                    <img src="{{ asset('storage/' . $booking->kendaraan->foto) }}" class="w-full sm:w-32 h-40 sm:h-20 object-cover rounded-xl bg-slate-100 shrink-0">
                @else
                    <div class="w-full sm:w-32 h-20 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 shrink-0">
                        <i data-lucide="car" class="w-6 h-6"></i>
                    </div>
                @endif
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3 w-full">
                    <div>
                        <p class="text-xs text-slate-400">Nama / Merk</p>
                        <p class="font-bold text-slate-700 text-sm sm:text-base">{{ $booking->kendaraan->nama }} <span class="text-xs font-normal text-slate-400">({{ $booking->kendaraan->merk }})</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Plat Nomor</p>
                        <p class="font-mono text-xs font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded inline-block mt-0.5 tracking-wider">{{ $booking->kendaraan->plat_nomor }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-xs text-slate-400">Tarif Harian</p>
                        <p class="text-sm font-bold text-slate-700 mt-0.5">Rp {{ number_format($booking->kendaraan->tarif_harian, 0, ',', '.') }} <span class="text-xs text-slate-400 font-normal">/ hari</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan (Informasi Pelanggan, Invoice, & Form Konfirmasi) --}}
    <div class="space-y-6">
        
        {{-- Card Informasi User Pelanggan --}}
        <div class="card p-4 sm:p-6">
            <h3 class="text-xs sm:text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i data-lucide="user" class="w-4 h-4 text-blue-500"></i> Informasi Pelanggan
            </h3>
            <div class="space-y-3.5">
                <div>
                    <p class="text-xs text-slate-400">Nama Pelanggan</p>
                    <p class="font-bold text-slate-700 text-sm sm:text-base mt-0.5">{{ $booking->pelanggan->user->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">No. Telepon / WhatsApp</p>
                    <p class="text-xs sm:text-sm text-slate-600 font-mono mt-0.5 flex items-center gap-1.5">
                        <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i> {{ $booking->pelanggan->user->phone ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Card Total Invoice Bayar --}}
        <div class="card p-4 sm:p-5 bg-slate-50 border border-slate-100">
            <div class="flex justify-between items-center text-xs sm:text-sm">
                <span class="text-slate-600 font-medium">Total Sewa Pokok:</span>
                <span class="font-extrabold text-slate-800 text-base sm:text-lg">Rp {{ number_format($booking->estimasi_biaya, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- AREA VALIDASI PERSETUJUAN ADMIN (Hanya muncul jika status pending) --}}
        @if($booking->status === 'pending')
        <div class="card p-4 sm:p-5 space-y-4 border border-blue-100 shadow-xs" x-data="{ adaDeposit: false, nominal: 0 }">
            <div>
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-4 h-4 text-blue-500"></i> Konfirmasi & Validasi
                </h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Tentukan metode pembayaran dan deposit jaminan sewa.</p>
            </div>

            <form method="POST" action="{{ route('admin.booking.disetujui', $booking) }}" class="space-y-4">
                @csrf 
                @method('PATCH')
                
                {{-- Form Select Metode Pembayaran --}}
                <div>
                    <label class="form-label text-xs font-semibold mb-1 block text-slate-600">Metode Pembayaran Resmi</label>
                    <select name="metode_pembayaran_id" class="form-input text-xs sm:text-sm w-full" required>
                        <option value="1">Cash (Tunai)</option>
                        <option value="2">Transfer Bank BCA</option>
                        <option value="3">DANA</option>
                        <option value="4">QRIS</option>
                    </select>
                </div>

                {{-- Opsi Input Nominal Uang Deposit --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="form-label text-xs font-semibold text-slate-600 block">Opsi Uang Deposit</label>
                        <button type="button" @click="adaDeposit = !adaDeposit; if(!adaDeposit) nominal = 0;" 
                                class="text-[10px] sm:text-xs font-bold px-2 py-1 rounded transition-colors flex items-center gap-1"
                                :class="adaDeposit ? 'bg-red-50 text-red-500 hover:bg-red-100' : 'bg-green-50 text-green-600 hover:bg-green-100'">
                            <span x-text="adaDeposit ? '✕ Batalkan Deposit' : '＋ Tambah Deposit'"></span>
                        </button>
                    </div>

                    <div x-show="adaDeposit" x-transition class="relative rounded-md shadow-xs">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-slate-400 text-xs font-semibold">Rp</span>
                        </div>
                        <input type="number" name="nominal_deposit" x-model.number="nominal"
                               class="form-input pl-8 text-xs sm:text-sm font-bold text-slate-700 w-full" 
                               min="0" placeholder="Tentukan nominal jaminan...">
                    </div>
                    <input type="hidden" name="pilih_deposit" :value="adaDeposit ? 1 : 0">
                </div>

                {{-- Action Action Buttons (Persetujuan / Penolakan) --}}
                <div class="flex flex-col sm:flex-row gap-2 pt-2">
                    <button type="submit" class="btn-primary flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-bold order-2 sm:order-1">
                        <i data-lucide="check" class="w-4 h-4"></i> Setujui Booking
                    </button>
                    <button type="button" onclick="openRejectModal({{ $booking->id }})" class="btn-danger sm:flex-initial flex items-center justify-center gap-1.5 py-2.5 text-xs font-bold order-1 sm:order-2 px-4">
                        <i data-lucide="x" class="w-4 h-4"></i> Tolak
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>

{{-- Reject Modal (Centered & Backdrop blur) --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-50 items-center justify-center p-4 transition-all">
    <div class="bg-white rounded-2xl w-full max-w-md p-5 sm:p-6 shadow-xl border border-slate-100">
        <h3 class="font-bold text-slate-800 text-base sm:text-lg mb-1">Tolak Booking</h3>
        <p class="text-xs sm:text-sm text-slate-500 mb-4">Berikan alasan penolakan booking ini secara jelas kepada pelanggan.</p>
        <form id="rejectForm" method="POST">
            @csrf @method('PATCH')
            <div class="mb-4">
                <label class="form-label block text-xs font-semibold text-slate-500 mb-1">Alasan Penolakan</label>
                <textarea name="alasan_penolakan" rows="3" class="form-input w-full text-xs sm:text-sm" required placeholder="Tulis alasan penolakan..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-danger flex-1 justify-center py-2 text-xs sm:text-sm font-semibold">Tolak Booking</button>
                <button type="button" onclick="closeRejectModal()" class="btn-secondary flex-1 justify-center py-2 text-xs sm:text-sm font-semibold">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openRejectModal(id) {
    document.getElementById('rejectForm').action = `/admin/booking/${id}/reject`;
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endpush