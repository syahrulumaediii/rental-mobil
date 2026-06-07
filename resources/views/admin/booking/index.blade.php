@extends('layouts.app')

@section('title', 'Booking Admin')
@section('page-title', 'Manajemen Booking')
@section('breadcrumb', 'Admin / Booking')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
{{-- Card Filter --}}
<div class="card p-4 sm:p-5 mb-5">
    <form method="GET" class="flex flex-col md:flex-row md:items-end gap-4">
        {{-- Input Cari --}}
        <div class="flex-1">
            <label class="form-label block text-xs font-semibold text-slate-500 mb-1">Cari Kode / Pelanggan</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input w-full" placeholder="BKG-... atau nama pelanggan">
        </div>
        
        {{-- Dropdown Status --}}
        <div class="w-full md:w-44">
            <label class="form-label block text-xs font-semibold text-slate-500 mb-1">Status</label>
            <select name="status" class="form-input w-full">
                <option value="">Semua</option>
                @foreach(['pending','disetujui','ditolak','aktif','selesai','dibatalkan'] as $s)
                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        
        {{-- Tombol Aksi Filter --}}
        <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0">
            <button type="submit" class="btn-primary flex-1 md:flex-none justify-center px-5">Filter</button>
            @if(request()->anyFilled(['search','status']))
            <a href="{{ route('admin.booking.index') }}" class="btn-secondary flex-1 md:flex-none text-center justify-center">Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- Card Konten Utama Tabel --}}
<div class="card mb-6 overflow-hidden">
    {{-- Wrapper responsive table --}}
    <div class="overflow-x-auto w-full">
        <table class="min-w-full divide-y divide-slate-100">
            <thead>
                <tr class="bg-slate-50/50 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    <th class="px-5 py-3.5">Kode Booking</th>
                    <th class="px-5 py-3.5">Pelanggan</th>
                    <th class="px-5 py-3.5">Kendaraan</th>
                    <th class="px-5 py-3.5">Periode</th>
                    <th class="px-5 py-3.5">Estimasi Biaya</th>
                    <th class="px-5 py-3.5">Status</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm whitespace-nowrap">
                @forelse($booking as $b)
                <tr class="hover:bg-slate-50/30 transition-colors">
                    {{-- Kode --}}
                    <td class="px-5 py-3.5">
                        <span class="font-mono text-xs font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-md">{{ $b->kode_booking }}</span>
                    </td>
                    {{-- Pelanggan --}}
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-slate-700">{{ $b->pelanggan->user->name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $b->pelanggan->user->phone }}</p>
                    </td>
                    {{-- Kendaraan --}}
                    <td class="px-5 py-3.5">
                        <p class="font-medium text-slate-700">{{ $b->kendaraan->nama }}</p>
                        <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $b->kendaraan->plat_nomor }}</p>
                    </td>
                    {{-- Periode --}}
                    <td class="px-5 py-3.5">
                        <p class="font-semibold text-xs text-blue-600">{{ $b->tanggal_mulai->format('d M Y') }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">s/d {{ $b->tanggal_selesai->format('d M Y') }} · <span class="font-medium text-slate-600">{{ $b->durasi_hari }} hari</span></p>
                    </td>
                    {{-- Biaya --}}
                    <td class="px-5 py-3.5 font-bold text-slate-700">
                        Rp {{ number_format($b->estimasi_biaya, 0, ',', '.') }}
                    </td>
                    {{-- Status --}}
                    <td class="px-5 py-3.5">
                        @php $sc = ['pending'=>'badge-yellow','disetujui'=>'badge-blue','ditolak'=>'badge-red','aktif'=>'badge-green','selesai'=>'badge-gray','dibatalkan'=>'badge-red']; @endphp
                        <span class="badge {{ $sc[$b->status] ?? 'badge-gray' }} text-[11px]">{{ ucfirst($b->status) }}</span>
                    </td>
                    {{-- Aksi --}}
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center gap-1.5 justify-end">
                            <a href="{{ route('admin.booking.show', $b) }}" class="p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors flex items-center justify-center" title="Lihat Detail">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            
                            @if($b->status === 'pending')
                            <form method="POST" action="{{ route('admin.booking.disetujui', $b) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors flex items-center justify-center" title="Setujui Booking">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <button onclick="openRejectModal({{ $b->id }})" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors flex items-center justify-center" title="Tolak Booking">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>

                            @elseif($b->status === 'disetujui')
                            <a href="{{ route('admin.transaksi.serah-terima', $b->id) }}" 
                               class="px-2.5 py-1 text-xs font-bold bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg border border-blue-200 transition-all flex items-center gap-1 shadow-sm" 
                               title="Proses Serah Terima Mobil">
                                <i data-lucide="key" class="w-3.5 h-3.5 shrink-0"></i>
                                <span>Serah Terima</span>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-12 text-slate-400 font-medium text-xs">
                        <i data-lucide="folder-open" class="w-6 h-6 mx-auto mb-2 text-slate-300"></i>
                        Tidak ada data booking ditemukan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination Area --}}
    @if($booking->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/30">{{ $booking->withQueryString()->links() }}</div>
    @endif
</div>

{{-- Reject Modal (Centered & Responsive) --}}
<div id="rejectModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 transition-all animate-fade-in">
    <div class="bg-white rounded-2xl w-full max-w-md p-5 sm:p-6 shadow-xl border border-slate-100">
        <h3 class="font-bold text-base sm:text-lg text-slate-800 mb-1">Tolak Booking</h3>
        <p class="text-xs sm:text-sm text-slate-500 mb-4">Berikan alasan penolakan booking ini dengan jelas.</p>
        <form id="rejectForm" method="POST">
            @csrf @method('PATCH')
            <div class="mb-4">
                <label class="form-label block text-xs font-semibold text-slate-500 mb-1">Alasan Penolakan</label>
                <textarea name="alasan_penolakan" rows="3" class="form-input w-full" required placeholder="Contoh: Unit mobil sedang dalam perbaikan berkala..."></textarea>
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