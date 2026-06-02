@extends('layouts.app')

@section('title', 'Dokumen Saya')
@section('page-title', 'Dokumen Saya')

@section('topbar-actions')
<a href="{{ route('pelanggan.dokumen.create') }}" class="btn-primary flex items-center gap-2">
    <i data-lucide="upload" class="w-4 h-4"></i> Upload Dokumen
</a>
@endsection

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')

<div class="bg-blue-50 border border-blue-200 rounded-2xl px-5 py-4 mb-5 text-sm text-blue-800">
    <p class="font-semibold mb-1 flex items-center gap-2"><i data-lucide="info" class="w-4 h-4"></i>Dokumen yang dibutuhkan untuk verifikasi:</p>
    <ul class="list-disc list-inside space-y-0.5 text-blue-700">
        <li>KTP (Kartu Tanda Penduduk) — wajib</li>
        <li>SIM (Surat Izin Mengemudi) — wajib</li>
        <li>Paspor / dokumen lain — opsional</li>
    </ul>
</div>

@php
$jenisList = ['ktp' => 'KTP', 'sim' => 'SIM', 'paspor' => 'Paspor', 'lainnya' => 'Lainnya'];
@endphp

<div class="grid sm:grid-cols-2 gap-4">
    @foreach($jenisList as $jenis => $label)
    @php $dok = $dokumen->where('jenis_dokumen', $jenis)->first(); @endphp
    <div class="card p-5">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 {{ $dok ? ($dok->status === 'verified' ? 'bg-green-100' : ($dok->status === 'ditolak' ? 'bg-red-100' : 'bg-yellow-100')) : 'bg-slate-100' }} rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="{{ $dok ? ($dok->status === 'verified' ? 'shield-check' : ($dok->status === 'ditolak' ? 'shield-x' : 'clock')) : 'file-plus' }}"
                   class="w-6 h-6 {{ $dok ? ($dok->status === 'verified' ? 'text-green-600' : ($dok->status === 'ditolak' ? 'text-red-500' : 'text-yellow-600')) : 'text-slate-400' }}"></i>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="font-bold text-slate-700">{{ $label }}</h3>
                    @if($jenis === 'ktp' || $jenis === 'sim')
                    <span class="badge badge-red text-[10px] px-1.5 py-0.5">Wajib</span>
                    @endif
                </div>
                @if($dok)
                    @php $ds = ['menunggu'=>['badge-yellow','Menunggu verifikasi'],'verified'=>['badge-green','Terverifikasi'],'ditolak'=>['badge-red','Ditolak']]; @endphp
                    <span class="badge {{ $ds[$dok->status][0] ?? 'badge-gray' }} text-xs">{{ $ds[$dok->status][1] ?? '-' }}</span>
                    <p class="text-xs text-slate-400 mt-1">Diunggah {{ $dok->created_at->format('d M Y') }}</p>
                    @if($dok->catatan)<p class="text-xs text-red-500 mt-1">{{ $dok->catatan }}</p>@endif
                    <div class="flex gap-2 mt-3">
                        <a href="{{ Storage::url($dok->file_path) }}" target="_blank" class="text-xs text-primary-600 font-semibold hover:underline flex items-center gap-1">
                            <i data-lucide="eye" class="w-3 h-3"></i> Lihat
                        </a>
                        @if($dok->status !== 'verified')
                        <form method="POST" action="{{ route('pelanggan.dokumen.destroy', $dok) }}" onsubmit="return confirm('Hapus dokumen ini?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 font-semibold hover:underline flex items-center gap-1">
                                <i data-lucide="trash-2" class="w-3 h-3"></i> Hapus
                            </button>
                        </form>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-slate-400">Belum diunggah</p>
                    <a href="{{ route('pelanggan.dokumen.create') }}?jenis={{ $jenis }}" class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-primary-600 hover:underline">
                        <i data-lucide="upload" class="w-3 h-3"></i> Upload
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
