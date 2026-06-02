@extends('layouts.app')

@section('title', 'Booking Saya')
@section('page-title', 'Booking Saya')

@section('topbar-actions')
<a href="{{ route('pelanggan.katalog') }}" class="btn-primary flex items-center gap-2">
    <i data-lucide="plus" class="w-4 h-4"></i> Booking Baru
</a>
@endsection

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')
<div class="card p-4 mb-5">
    <form method="GET" class="flex gap-3 items-end">
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input w-44">
                <option value="">Semua Status</option>
                @foreach(['pending'=>'Menunggu','disetujui'=>'Disetujui','berlangsung'=>'Berlangsung','selesai'=>'Selesai','ditolak'=>'Ditolak','dibatalkan'=>'Dibatalkan'] as $val=>$lbl)
                <option value="{{ $val }}" {{ request('status')===$val?'selected':'' }}>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request('status'))<a href="{{ route('pelanggan.booking.index') }}" class="btn-secondary">Reset</a>@endif
    </form>
</div>

<div class="space-y-4">
    @forelse($booking as $b)
    @php $sc = ['pending'=>['badge-yellow','clock','Menunggu Persetujuan'],'disetujui'=>['badge-blue','check-circle','Disetujui - Silakan ambil kendaraan'],'berlangsung'=>['badge-green','activity','Sedang Berlangsung'],'selesai'=>['badge-gray','check-circle-2','Selesai'],'ditolak'=>['badge-red','x-circle','Ditolak'],'dibatalkan'=>['badge-red','x-circle','Dibatalkan']][$b->status] ?? ['badge-gray','circle',ucfirst($b->status)]; @endphp
    <div class="card p-5">
        <div class="flex items-start justify-between mb-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-mono text-xs font-bold text-slate-500">{{ $b->kode_booking }}</span>
                    <span class="badge {{ $sc[0] }}"><i data-lucide="{{ $sc[1] }}" class="w-3 h-3 mr-1"></i>{{ $sc[2] }}</span>
                </div>
                <h3 class="font-bold text-slate-800 text-lg">{{ $b->kendaraan->nama }}</h3>
                <p class="text-sm text-slate-400">{{ $b->kendaraan->merk }} {{ $b->kendaraan->model }} · {{ $b->kendaraan->plat_nomor }}</p>
            </div>
            <p class="font-extrabold text-primary-700 text-lg shrink-0">Rp {{ number_format($b->estimasi_biaya, 0, ',', '.') }}</p>
        </div>
        <div class="flex flex-wrap gap-4 text-sm text-slate-500 mb-4">
            <span class="flex items-center gap-1.5"><i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>{{ $b->tanggal_mulai?->format('d M Y') }}</span>
            <span class="text-slate-300">→</span>
            <span class="flex items-center gap-1.5"><i data-lucide="calendar-check" class="w-4 h-4 text-slate-400"></i>{{ $b->tanggal_selesai?->format('d M Y') }}</span>
            <span class="flex items-center gap-1.5"><i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>{{ $b->durasi_hari }} hari</span>
        </div>
        @if($b->alasan_penolakan)
        <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-2.5 mb-3 text-sm text-red-700">
            <strong>Alasan Penolakan:</strong> {{ $b->alasan_penolakan }}
        </div>
        @endif
        @if($b->catatan)
        <p class="text-xs text-slate-400 mb-3">Catatan: {{ $b->catatan }}</p>
        @endif
        <div class="flex gap-2 justify-end">
            <a href="{{ route('pelanggan.booking.show', $b) }}" class="btn-secondary text-xs px-3 py-1.5">Detail</a>
            @if($b->status === 'pending')
            <form method="POST" action="{{ route('pelanggan.booking.cancel', $b) }}" onsubmit="return confirm('Batalkan booking ini?')">
                @csrf @method('PATCH')
                <button class="btn-danger text-xs px-3 py-1.5">Batalkan</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="card py-16 text-center">
        <i data-lucide="calendar-x" class="w-12 h-12 text-slate-200 mx-auto mb-3"></i>
        <p class="text-slate-400 font-medium">Belum ada booking</p>
        <p class="text-slate-300 text-sm mt-1">Yuk, temukan kendaraan yang sesuai untukmu</p>
        <a href="{{ route('pelanggan.katalog') }}" class="btn-primary inline-block mt-4">Lihat Katalog</a>
    </div>
    @endforelse
</div>

@if($booking->hasPages())
<div class="mt-5">{{ $booking->withQueryString()->links() }}</div>
@endif
@endsection
