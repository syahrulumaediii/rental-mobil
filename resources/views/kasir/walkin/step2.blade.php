@extends('layouts.app')

@section('title', 'Walk-In — Booking Kendaraan')
@section('page-title', 'Walk-In: Sewa Langsung')
@section('breadcrumb', 'Kasir / Walk-In / Booking Kendaraan')

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
            {{-- Step 1 ✓ --}}
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold shadow-md shadow-green-200">
                    <i data-lucide="check" class="w-4 h-4"></i>
                </span>
                <span class="text-sm font-semibold text-green-600 hidden sm:inline">Data Pelanggan</span>
            </div>
            <div class="w-10 sm:w-16 h-0.5 bg-green-300 mx-1 sm:mx-2"></div>
            {{-- Step 2 aktif --}}
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center text-sm font-bold shadow-md shadow-primary-200">2</span>
                <span class="text-sm font-semibold text-primary-700 hidden sm:inline">Booking Kendaraan</span>
            </div>
            <div class="w-10 sm:w-16 h-0.5 bg-slate-200 mx-1 sm:mx-2"></div>
            {{-- Step 3 --}}
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-sm font-bold">3</span>
                <span class="text-sm text-slate-400 hidden sm:inline">Serah Terima</span>
            </div>
        </div>
    </div>

    {{-- Info Pelanggan Terpilih --}}
    <div class="card p-4 sm:p-5 border-l-4 border-l-green-500">
        <h3 class="font-bold text-slate-700 mb-3 flex items-center gap-2 text-base">
            <i data-lucide="user-check" class="w-5 h-5 text-green-500"></i> Pelanggan Terpilih
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-1 text-sm">
            @foreach([
                ['Nama', $pelanggan->user->name],
                ['NIK', $pelanggan->nik ?? '-'],
                ['Email', $pelanggan->user->email],
                ['Telepon', $pelanggan->user->phone ?? '-'],
                ['Alamat', $pelanggan->alamat ?? '-'],
            ] as [$lbl, $val])
            <div class="flex flex-col sm:flex-row sm:justify-between py-1.5 border-b border-slate-50 last:border-0">
                <span class="text-xs text-slate-400 uppercase tracking-wider sm:normal-case sm:text-sm">{{ $lbl }}</span>
                <span class="font-medium text-slate-700 sm:text-right mt-0.5 sm:mt-0">{{ $val }}</span>
            </div>
            @endforeach
        </div>
        <div class="mt-3">
            <a href="{{ route('kasir.walkin.step1') }}" class="text-xs text-primary-600 hover:text-primary-700 font-medium inline-flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-3 h-3"></i> Ganti pelanggan
            </a>
        </div>
    </div>

    {{-- Form Booking --}}
    <div class="card p-4 sm:p-5">
        <h3 class="font-bold text-slate-700 mb-4 flex items-center gap-2 text-base sm:text-lg">
            <i data-lucide="car" class="w-5 h-5 text-primary-600"></i> Form Booking Kendaraan
        </h3>
        <form method="POST" action="{{ route('kasir.walkin.store') }}">
            @csrf
            <input type="hidden" name="pelanggan_id" value="{{ $pelanggan->id }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Pilih Kendaraan --}}
                <div class="sm:col-span-2">
                    <label class="form-label">Pilih Kendaraan <span class="text-red-500">*</span></label>
                    <select name="kendaraan_id" id="kendaraanSelect" class="form-input w-full" required>
                        <option value="">-- Pilih kendaraan --</option>
                        @foreach($kendaraan as $k)
                        <option value="{{ $k->id }}" 
                                data-tarif="{{ $k->tarif_harian }}"
                                data-foto="{{ $k->foto ? asset('storage/' . $k->foto) : '' }}"
                                data-plat="{{ $k->plat_nomor }}"
                                data-kategori="{{ $k->kategori->nama ?? '-' }}"
                                data-transmisi="{{ ucfirst($k->transmisi) }}"
                                data-kapasitas="{{ $k->kapasitas }}"
                                data-warna="{{ $k->warna }}"
                                {{ old('kendaraan_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama }} — {{ $k->plat_nomor }} (Rp {{ number_format($k->tarif_harian, 0, ',', '.') }}/hari)
                        </option>
                        @endforeach
                    </select>
                    @error('kendaraan_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Preview Kendaraan --}}
                <div class="sm:col-span-2 hidden" id="kendaraanPreview">
                    <div class="flex flex-col sm:flex-row gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div id="previewFoto" class="w-full sm:w-32 h-24 bg-slate-200 rounded-xl flex items-center justify-center text-slate-400 overflow-hidden shrink-0">
                            <i data-lucide="car" class="w-8 h-8"></i>
                        </div>
                        <div class="flex-1 grid grid-cols-2 gap-x-4 gap-y-1 text-sm">
                            <div>
                                <span class="text-xs text-slate-400">Plat Nomor</span>
                                <p class="font-mono font-bold text-slate-700" id="previewPlat">-</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400">Kategori</span>
                                <p class="font-medium text-slate-700" id="previewKategori">-</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400">Transmisi</span>
                                <p class="font-medium text-slate-700" id="previewTransmisi">-</p>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400">Tarif / Hari</span>
                                <p class="font-bold text-primary-600" id="previewTarif">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tanggal Mulai --}}
                {{-- Tanggal Mulai --}}
                <div>
                    <label class="form-label">Tanggal Mulai Sewa <span class="text-red-500">*</span></label>
                    <input type="text" name="tanggal_mulai" id="tanggalMulai" 
                        value="{{ old('tanggal_mulai', date('Y-m-d')) }}" 
                        class="form-input w-full" placeholder="DD-MM-YYYY" required>
                    @error('tanggal_mulai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal Selesai --}}
                <div>
                    <label class="form-label">Tanggal Selesai Sewa <span class="text-red-500">*</span></label>
                    <input type="text" name="tanggal_selesai" id="tanggalSelesai" 
                        value="{{ old('tanggal_selesai') }}" 
                        class="form-input w-full" placeholder="DD-MM-YYYY" required>
                    @error('tanggal_selesai') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Ringkasan Hitung --}}
                <div class="sm:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-4 bg-linear-to-r from-primary-50 to-blue-50 rounded-xl border border-primary-100">
                        <div class="text-center sm:text-left">
                            <span class="text-xs text-slate-400 uppercase tracking-wider">Durasi</span>
                            <p class="text-lg font-bold text-slate-800" id="displayDurasi">0 hari</p>
                        </div>
                        <div class="text-center">
                            <span class="text-xs text-slate-400 uppercase tracking-wider">Tarif / Hari</span>
                            <p class="text-lg font-bold text-slate-700" id="displayTarif">Rp 0</p>
                        </div>
                        <div class="text-center sm:text-right">
                            <span class="text-xs text-primary-500 uppercase tracking-wider font-semibold">Estimasi Total</span>
                            <p class="text-xl font-extrabold text-primary-700" id="displayEstimasi">Rp 0</p>
                        </div>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="sm:col-span-2">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" rows="2" class="form-input w-full" placeholder="Catatan opsional untuk booking ini...">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row gap-3 mt-5 pt-4 border-t border-slate-100">
                <a href="{{ route('kasir.walkin.step1') }}" class="btn-secondary w-full sm:w-auto text-center justify-center py-2.5 sm:py-2">
                    <i data-lucide="arrow-left" class="w-4 h-4 inline"></i> Kembali
                </a>
                <button type="submit" class="btn-primary w-full sm:w-auto flex items-center justify-center gap-2 py-2.5 sm:py-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> Buat Booking & Lanjut Serah Terima
                </button>
            </div>
        </form>
    </div>


    
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const kendaraanSelect = document.getElementById('kendaraanSelect');
    // const tanggalMulai = document.getElementById('tanggalMulai');
    // const tanggalSelesai = document.getElementById('tanggalSelesai');
    const kendaraanPreview = document.getElementById('kendaraanPreview');
    const displayDurasi = document.getElementById('displayDurasi');
    const displayTarif = document.getElementById('displayTarif');
    const displayEstimasi = document.getElementById('displayEstimasi');

    // Inisialisasi Flatpickr
    const fpMulai = flatpickr("#tanggalMulai", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-m-Y",
        minDate: "today",
        onChange: function(selectedDates, dateStr) {
            fpSelesai.set('minDate', dateStr); // Selesai minimal sama dengan Mulai
            updateEstimasi();
        }
    });

    const fpSelesai = flatpickr("#tanggalSelesai", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-m-Y",
        minDate: "today",
        onChange: function() {
            updateEstimasi();
        }
    });


    let currentTarif = 0;

    function formatRupiah(num) {
        return 'Rp ' + num.toLocaleString('id-ID');
    }

    function updateKendaraanPreview() {
        const selected = kendaraanSelect.options[kendaraanSelect.selectedIndex];
        if (!kendaraanSelect.value) {
            kendaraanPreview.classList.add('hidden');
            currentTarif = 0;
            updateEstimasi();
            return;
        }

        currentTarif = parseFloat(selected.dataset.tarif) || 0;
        const foto = selected.dataset.foto;
        const plat = selected.dataset.plat;
        const kategori = selected.dataset.kategori;
        const transmisi = selected.dataset.transmisi;

        document.getElementById('previewPlat').textContent = plat;
        document.getElementById('previewKategori').textContent = kategori;
        document.getElementById('previewTransmisi').textContent = transmisi;
        document.getElementById('previewTarif').textContent = formatRupiah(currentTarif);

        const fotoEl = document.getElementById('previewFoto');
        if (foto) {
            fotoEl.innerHTML = `<img src="${foto}" class="w-full h-full object-cover" alt="Foto kendaraan">`;
        } else {
            fotoEl.innerHTML = '<i data-lucide="car" class="w-8 h-8"></i>';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        kendaraanPreview.classList.remove('hidden');
        updateEstimasi();
    }

    function updateEstimasi() {
            // Mengambil nilai dari input (yang sudah format Y-m-d dari Flatpickr)
            const mulaiVal = document.getElementById('tanggalMulai').value;
            const selesaiVal = document.getElementById('tanggalSelesai').value;

            if (mulaiVal && selesaiVal) {
                const mulai = new Date(mulaiVal);
                const selesai = new Date(selesaiVal);

                if (selesai > mulai) {
                    const durasi = Math.ceil((selesai - mulai) / (1000 * 60 * 60 * 24));
                    const estimasi = durasi * currentTarif;

                    displayDurasi.textContent = durasi + ' hari';
                    displayTarif.textContent = formatRupiah(currentTarif);
                    displayEstimasi.textContent = formatRupiah(estimasi);
                    return;
                }
            }
            
            displayDurasi.textContent = '0 hari';
            displayTarif.textContent = formatRupiah(currentTarif);
            displayEstimasi.textContent = 'Rp 0';
        }

        kendaraanSelect.addEventListener('change', updateKendaraanPreview);
        tanggalMulai.addEventListener('change', updateEstimasi);
        tanggalSelesai.addEventListener('change', updateEstimasi);

        // Initial load for old() values
        updateKendaraanPreview();
    });
</script>
@endpush
