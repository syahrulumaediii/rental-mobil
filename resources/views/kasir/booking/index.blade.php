@extends('layouts.app')

@section('title', 'Booking')
@section('page-title', 'Manajemen Booking')
@section('breadcrumb', 'Kasir / Booking')

@section('sidebar-nav')
    @include('components.sidebar-kasir')
@endsection

@section('content')
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
                @foreach(['pending','disetujui','ditolak','berlangsung','selesai','dibatalkan'] as $s)
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
    <table>
        <thead>
            <tr>
                <th>Kode Booking</th>
                <th>Pelanggan</th>
                <th>Kendaraan</th>
                <th>Periode</th>
                <th>Estimasi Biaya</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($booking as $b)
            <tr>
                <td><span class="font-mono text-xs font-bold text-slate-600">{{ $b->kode_booking }}</span></td>
                <td>
                    <p class="font-semibold text-slate-700">{{ $b->pelanggan->user->name }}</p>
                    <p class="text-xs text-slate-400">{{ $b->pelanggan->user->phone }}</p>
                </td>
                <td>
                    <p class="font-medium text-slate-700">{{ $b->kendaraan->nama }}</p>
                    <p class="text-xs text-slate-400 font-mono">{{ $b->kendaraan->plat_nomor }}</p>
                </td>
                <td>
                    <p class="text-sm text-slate-600">{{ $b->tanggal_mulai->format('d M Y') }}</p>
                    <p class="text-xs text-slate-400">s/d {{ $b->tanggal_selesai->format('d M Y') }} · {{ $b->durasi_hari }} hari</p>
                </td>
                <td class="font-semibold text-slate-700">Rp {{ number_format($b->estimasi_biaya, 0, ',', '.') }}</td>
                <td>
                    @php $sc = ['pending'=>'badge-yellow','disetujui'=>'badge-blue','ditolak'=>'badge-red','berlangsung'=>'badge-green','selesai'=>'badge-gray','dibatalkan'=>'badge-red']; @endphp
                    <span class="badge {{ $sc[$b->status] ?? 'badge-gray' }}">{{ ucfirst($b->status) }}</span>
                </td>
                <td>
                    <div class="flex items-center gap-1.5 justify-end">
                        <a href="{{ route('kasir.booking.show', $b) }}" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"><i data-lucide="eye" class="w-4 h-4"></i></a>
                        @if($b->status === 'pending')
                        <form method="POST" action="{{ route('kasir.booking.approve', $b) }}">
                            @csrf @method('PATCH')
                            <button class="p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Setujui">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                        </form>
                        <button onclick="openRejectModal({{ $b->id }})" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Tolak">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-12 text-slate-400">Tidak ada data booking</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($booking->hasPages())
    <div class="px-5 py-4 border-t border-slate-50">{{ $booking->withQueryString()->links() }}</div>
    @endif
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
