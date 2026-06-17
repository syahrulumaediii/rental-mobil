@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('topbar-actions')
<a href="{{ route('pelanggan.profil.edit') }}" class="btn-secondary flex items-center gap-2">
    <i data-lucide="pencil" class="w-4 h-4"></i> Edit Profil
</a>
@endsection

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="card p-6">
        <div class="flex items-center gap-5 mb-6">
            <div class="w-16 h-16 shrink-0">
                    @if($pelanggan->foto_profil)
                        <img src="{{ asset('storage/' . $pelanggan->foto_profil) }}" 
                            alt="Foto Profil" 
                            class="w-16 h-16 rounded-full object-cover border border-slate-200">
                    @else
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center font-extrabold text-primary-700 text-2xl uppercase">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                </div>
            <div>
                <h2 class="font-extrabold text-slate-800 text-xl">{{ $user->name }}</h2>
                <p class="text-slate-400 text-sm">{{ $user->email }}</p>
                <p class="text-slate-400 text-sm">{{ $user->phone }}</p>
            </div>

            
            <div class="ml-auto">
                @php $sv = ['belum_verifikasi'=>['badge-gray','Belum Verifikasi'],'pending'=>['badge-yellow','Pending'],'verified'=>['badge-green','Terverifikasi'],'rejected'=>['badge-red','Rejected']]; @endphp
                <span class="badge {{ $sv[$pelanggan->status_verifikasi ?? 'belum_verifikasi'][0] }}">
                    {{ $sv[$pelanggan->status_verifikasi ?? 'belum_verifikasi'][1] }}
                </span>
            </div>
        </div>

        <h3 class="font-semibold text-slate-600 text-xs uppercase tracking-widest mb-3">Data Pribadi</h3>
        <div class="grid grid-cols-2 gap-x-8 gap-y-2 text-sm">
            @foreach([
                ['NIK',            $pelanggan->nik ?? '-'],
                ['Tempat Lahir',   $pelanggan->tempat_lahir ?? '-'],
                ['Tanggal Lahir',  $pelanggan->tanggal_lahir?->format('d M Y') ?? '-'],
                ['Jenis Kelamin',  ucfirst($pelanggan->jenis_kelamin ?? '-')],
                ['Kota',           $pelanggan->kota ?? '-'],
                ['Pekerjaan',      $pelanggan->pekerjaan ?? '-'],
            ] as [$lbl,$val])
            <div class="flex justify-between py-2 border-b border-slate-50">
                <span class="text-slate-400">{{ $lbl }}</span>
                <span class="font-medium text-slate-700">{{ $val }}</span>
            </div>
            @endforeach
        </div>
        @if($pelanggan->alamat)
        <div class="mt-3 pt-3 border-t border-slate-50 text-sm">
            <p class="text-slate-400 mb-1">Alamat</p>
            <p class="text-slate-700">{{ $pelanggan->alamat }}</p>
        </div>
        @endif
    </div>

    <div class="card p-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-slate-700">Password</h3>
                <p class="text-sm text-slate-400 mt-0.5">Perbarui password akun Anda secara berkala</p>
            </div>
            <a href="{{ route('pelanggan.profil.edit-password') }}" class="btn-secondary text-xs px-3 py-1.5">Ganti Password</a>
        </div>
    </div>
</div>
@endsection
