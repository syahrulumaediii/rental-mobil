@extends('layouts.app')

@section('title', 'Detail Booking #' . $booking->kode_booking)
@section('page-title', 'Detail Booking')

@section('breadcrumb')
<a href="{{ route('pelanggan.booking.index') }}" class="hover:text-primary-600 transition-colors">Booking Saya</a> 
<span class="mx-1">/</span> 
<span class="text-slate-600">#{{ $booking->kode_booking }}</span>
@endsection

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')
@php 
    // Sinkronisasi status terikat dengan tampilan warna UI
    $statusConfig = [
        'pending' => ['badge-yellow', 'clock', 'Menunggu Persetujuan Admin'],
        'disetujui' => ['badge-blue', 'check-circle', 'Disetujui - Silakan Ambil Kendaraan'],
        'ditolak' => ['badge-red', 'x-circle', 'Booking Ditolak'],
        'aktif' => ['badge-green', 'activity', 'Kendaraan Sedang Digunakan (Aktif)'],
        'selesai' => ['badge-gray', 'check-circle-2', 'Selesai'],
        'dibatalkan' => ['badge-red', 'x-circle', 'Booking Dibatalkan']
    ][$booking->status] ?? ['badge-gray', 'circle', ucfirst($booking->status)];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- KOLOM UTAMA (KIRI): INFORMASI UTAMA & RINCIAN --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Card Kendaraan & Status --}}
        <div class="card p-6">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4 mb-4">
                <div>
                    <span class="font-mono text-xs font-bold text-slate-400 block mb-1">KODE BOOKING</span>
                    <h2 class="text-xl font-bold text-slate-800">{{ $booking->kode_booking }}</h2>
                </div>
                <span class="badge {{ $statusConfig[0] }} text-sm py-1.5 px-3">
                    <i data-lucide="{{ $statusConfig[1] }}" class="w-4 h-4 mr-1.5"></i>{{ $statusConfig[2] }}
                </span>
            </div>

            <div class="flex flex-col md:flex-row gap-5 items-start">
                @if($booking->kendaraan->foto)
                    <img src="{{ asset('storage/' . $booking->kendaraan->foto) }}" alt="Foto {{ $booking->kendaraan->nama }}" class="w-full md:w-44 h-28 object-cover rounded-xl border border-slate-100">
                @else
                    <div class="w-full md:w-44 h-28 bg-slate-50 border border-slate-100 rounded-xl flex flex-col items-center justify-center text-slate-300">
                        <i data-lucide="car" class="w-8 h-8 mb-1"></i>
                        <span class="text-[10px] font-medium">Tidak ada foto</span>
                    </div>
                @endif
                
                <div class="flex-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-primary-600 bg-primary-50 px-2.5 py-0.5 rounded-md">
                        {{ $booking->kendaraan->merk }}
                    </span>
                    <h3 class="text-lg font-bold text-slate-800 mt-1.5">{{ $booking->kendaraan->nama }}</h3>
                    <p class="text-sm font-mono text-slate-500 mt-0.5">{{ $booking->kendaraan->plat_nomor }} · Warna {{ $booking->kendaraan->warna ?? '-' }}</p>
                    
                    <div class="grid grid-cols-3 gap-2 mt-4 text-xs text-slate-500 max-w-sm">
                        <div class="bg-slate-50 p-2 rounded-lg text-center">
                            <span class="block text-slate-400 mb-0.5">Kapasitas</span>
                            <strong class="text-slate-700 text-sm">{{ $booking->kendaraan->kapasitas }} Kursi</strong>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-lg text-center">
                            <span class="block text-slate-400 mb-0.5">Transmisi</span>
                            <strong class="text-slate-700 text-sm capitalize">{{ $booking->kendaraan->transmisi }}</strong>
                        </div>
                        <div class="bg-slate-50 p-2 rounded-lg text-center">
                            <span class="block text-slate-400 mb-0.5">Bahan Bakar</span>
                            <strong class="text-slate-700 text-sm capitalize">{{ $booking->kendaraan->bahan_bakar }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Waktu Sewa --}}
        <div class="card p-6">
            <h4 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i> Durasi & Jadwal Sewa
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center bg-slate-50 p-4 rounded-xl border border-slate-100">
                <div>
                    <span class="text-xs text-slate-400 block mb-0.5">Tanggal Mulai (Ambil)</span>
                    <span class="font-bold text-slate-700 block">{{ $booking->tanggal_mulai?->format('d M Y') }}</span>
                    <span class="text-xs text-slate-500 font-mono">{{ $booking->tanggal_mulai?->format('H:i') }} WIB</span>
                </div>
                <div class="flex md:justify-center shrink-0">
                    <span class="badge bg-primary-100 text-primary-700 border border-primary-200 py-1 px-3 font-semibold rounded-lg flex items-center gap-1">
                        <i data-lucide="move-right" class="w-3.5 h-3.5 hidden md:block"></i>
                        {{ $booking->durasi_hari }} Hari Sewa
                    </span>
                </div>
                <div class="md:text-right">
                    <span class="text-xs text-slate-400 block mb-0.5">Tanggal Selesai (Kembali)</span>
                    <span class="font-bold text-slate-700 block">{{ $booking->tanggal_selesai?->format('d M Y') }}</span>
                    <span class="text-xs text-slate-500 font-mono">{{ $booking->tanggal_selesai?->format('H:i') }} WIB</span>
                </div>
            </div>

            {{-- Jika kendaraan sudah berjalan / selesai, tampilkan log aktual pengembalian dari kasir --}}
            @if($booking->transaksiSewa)
            <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-400 block">Aktual Diambil</span>
                    <span class="font-medium text-slate-700 font-mono">{{ \Carbon\Carbon::parse($booking->transaksiSewa->tanggal_ambil_aktual)->format('d M Y · H:i') }} WIB</span>
                </div>
                <div>
                    <span class="text-xs text-slate-400 block">Aktual Dikembalikan</span>
                    @if($booking->transaksiSewa->tanggal_kembali_aktual)
                        <span class="font-medium text-slate-700 font-mono">{{ \Carbon\Carbon::parse($booking->transaksiSewa->tanggal_kembali_aktual)->format('d M Y · H:i') }} WIB</span>
                    @else
                        <span class="text-xs text-amber-600 font-medium italic flex items-center gap-1 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span> Belum dikembalikan
                        </span>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Blok Alasan Penolakan / Catatan Khusus --}}
        @if($booking->alasan_penolakan)
        <div class="bg-red-50 border border-red-100 rounded-xl p-5 flex gap-3 items-start">
            <i data-lucide="alert-octagon" class="w-5 h-5 text-red-600 shrink-0 mt-0.5"></i>
            <div>
                <h5 class="font-bold text-red-800 text-sm mb-1">Booking Ditolak Admin</h5>
                <p class="text-sm text-red-700 leading-relaxed">{{ $booking->alasan_penolakan }}</p>
            </div>
        </div>
        @endif

        @if($booking->catatan)
        <div class="card p-5 bg-slate-50/50 border-dashed">
            <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Catatan Permintaan Anda:</h5>
            <p class="text-sm text-slate-600 italic">"{{ $booking->catatan }}"</p>
        </div>
        @endif

        {{-- JIKA ADA DENDA (Kerusakan/Keterlambatan) --}}
        @if($booking->transaksiSewa && $booking->transaksiSewa->denda->count() > 0)
        <div class="card p-6 border-red-100 bg-red-50/20">
            <h4 class="font-bold text-red-800 mb-3 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i> Log Pelanggaran & Denda
            </h4>
            <div class="divide-y divide-red-100/50">
                @foreach($booking->transaksiSewa->denda as $d)
                <div class="py-2.5 flex items-start justify-between gap-4 text-sm">
                    <div>
                        <span class="badge badge-red capitalize text-[10px] py-0 px-2 font-bold mb-1">{{ $d->jenis_denda }}</span>
                        <p class="text-slate-700 font-medium">{{ $d->keterangan }}</p>
                        @if($d->jumlah_jam_telat > 0)
                            <span class="text-xs text-slate-400 block mt-0.5">Durasi Telat: {{ $d->jumlah_jam_telat }} Jam (Tarif: Rp {{ number_format($d->tarif_denda, 0, ',', '.') }}/jam)</span>
                        @endif
                    </div>
                    <span class="font-bold text-red-600 shrink-0">Rp {{ number_format($d->total_denda, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- KOLOM SAMPING (KANAN): KEUANGAN, DEPOSIT & AKSI --}}
    <div class="space-y-6">
        
        {{-- Rincian Biaya Finansial --}}
        <div class="card p-6 bg-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-primary-50/50 rounded-full translate-x-8 -translate-y-8 -z-0"></div>
            
            <h4 class="font-bold text-slate-800 mb-4 relative z-10">Rincian Invoice</h4>
            
            <div class="space-y-3 text-sm pb-4 border-b border-slate-100">
                <div class="flex justify-between text-slate-500">
                    <span>Tarif Sewa Mobil</span>
                    <span>Rp {{ number_format($booking->kendaraan->tarif_harian, 0, ',', '.') }} / hari</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>Total Biaya Sewa ({{ $booking->durasi_hari }} Hari)</span>
                    <span class="font-semibold text-slate-700">Rp {{ number_format($booking->estimasi_biaya, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-500 border-t border-dashed border-slate-100 pt-2">
                    <span class="font-medium text-amber-700">DP (Down Payment 30%)</span>
                    <span class="font-bold text-amber-700">Rp {{ number_format($booking->estimasi_biaya * 0.3, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>Sisa Tagihan (70%)</span>
                    <span class="font-medium text-slate-700">Rp {{ number_format($booking->estimasi_biaya * 0.7, 0, ',', '.') }}</span>
                </div>
                
                {{-- Data akumulasi jika transaksi sewa sudah terbuat --}}
                @if($booking->transaksiSewa)
                    <div class="flex justify-between text-red-600">
                        <span>Total Akumulasi Denda</span>
                        <span>+ Rp {{ number_format($booking->transaksiSewa->total_denda, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            {{-- Bagian Jaminan Deposit --}}
            @if($booking->transaksiSewa && $booking->transaksiSewa->deposit)
            <div class="bg-slate-50 p-3 rounded-xl my-4 text-xs space-y-1.5 border border-slate-100">
                <div class="flex justify-between text-slate-500">
                    <span class="flex items-center gap-1"><i data-lucide="shield-check" class="w-3.5 h-3.5 text-slate-400"></i>Uang Deposit Jaminan</span>
                    <span class="font-semibold text-slate-700">Rp {{ number_format($booking->transaksiSewa->deposit->jumlah, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Status Klaim</span>
                    <span class="font-bold uppercase tracking-wider text-[10px] {{ $booking->transaksiSewa->deposit->status === 'dipotong' ? 'text-red-500' : 'text-green-600' }}">
                        {{ $booking->transaksiSewa->deposit->status }}
                    </span>
                </div>
                @if($booking->transaksiSewa->deposit->jumlah_dipotong > 0)
                <div class="text-[11px] text-red-600 bg-red-50 border border-red-100 p-1.5 rounded mt-1">
                    <strong>Potongan:</strong> {{ $booking->transaksiSewa->deposit->alasan_potongan }}
                </div>
                @endif
            </div>
            @endif

            <div class="pt-4 flex items-baseline justify-between mb-4">
                <span class="text-sm font-bold text-slate-800">Grand Total Tagihan</span>
                <span class="text-xl font-black text-primary-600">
                    Rp {{ number_format(($booking->transaksiSewa ? ($booking->transaksiSewa->total_biaya + $booking->transaksiSewa->total_denda) : $booking->estimasi_biaya), 0, ',', '.') }}
                </span>
            </div>

            {{-- Riwayat Record Pembayaran Berdasarkan Tabel 'pembayaran' --}}
            @if($booking->transaksiSewa && $booking->transaksiSewa->pembayaran->count() > 0)
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Riwayat Pembayaran Anda:</h5>
                    <div class="space-y-2">
                        @foreach($booking->transaksiSewa->pembayaran as $p)
                        <div class="flex items-center justify-between text-xs bg-slate-50 p-2 rounded-lg border border-slate-100/60">
                            <div>
                                <span class="font-semibold text-slate-700">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-slate-400 block font-mono">{{ $p->created_at->format('d/m/y H:i') }} · {{ $p->metodePembayaran->nama ?? 'Tunai' }}</span>
                            </div>
                            <span class="badge {{ $p->status === 'lunas' ? 'badge-green' : ($p->status === 'pending' ? 'badge-yellow' : 'badge-red') }} text-[10px] py-0.5 px-2">
                                {{ $p->status }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Panel Tombol Aksi Pembatalan Khusus --}}
        <div class="space-y-2">
            @if($booking->status === 'pending')
                <form method="POST" action="{{ route('pelanggan.booking.cancel', $booking) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan booking ini?')">
                    @csrf 
                    @method('PATCH')
                    <button type="submit" class="w-full btn-danger py-3 flex items-center justify-center gap-2 rounded-xl text-sm shadow-sm cursor-pointer">
                        <i data-lucide="x-circle" class="w-4 h-4"></i> Batalkan Pengajuan Booking
                    </button>
                </form>
            @endif

            <a href="{{ route('pelanggan.booking.index') }}" class="w-full btn-secondary py-2.5 text-center block rounded-xl text-sm font-medium">
                Kembali ke Daftar
            </a>
        </div>

    </div>
</div>
@endsection