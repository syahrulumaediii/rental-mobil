@extends('layouts.app')

@section('title', 'Detail Booking - ' . $booking->kode_booking)
@section('page-title', 'Detail Booking')
@section('breadcrumb', 'Kasir / Booking / Detail')

@section('sidebar-nav')
    @include('components.sidebar-kasir')
@endsection

@section('content')
<div class="mb-4">
    <a href="{{ route('kasir.booking.index') }}" class="btn-secondary inline-flex items-center gap-2">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="card p-6">
            <div class="flex justify-between items-start border-b border-slate-100 pb-4 mb-4">
                <div>
                    <p class="text-xs text-slate-400 font-mono uppercase tracking-wider">Kode Booking</p>
                    <h2 class="text-xl font-mono font-bold text-slate-800">{{ $booking->kode_booking }}</h2>
                </div>
                <div>
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
                    <span class="badge {{ $statusClasses[$booking->status] ?? 'badge-gray' }} text-sm px-3 py-1">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-slate-400">Tanggal Mulai Sewa</p>
                    <p class="font-medium text-slate-700 flex items-center gap-2 mt-0.5">
                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                        {{ $booking->tanggal_mulai->format('d M Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Tanggal Selesai Sewa</p>
                    <p class="font-medium text-slate-700 flex items-center gap-2 mt-0.5">
                        <i data-lucide="calendar-check" class="w-4 h-4 text-slate-400"></i>
                        {{ $booking->tanggal_selesai->format('d M Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Durasi Sewa</p>
                    <p class="font-medium text-slate-700 mt-0.5">{{ $booking->durasi_hari }} Hari</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Tanggal Pengajuan</p>
                    <p class="font-medium text-slate-700 mt-0.5">{{ $booking->created_at->format('d M Y · H:i') }}</p>
                </div>
            </div>

            @if($booking->catatan)
            <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100">
                <p class="text-xs text-slate-400 font-medium mb-1">Catatan Pelanggan:</p>
                <p class="text-sm text-slate-600 italic">"{{ $booking->catatan }}"</p>
            </div>
            @endif
        </div>

        <div class="card p-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i data-lucide="car" class="w-4 h-4 text-primary-500"></i> Detail Kendaraan
            </h3>
            <div class="flex flex-col sm:flex-row gap-4 items-start">
                @if($booking->kendaraan->foto)
                    <img src="{{ asset('storage/' . $booking->kendaraan->foto) }}" class="w-32 h-20 object-cover rounded-xl bg-slate-100">
                @else
                    <div class="w-32 h-20 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                        <i data-lucide="car" class="w-6 h-6"></i>
                    </div>
                @endif
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <p class="text-xs text-slate-400">Nama / Merk</p>
                        <p class="font-semibold text-slate-700">{{ $booking->kendaraan->nama }} ({{ $booking->kendaraan->merk }})</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Plat Nomor</p>
                        <p class="font-mono text-sm font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded inline-block mt-0.5">{{ $booking->kendaraan->plat_nomor }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400">Tarif Harian</p>
                        <p class="text-sm font-medium text-slate-700 mt-0.5">Rp {{ number_format($booking->kendaraan->tarif_harian, 0, ',', '.') }} / hari</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="card p-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i data-lucide="user" class="w-4 h-4 text-primary-500"></i> Informasi Pelanggan
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-slate-400">Nama Pelanggan</p>
                    <p class="font-semibold text-slate-700">{{ $booking->pelanggan->user->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">No. Telepon</p>
                    <p class="text-sm text-slate-600 font-mono">{{ $booking->pelanggan->user->phone ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="card p-6 bg-slate-50 border border-slate-100">
            <div class="flex justify-between text-sm">
                <span class="text-slate-600">Total Sewa Pokok:</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($booking->estimasi_biaya, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- AREA KONFIRMASI KASIR --}}
        @if($booking->status === 'pending')
        <div class="card p-5 space-y-4 border border-blue-100 shadow-sm">

            <form method="POST" action="{{ route('kasir.booking.disetujui', $booking) }}">
                @csrf
                @method('PATCH')

                <div class="flex gap-2 pt-2">
                    <button type="submit"
                        class="btn-primary flex-1 flex items-center justify-center gap-1.5 py-2 text-xs">
                        <i data-lucide="check" class="w-4 h-4"></i> Setujui Booking
                    </button>

                    <button type="button"
                        onclick="openRejectModal({{ $booking->id }})"
                        class="btn-danger flex-1 flex items-center justify-center gap-1.5 py-2 text-xs">
                        <i data-lucide="x" class="w-4 h-4"></i> Tolak
                    </button>
                </div>

            </form>

        </div>

        @endif
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-xl">
        <h3 class="font-bold text-slate-800 mb-1">Tolak Booking</h3>
        <p class="text-sm text-slate-500 mb-4">Berikan alasan penolakan booking ini.</p>
        <form id="rejectForm" method="POST">
            @csrf @method('PATCH')
            <div class="mb-4">
                <label class="form-label">Alasan Penolakan</label>
                <textarea name="alasan_penolakan" rows="3" class="form-input" required placeholder="Tulis alasan penolakan..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-danger flex-1">Tolak Booking</button>
                <button type="button" onclick="closeRejectModal()" class="btn-secondary flex-1">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openRejectModal(id) {
    document.getElementById('rejectForm').action = `/kasir/booking/${id}/ditolak`;
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}
</script>
@endpush