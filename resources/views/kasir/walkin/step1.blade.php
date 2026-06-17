@extends('layouts.app')

@section('title', 'Walk-In — Data Pelanggan')
@section('page-title', 'Walk-In: Sewa Langsung')
@section('breadcrumb', 'Kasir / Walk-In / Data Pelanggan')

@section('sidebar-nav')
    @include('components.sidebar-kasir')
@endsection

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="max-w-3xl mx-auto space-y-5 px-2 sm:px-0">

    {{-- Stepper --}}
    <div class="card p-4 sm:p-5">
        <div class="flex items-center justify-center gap-0">
            {{-- Step 1 --}}
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center text-sm font-bold shadow-md shadow-primary-200">1</span>
                <span class="text-sm font-semibold text-primary-700 hidden sm:inline">Data Pelanggan</span>
            </div>
            <div class="w-10 sm:w-16 h-0.5 bg-slate-200 mx-1 sm:mx-2"></div>
            {{-- Step 2 --}}
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-sm font-bold">2</span>
                <span class="text-sm text-slate-400 hidden sm:inline">Booking Kendaraan</span>
            </div>
            <div class="w-10 sm:w-16 h-0.5 bg-slate-200 mx-1 sm:mx-2"></div>
            {{-- Step 3 --}}
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-sm font-bold">3</span>
                <span class="text-sm text-slate-400 hidden sm:inline">Serah Terima</span>
            </div>
        </div>
    </div>

    {{-- Pencarian Pelanggan --}}
    <div class="card p-4 sm:p-5">
        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2 text-base sm:text-lg">
            <i data-lucide="search" class="w-5 h-5 text-primary-600"></i> Cari Pelanggan Terdaftar
        </h3>
        <p class="text-sm text-slate-500 mb-4">Cari berdasarkan NIK, nama, email, atau nomor telepon pelanggan.</p>

        <div class="flex gap-3">
            <div class="flex-1">
                <input type="text" id="searchKeyword" class="form-input w-full" placeholder="Ketik NIK / nama / telepon / email..." autocomplete="off">
            </div>
            <button type="button" id="btnCari" class="btn-primary flex items-center gap-1.5 px-4">
                <i data-lucide="search" class="w-4 h-4"></i> Cari
            </button>
        </div>

        {{-- Loading --}}
        <div id="searchLoading" class="hidden mt-4 text-center py-6">
            <div class="inline-flex items-center gap-2 text-slate-400 text-sm">
                <svg class="animate-spin w-5 h-5" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Mencari...
            </div>
        </div>

        {{-- Hasil Pencarian --}}
        <div id="searchResults" class="mt-4 space-y-2 hidden"></div>

        {{-- Pesan Tidak Ditemukan --}}
        <div id="noResults" class="hidden mt-4 p-4 bg-amber-50 border border-amber-200 rounded-xl text-center">
            <i data-lucide="user-x" class="w-8 h-8 text-amber-400 mx-auto mb-2"></i>
            <p class="text-sm text-amber-700 font-medium">Pelanggan tidak ditemukan</p>
            <p class="text-xs text-amber-500 mt-1">Silakan isi form di bawah untuk mendaftarkan pelanggan baru.</p>
        </div>
    </div>

    {{-- Form Pelanggan Baru --}}
    <div class="card p-4 sm:p-5" id="formPelangganBaru">
        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2 text-base sm:text-lg">
            <i data-lucide="user-plus" class="w-5 h-5 text-primary-600"></i> Daftarkan Pelanggan Baru
        </h3>
        <p class="text-sm text-slate-500 mb-5">Isi data berikut untuk pelanggan yang datang langsung. Password default: <code class="bg-slate-100 px-1.5 py-0.5 rounded text-xs font-mono">password123</code></p>

        <form method="POST" action="{{ route('kasir.walkin.step2') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" class="form-input w-full" placeholder="Nama lengkap pelanggan" required>
                    @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" class="form-input w-full" maxlength="20" placeholder="Nomor Induk Kependudukan">
                    @error('nik') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-input w-full" placeholder="email@example.com" required>
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">No. Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-input w-full" placeholder="08xxxxxxxxxx" required>
                    @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-input w-full">
                        <option value="">-- Pilih --</option>
                        <option value="laki-laki" {{ old('jenis_kelamin') === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="perempuan" {{ old('jenis_kelamin') === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Pekerjaan</label>
                    <input type="text" name="pekerjaan" value="{{ old('pekerjaan') }}" class="form-input w-full" placeholder="Pekerjaan pelanggan">
                </div>
                <div>
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="form-input w-full" placeholder="Kota kelahiran">
                </div>
                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">
                        Tanggal Lahir
                    </label>
                    <input 
                        type="text" 
                        name="tanggal_lahir" 
                        id="tanggal_lahir"
                        value="{{ old('tanggal_lahir') }}" 
                        class="form-input w-full text-sm" 
                        placeholder="DD-MM-YYYY"
                        {{-- readonly dihapus agar user bisa mengetik manual --}}
                    >
                    @error('tanggal_lahir') 
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p> 
                    @enderror
                </div>
                <div>
                    <label class="form-label">Kota</label>
                    <input type="text" name="kota" value="{{ old('kota') }}" class="form-input w-full" placeholder="Kota domisili">
                </div>
                <div class="sm:col-span-2">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" rows="2" class="form-input w-full" placeholder="Alamat lengkap pelanggan...">{{ old('alamat') }}</textarea>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-5 pt-4 border-t border-slate-100">
                <a href="{{ route('kasir.dashboard') }}" class="btn-secondary w-full sm:w-auto text-center justify-center py-2.5 sm:py-2">Batal</a>
                <button type="submit" class="btn-primary w-full sm:w-auto flex items-center justify-center gap-2 py-2.5 sm:py-2">
                    <i data-lucide="arrow-right" class="w-4 h-4"></i> Daftar & Lanjut Pilih Kendaraan
                </button>
            </div>
        </form>
    </div>

    {{-- Form Pelanggan Existing (hidden, diisi via JS) --}}
    <form id="formExisting" method="POST" action="{{ route('kasir.walkin.step2') }}" class="hidden">
        @csrf
        <input type="hidden" name="pelanggan_id" id="selectedPelangganId">
    </form>
</div>
@endsection

@push('scripts')
<script>


flatpickr("#tanggal_lahir", {
    dateFormat: "Y-m-d",      // Format untuk Database (tetap Y-m-d)
    altInput: true,           // Biarkan user melihat format DD-MM-YYYY
    altFormat: "d-m-Y",       // Format yang muncul di layar user
    allowInput: true,         // PENTING: Ini membuat input bisa diketik manual
    maxDate: "today"
});
    
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchKeyword');
    const btnCari = document.getElementById('btnCari');
    const searchResults = document.getElementById('searchResults');
    const noResults = document.getElementById('noResults');
    const searchLoading = document.getElementById('searchLoading');

    async function doSearch() {
        const keyword = searchInput.value.trim();
        if (keyword.length < 2) {
            searchResults.classList.add('hidden');
            noResults.classList.add('hidden');
            return;
        }

        searchLoading.classList.remove('hidden');
        searchResults.classList.add('hidden');
        noResults.classList.add('hidden');

        try {
            const res = await fetch('{{ route("kasir.walkin.cari") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ keyword })
            });

            const data = await res.json();
            searchLoading.classList.add('hidden');

            if (data.length === 0) {
                noResults.classList.remove('hidden');
                searchResults.classList.add('hidden');
                return;
            }

            searchResults.innerHTML = '';
            data.forEach(function(p) {
                const blacklistBadge = p.is_blacklisted 
                    ? '<span class="badge badge-red text-xs">Blacklist</span>' 
                    : '';
                const isDisabled = p.is_blacklisted ? 'opacity-50 pointer-events-none' : '';

                const card = document.createElement('div');
                card.className = `flex items-center justify-between p-3 sm:p-4 bg-slate-50 hover:bg-primary-50 border border-slate-100 hover:border-primary-200 rounded-xl transition-all cursor-pointer group ${isDisabled}`;
                card.innerHTML = `
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-slate-700 group-hover:text-primary-700 transition-colors">${p.nama}</p>
                            ${blacklistBadge}
                        </div>
                        <div class="flex flex-wrap gap-x-4 gap-y-0.5 mt-1 text-xs text-slate-400">
                            <span><i data-lucide="credit-card" class="w-3 h-3 inline"></i> ${p.nik}</span>
                            <span><i data-lucide="phone" class="w-3 h-3 inline"></i> ${p.phone}</span>
                            <span><i data-lucide="mail" class="w-3 h-3 inline"></i> ${p.email}</span>
                        </div>
                    </div>
                    <div class="ml-3 shrink-0">
                        <span class="px-3 py-1.5 text-xs font-bold bg-primary-50 text-primary-600 group-hover:bg-primary-600 group-hover:text-white rounded-lg border border-primary-200 group-hover:border-primary-600 transition-all">Pilih</span>
                    </div>
                `;

                if (!p.is_blacklisted) {
                    card.addEventListener('click', function() {
                        document.getElementById('selectedPelangganId').value = p.id;
                        document.getElementById('formExisting').submit();
                    });
                }

                searchResults.appendChild(card);
            });

            searchResults.classList.remove('hidden');

            // Re-initialize lucide icons for dynamically added content
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

        } catch (err) {
            searchLoading.classList.add('hidden');
            console.error('Search error:', err);
        }
    }

    btnCari.addEventListener('click', doSearch);
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            doSearch();
        }
    });
});
</script>
@endpush
