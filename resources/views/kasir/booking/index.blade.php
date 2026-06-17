@extends('layouts.app')

@section('title', 'Booking')
@section('page-title', 'Manajemen Booking')
@section('breadcrumb', 'Kasir / Booking')

@section('sidebar-nav')
    @include('components.sidebar-kasir')
@endsection

@section('content')

{{-- BANNER NOTIFIKASI --}}
@if(isset($pendingCount) && $pendingCount > 0)
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-5 flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center text-amber-600">
            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
        </div>
        <div>
            <h4 class="font-bold text-amber-900">Perhatian: Ada Booking Baru!</h4>
            <p class="text-sm text-amber-700">Terdapat <strong>{{ $pendingCount }} booking</strong> yang menunggu konfirmasi Anda.</p>
        </div>
    </div>
    {{-- <a href="{{ route('kasir.booking.index', ['status' => 'pending']) }}" 
       class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-lg transition-colors">
        Lihat Sekarang
    </a> --}}
</div>
@endif

<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-44">
            <label class="form-label">Cari Kode / Pelanggan</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="BKG-... atau nama pelanggan">
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input w-40">
                <option value="">Semua</option>
                {{-- DI SINI: Sudah diubah dari 'berlangsung' menjadi 'aktif' --}}
                @foreach(['pending','disetujui','ditolak','aktif','selesai','dibatalkan'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if(request()->anyFilled(['search','status']))
        <a href="{{ route('kasir.booking.index') }}" class="btn-secondary">Reset</a>
        @endif
    </form>
</div>

<div class="card overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="text-left text-xs uppercase text-slate-400">
                <th class="p-4">Kode Booking</th>
                <th class="p-4">Pelanggan</th>
                <th class="p-4">Kendaraan</th>
                <th class="p-4">Periode</th>
                <th class="p-4">Biaya</th>
                <th class="p-4">Sumber</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($booking as $b)
            <tr>
                <td class="p-4"><span class="font-mono text-xs font-bold text-slate-600">{{ $b->kode_booking }}</span></td>
                <td class="p-4">
                    <p class="font-semibold text-slate-700 text-sm">{{ $b->pelanggan->user->name }}</p>
                    <p class="text-xs text-slate-400">{{ $b->pelanggan->user->phone }}</p>
                </td>
                <td class="p-4">
                    <p class="font-medium text-slate-700 text-sm">{{ $b->kendaraan->nama }}</p>
                    <p class="text-xs text-slate-400 font-mono">{{ $b->kendaraan->plat_nomor }}</p>
                </td>
                <td class="p-4">
                    <p class="text-sm text-slate-600">{{ $b->tanggal_mulai->format('d M Y') }}</p>
                    <p class="text-xs text-slate-400">s/d {{ $b->tanggal_selesai->format('d M Y') }} · {{ $b->durasi_hari }} hari</p>
                </td>
                <td class="p-4 font-semibold text-slate-700 text-sm">Rp {{ number_format($b->estimasi_biaya, 0, ',', '.') }}</td>
                
                {{-- Kolom Sumber & Dibuat Oleh --}}
                <td class="p-4">
                    <span class="badge {{ $b->sumber_booking === 'online' ? 'badge-blue' : 'badge-purple' }} text-[10px] px-2 py-1">
                        {{ strtoupper($b->sumber_booking) }}
                    </span>
                    
                    @if($b->dibuatOleh)
                        <p class="text-[10px] text-slate-400 mt-1 truncate max-w-25" title="Dibuat oleh: {{ $b->dibuatOleh->name }}">
                            Oleh: {{ explode(' ', $b->dibuatOleh->name)[0] }}
                        </p>
                    @endif
                </td>

                <td class="p-4">
                    @php $sc = ['pending'=>'badge-yellow','disetujui'=>'badge-blue','ditolak'=>'badge-red','aktif'=>'badge-green','selesai'=>'badge-gray','dibatalkan'=>'badge-red']; @endphp
                    <span class="badge {{ $sc[$b->status] ?? 'badge-gray' }}">{{ ucfirst($b->status) }}</span>
                </td>
                
                <td class="p-4 text-right">
                    <div class="flex items-center gap-1.5 justify-end">
                        <a href="{{ route('kasir.booking.show', $b) }}" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </a>
                        
                        @if($b->status === 'pending')
                        <form method="POST" action="{{ route('kasir.booking.disetujui', $b) }}">
                            @csrf @method('PATCH')
                            <button class="p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                        </form>
                        <button onclick="openRejectModal({{ $b->id }})" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                        @elseif($b->status === 'disetujui')
                        <a href="{{ route('kasir.transaksi.serah-terima', $b->id) }}" 
                           class="px-2.5 py-1 text-xs font-bold bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg border border-blue-200 transition-all flex items-center gap-1">
                            <i data-lucide="key" class="w-3.5 h-3.5"></i> Serah Terima
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center py-12 text-slate-400">Tidak ada data booking</td></tr>
            @endforelse
        </tbody>
    </table>
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
    document.getElementById('rejectForm').action = `/kasir/booking/${id}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}
function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endpush
