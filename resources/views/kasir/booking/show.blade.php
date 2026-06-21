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
            <div class="flex flex-wrap justify-between items-start border-b border-slate-100 pb-4 mb-4 gap-4">
                <div>
                    <p class="text-xs text-slate-400 font-mono uppercase tracking-wider">Kode Booking</p>
                    <h2 class="text-xl font-mono font-bold text-slate-800">{{ $booking->kode_booking }}</h2>
                </div>
                <div>
                    @php 
                        $statusClasses = [
                            'pending' => 'badge-yellow', 'disetujui' => 'badge-blue', 'ditolak' => 'badge-red',
                            'aktif' => 'badge-green', 'selesai' => 'badge-gray', 'dibatalkan' => 'badge-red'
                        ]; 
                    @endphp
                    <span class="badge {{ $statusClasses[$booking->status] ?? 'badge-gray' }} text-sm px-3 py-1">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs text-slate-400">Tanggal Mulai</p>
                    <p class="font-medium text-slate-700 flex items-center gap-2 mt-0.5">
                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                        {{ $booking->tanggal_mulai->format('d M Y · H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Tanggal Selesai</p>
                    <p class="font-medium text-slate-700 flex items-center gap-2 mt-0.5">
                        <i data-lucide="calendar-check" class="w-4 h-4 text-slate-400"></i>
                        {{ $booking->tanggal_selesai->format('d M Y · H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Durasi</p>
                    <p class="font-medium text-slate-700 mt-0.5">{{ $booking->durasi_hari }} Hari</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400">Pengajuan</p>
                    <p class="font-medium text-slate-700 mt-0.5">{{ $booking->created_at->format('d M Y · H:i') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-slate-100">
                <div>
                    <p class="text-xs text-slate-400 mb-2">Sumber Booking</p>
                    <span class="badge {{ $booking->sumber_booking === 'online' ? 'badge-blue' : 'badge-purple' }} text-[10px] uppercase">
                        {{ $booking->sumber_booking }}
                    </span>
                </div>
                @if($booking->dibuatOleh)
                <div>
                    <p class="text-xs text-slate-400">Dibuat Oleh</p>
                    <p class="text-sm font-medium text-slate-700">{{ $booking->dibuatOleh->name }}</p>
                </div>
                @endif
            </div>

            @if($booking->catatan)
            <div class="mt-6 p-4 bg-slate-50 rounded-xl border border-slate-100">
                <p class="text-xs text-slate-400 font-bold uppercase mb-1">Catatan Pelanggan:</p>
                <p class="text-sm text-slate-600 italic">"{{ $booking->catatan }}"</p>
            </div>
            @endif

            {{-- Menampilkan info alasan penolakan jika statusnya ditolak --}}
            @if($booking->status === 'ditolak' && $booking->alasan_penolakan)
            <div class="mt-6 p-4 bg-red-50 rounded-xl border border-red-100 text-red-800">
                <p class="text-xs font-bold uppercase mb-1 flex items-center gap-1.5 text-red-600">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i> Alasan Penolakan Unit:
                </p>
                <p class="text-sm italic">"{{ $booking->alasan_penolakan }}"</p>
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
                    <div>
                        <p class="text-xs text-amber-600 font-medium">Denda Terlambat</p>
                        <p class="text-sm font-bold text-amber-700 mt-0.5">
                            Rp {{ number_format($booking->kendaraan->denda_per_jam ?? 0, 0, ',', '.') }} / jam
                        </p>
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

        <div class="card p-6 bg-slate-50 border border-slate-100 space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-slate-600">Total Sewa Pokok:</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($booking->estimasi_biaya, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs text-amber-700 pt-1 border-t border-slate-200/50">
                <span>Down Payment (DP 30%):</span>
                <span class="font-bold">Rp {{ number_format($booking->estimasi_biaya * 0.3, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs text-slate-600">
                <span>Sisa Tagihan (70%):</span>
                <span class="font-semibold">Rp {{ number_format($booking->estimasi_biaya * 0.7, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- 🌟 ACTION HUBUNGI/KIRIM WHATSAPP (AKTIF JIKA STATUSNYA DISETUJUI ATAU DITOLAK) --}}
        @if($booking->status === 'disetujui' || $booking->status === 'ditolak')
            @php
                $phone = $booking->pelanggan->user->phone;
                // Standardisasi nomor ke kode negara Indonesia (62)
                if (str_starts_with($phone, '0')) { $phone = '62' . substr($phone, 1); }
                $phone = preg_replace('/[^0-9]/', '', $phone);

                if($booking->status === 'disetujui') {
                    $textMessage = "*Yth. Kak " . $booking->pelanggan->user->name . ",\n\n"
                                 . "✅ *BOOKING DISETUJUI* dengan Kode: *" . $booking->kode_booking . "*.\n\n"
                                 . "📍 *Detail Reservasi:*\n"
                                 . "• Unit: " . $booking->kendaraan->merk . " " . $booking->kendaraan->nama . " (" . $booking->kendaraan->plat_nomor . ")\n"
                                 . "• Jadwal: " . $booking->tanggal_mulai->format('d M Y H:i') . " s/d " . $booking->tanggal_selesai->format('d M Y H:i') . "\n"
                                 . "• Durasi: " . $booking->durasi_hari . " Hari\n"
                                 . "• Estimasi Biaya: *Rp " . number_format($booking->estimasi_biaya, 0, ',', '.') . "*\n"
                                 . "• Ketentuan Overtime: Rp " . number_format($booking->kendaraan->denda_per_jam, 0, ',', '.') . "/jam\n\n"
                                 . "Notifikasi resmi juga telah dikirim ke akun Anda. Silakan datang ke pool kami tepat waktu untuk pengambilan unit. Terima kasih! 🙏✨";
                } else {
                    $textMessage = "*Yth. Kak " . $booking->pelanggan->user->name . ",\n\n"
                                 . "❌ *PEMBERITAHUAN PENOLAKAN BOOKING*\n"
                                 . "Mohon maaf, pengajuan sewa Anda dengan Kode *" . $booking->kode_booking . "* saat ini *BELUM DAPAT KAMI SETUJUI*.\n\n"
                                 . "🚫 *Alasan Penolakan:* " . ($booking->alasan_penolakan ?? '-') . "\n\n"
                                 . "Pemberitahuan resmi telah dikirim ke sistem akun Anda. Apabila ada pertanyaan lebih lanjut, silakan hubungi tim CS kami. Terima kasih. 🙏";
                }
                $waUrl = "https://api.whatsapp.com/send?phone=" . $phone . "&text=" . urlencode($textMessage);
            @endphp

            <div class="card p-5 border {{ $booking->status === 'disetujui' ? 'border-emerald-100 bg-emerald-50/20' : 'border-rose-100 bg-rose-50/10' }}">
                <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <i data-lucide="message-square" class="w-4 h-4 text-emerald-600"></i> Integrasi Pesan Pelanggan
                </h4>
                <p class="text-xs text-slate-500 mb-3 leading-relaxed">Kirim ulang atau pastikan rincian konfirmasi terkirim melalui WhatsApp pelanggan.</p>
                <a href="{{ $waUrl }}" target="_blank" id="waAutoBtn" class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold text-xs text-white shadow-xs transition transform active:scale-95 {{ $booking->status === 'disetujui' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-600 hover:bg-amber-700' }}">
                    <i data-lucide="send" class="w-4 h-4"></i> Buka Chat WhatsApp
                </a>
            </div>
        @endif

        {{-- AREA KONFIRMASI KASIR (TAMPIL HANYA JIKA STATUS PENDING) --}}
        @if($booking->status === 'pending')
        <div class="card p-5 space-y-4 border border-blue-100 shadow-sm">
            <form method="POST" action="{{ route('kasir.booking.disetujui', $booking) }}">
                @csrf
                @method('PATCH')

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="btn-primary flex-1 flex items-center justify-center gap-1.5 py-2 text-xs">
                        <i data-lucide="check" class="w-4 h-4"></i> Setujui Booking
                    </button>

                    <button type="button" onclick="openRejectModal({{ $booking->id }})" class="btn-danger flex-1 flex items-center justify-center gap-1.5 py-2 text-xs">
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

// 🌟 LOGIKA OTOMATIS: Sistem langsung membuka WhatsApp sesaat setelah halaman memuat ulang (jika dipicu session controller)
@if(session('trigger_wa') && session('booking_id') == $booking->id)
    window.addEventListener('DOMContentLoaded', () => {
        const waLink = document.getElementById('waAutoBtn');
        if(waLink) {
            setTimeout(() => {
                waLink.click();
            }, 800); // Jeda 0.8 detik agar rendering halaman selesai dengan sempurna
        }
    });
@endif
</script>
@endpush