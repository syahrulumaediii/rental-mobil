@extends('layouts.app')

@section('title', 'Detail Pelanggan')
@section('page-title', 'Detail Pelanggan')
@section('breadcrumb', 'Admin / Pelanggan / Detail')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
<div class="grid lg:grid-cols-3 gap-6">

    {{-- Info Pelanggan --}}
    <div class="lg:col-span-1 space-y-5">
        <div class="card p-6 text-center">
            {{-- Foto Profil --}}
            <div class="mx-auto mb-3 w-20 h-20 rounded-full border-2 border-slate-100 overflow-hidden shadow-sm">
                @if($pelanggan->foto_profil)
                    <img src="{{ asset('storage/' . $pelanggan->foto_profil) }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-primary-100 flex items-center justify-center font-extrabold text-primary-700 text-2xl">
                        {{ strtoupper(substr($pelanggan->user->name, 0, 2)) }}
                    </div>
                @endif
            </div>
            
            <h2 class="font-bold text-slate-800 text-lg">{{ $pelanggan->user->name }}</h2>
            <p class="text-sm text-slate-400">{{ $pelanggan->user->email }}</p>
            <p class="text-sm text-slate-400">{{ $pelanggan->user->phone }}</p>
            <div class="mt-3 flex flex-wrap justify-center gap-2">
                @php $sv = ['belum_verifikasi'=>'badge-gray','pending'=>'badge-yellow','verified'=>'badge-green','rejected'=>'badge-red']; @endphp
                <span class="badge {{ $sv[$pelanggan->status_verifikasi] ?? 'badge-gray' }}">{{ ucwords(str_replace('_',' ',$pelanggan->status_verifikasi)) }}</span>
                @if($pelanggan->isBlacklisted()) <span class="badge badge-red"><i data-lucide="ban" class="w-3 h-3 mr-1"></i>Blacklist</span> @endif
            </div>
        </div>

        {{-- Data Pribadi (Ditambahkan wrapper agar tidak berantakan) --}}
        <div class="card p-5">
            <h3 class="font-semibold text-slate-700 mb-3 text-sm">Data Pribadi</h3>
            <div class="space-y-3">
                @foreach([['NIK',$pelanggan->nik ?? '-'],['Tempat Lahir',$pelanggan->tempat_lahir ?? '-'],['Tgl Lahir',$pelanggan->tanggal_lahir?->format('d M Y') ?? '-'],['Jenis Kelamin',ucfirst($pelanggan->jenis_kelamin ?? '-')],['Kota',$pelanggan->kota ?? '-'],['Pekerjaan',$pelanggan->pekerjaan ?? '-']] as [$lbl,$val])
                <div class="flex justify-between text-sm border-b border-slate-50 pb-1.5 last:border-0">
                    <span class="text-slate-400">{{ $lbl }}</span>
                    <span class="font-medium text-slate-700 ml-4 text-right">{{ $val }}</span>
                </div>
                @endforeach
                @if($pelanggan->alamat)
                <div class="pt-2 text-sm"><p class="text-slate-400 mb-1">Alamat</p><p class="text-slate-700">{{ $pelanggan->alamat }}</p></div>
                @endif
            </div>
        </div>

        {{-- Tindakan (Tetap sama) --}}
        <div class="card p-5">
            <h3 class="font-semibold text-slate-700 mb-3 text-sm">Tindakan</h3>
            @if($pelanggan->isBlacklisted())
            <form method="POST" action="{{ route('admin.pelanggan.unblacklist', $pelanggan) }}" onsubmit="return confirm('Hapus dari blacklist?')">
                @csrf @method('DELETE')
                <button class="w-full btn-secondary text-green-600 hover:bg-green-50">
                    <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i> Hapus dari Blacklist
                </button>
            </form>
            @else
            <button onclick="document.getElementById('blacklistForm').classList.toggle('hidden')" class="w-full btn-danger">
                <i data-lucide="ban" class="w-4 h-4 inline mr-1"></i> Masukkan Blacklist
            </button>
            <form id="blacklistForm" method="POST" action="{{ route('admin.pelanggan.blacklist', $pelanggan) }}" class="hidden mt-3">
                @csrf
                <textarea name="alasan" rows="2" class="form-input text-sm" placeholder="Alasan blacklist..." required></textarea>
                <button type="submit" class="w-full btn-danger mt-2">Konfirmasi</button>
            </form>
            @endif
        </div>
    </div>

    <div class="lg:col-span-2 space-y-5">
        {{-- Dokumen --}}
        <div class="card">
            <div class="px-5 py-4 border-b border-slate-50">
                <h3 class="font-bold text-slate-700">Dokumen Pelanggan</h3>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($pelanggan->dokumen as $dok)
                <div class="px-5 py-4 flex items-center gap-4">
                    <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="file-text" class="w-5 h-5 text-slate-500"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-slate-700 capitalize">{{ strtoupper($dok->jenis_dokumen) }}</p>
                        <p class="text-xs text-slate-400">Diunggah {{ $dok->created_at->format('d M Y') }}</p>
                        @if($dok->catatan)<p class="text-xs text-slate-500 mt-0.5">{{ $dok->catatan }}</p>@endif
                    </div>
                    @php $ds = ['pending'=>'badge-yellow','verified'=>'badge-green','rejected'=>'badge-red']; @endphp
                    <span class="badge {{ $ds[$dok->status] ?? 'badge-gray' }}">{{ ucfirst($dok->status) }}</span>
                    <a href="{{ Storage::url($dok->file_path) }}" target="_blank" class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg"><i data-lucide="external-link" class="w-4 h-4"></i></a>

                @if($dok->status === 'pending')
                <div class="flex gap-2">
                    {{-- Tombol Verified (Sudah Benar) --}}
                    <form method="POST" action="{{ route('admin.dokumen.verifikasi', $dok) }}">
                        @csrf 
                        @method('PATCH')
                        <input type="hidden" name="status" value="verified">
                        <button type="submit" class="px-3 py-1.5 bg-green-50 text-green-700 text-xs font-semibold rounded-lg hover:bg-green-100">Verified</button>
                    </form>
                    
                    {{-- Tombol Tolak (DIPERBAIKI: Menggunakan 'this' dan 'data-action') --}}
                    <button type="button" 
                            onclick="openRejectDok(this)" 
                            data-action="{{ route('admin.dokumen.verifikasi', $dok) }}" 
                            class="px-3 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-100">
                        Tolak
                    </button>
                </div>
                @endif

                </div>
                @empty
                <div class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada dokumen yang diunggah</div>
                @endforelse
            </div>
        </div>

        {{-- Riwayat Booking --}}
        <div class="card">
            <div class="px-5 py-4 border-b border-slate-50">
                <h3 class="font-bold text-slate-700">Riwayat Booking</h3>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($pelanggan->booking->take(5) as $b)
                <div class="px-5 py-3.5 flex items-center gap-3">
                    <div class="flex-1">
                        <p class="font-mono text-xs font-bold text-slate-600">{{ $b->kode_booking }}</p>
                        <p class="text-sm text-slate-600">{{ $b->kendaraan->nama ?? '-' }}</p>
                        <p class="text-xs text-slate-400">{{ $b->tanggal_mulai?->format('d M Y') }} – {{ $b->tanggal_selesai?->format('d M Y') }}</p>
                    </div>
                    @php $sc = ['pending'=>'badge-yellow','disetujui'=>'badge-blue','berlangsung'=>'badge-green','selesai'=>'badge-gray','ditolak'=>'badge-red','dibatalkan'=>'badge-red']; @endphp
                    <span class="badge {{ $sc[$b->status] ?? 'badge-gray' }}">{{ ucfirst($b->status) }}</span>
                </div>
                @empty
                <div class="px-5 py-8 text-center text-slate-400 text-sm">Belum ada riwayat booking</div>
                @endforelse
            </div>
        </div>
    </div>
</div>


{{-- Reject Dokumen Modal --}}
{{-- Reject Dokumen Modal --}}
<div id="rejectDokModal" class="hidden fixed inset-0 bg-black/50 z-50 items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-xl">
        <h3 class="font-bold text-slate-800 mb-4">Tolak Dokumen</h3>
        
        {{-- DIPERBAIKI: Action dikosongkan karena akan diisi oleh JS --}}
        <form id="rejectDokForm" method="POST" action="">
            @csrf 
            @method('PATCH')
            <input type="hidden" name="status" value="rejected"> 
            
            <div class="mb-4">
                <label class="form-label">Catatan Alasan (wajib)</label>
                <textarea name="catatan" rows="2" class="form-input shadow-sm border border-slate-200 rounded-lg p-2 text-sm w-full" placeholder="Contoh: Foto SIM buram atau terpotong..." required></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-danger flex-1">Tolak</button>
                <button type="button" onclick="document.getElementById('rejectDokModal').classList.add('hidden')" class="btn-secondary flex-1">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openRejectDok(button) {
    // 1. Ambil URL lengkap dari atribut data-action milik tombol yang diklik
    const actionUrl = button.getAttribute('data-action');
    
    // 2. Suntikkan URL tersebut ke dalam atribut action milik FORM di dalam modal
    document.getElementById('rejectDokForm').action = actionUrl;
    
    // 3. Tampilkan modalnya
    document.getElementById('rejectDokModal').classList.remove('hidden');
    document.getElementById('rejectDokModal').classList.add('flex');
}
</script>
@endpush
