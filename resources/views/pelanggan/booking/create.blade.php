@extends('layouts.app')

@section('title', 'Buat Booking')
@section('page-title', 'Buat Booking')
@section('breadcrumb', 'Katalog / Create Booking')

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')

{{-- Load Asset Flatpickr Library --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- Root Container Alpine.js dengan Inisialisasi State Awal --}}
<div class="max-w-2xl mx-auto" x-data="{ 
    mulai: '{{ request('start_suggest', old('tanggal_mulai', '')) }}', 
    selesai: @json(old('tanggal_selesai', '')), 
    durasi: 0, 
    estimasi: 0, 
    tarif: {{ $kendaraan->tarif_harian }} 
    }">
    {{-- Ringkasan Informasi Kendaraan yang Dipilih --}}
    <div class="card p-5 mb-5">
        <div class="flex gap-4">
            @if($kendaraan->foto)
            <img src="{{ Storage::url($kendaraan->foto) }}" class="w-24 h-24 rounded-xl object-cover shrink-0">
            @else
            <div class="w-24 h-24 bg-slate-100 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="car" class="w-10 h-10 text-slate-300"></i>
            </div>
            @endif
            <div class="flex-1">
                <h2 class="font-extrabold text-slate-800 text-lg">{{ $kendaraan->nama }}</h2>
                <p class="text-slate-400 text-sm">{{ $kendaraan->merk }} {{ $kendaraan->model }} · {{ $kendaraan->tahun }}</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="badge badge-blue">{{ $kendaraan->kategori->nama ?? '-' }}</span>
                    <span class="badge badge-gray capitalize">{{ $kendaraan->transmisi }}</span>
                    <span class="badge badge-gray capitalize">{{ $kendaraan->bahan_bakar }}</span>
                </div>
                <p class="font-extrabold text-primary-700 text-lg mt-2">
                    Rp {{ number_format($kendaraan->tarif_harian, 0, ',', '.') }} 
                    <span class="text-sm font-normal text-slate-400">/ hari</span>
                </p>
            </div>
        </div>
    </div>

    {{-- Form Utama Pengajuan Booking --}}
    <div class="card p-5">
        <h3 class="font-bold text-slate-700 mb-4">Detail Pemesanan</h3>
        <form method="POST" action="{{ route('pelanggan.booking.store') }}">
            @csrf
            {{-- Hidden ID Kendaraan --}}
            <input type="hidden" name="kendaraan_id" value="{{ $kendaraan->id }}">

            {{-- Input Penjadwalan Waktu Sewa --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                {{-- Komponen Tanggal Mulai --}}
                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Tanggal & Jam Mulai Pengambilan</label>
                    <input type="text" 
                        name="tanggal_mulai" 
                        id="tanggal_mulai"
                        x-model="mulai"
                        @input="hitungDurasi()"
                        class="form-input w-full text-sm" 
                        placeholder="Pilih Tanggal & Waktu Mulai"
                        required>
                    @error('tanggal_mulai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Komponen Tanggal Selesai --}}
                <div>
                    <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Tanggal & Jam Selesai Pengembalian</label>
                    <input type="text" 
                        name="tanggal_selesai" 
                        id="tanggal_selesai"
                        class="form-input w-full text-sm" 
                        placeholder="Pilih Tanggal & Waktu Selesai"
                        autocomplete="off"
                        required>
                    @error('tanggal_selesai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- 🌟 LIVE PREVIEW BOX: Otomatis Muncul Menggunakan Animasi Transisi Alpine --}}
            <div x-show="durasi > 0" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="space-y-3 mb-5">
                
                {{-- Struktur Rincian Pembiayaan Matematis --}}
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                    <h4 class="text-xs font-bold uppercase tracking-wide text-slate-400 mb-2">💡 Kalkulasi Sewa Langsung</h4>
                    <div class="flex justify-between text-sm text-slate-600 border-b border-dashed border-slate-200 pb-2">
                        <span>Durasi Sewa Mobil</span>
                        <span class="font-bold text-slate-800"><span x-text="durasi"></span> Hari</span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-600 border-b border-dashed border-slate-200 py-2">
                        <span>Tarif Kendaraan</span>
                        <span>Rp {{ number_format($kendaraan->tarif_harian, 0, ',', '.') }} / hari</span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm font-bold text-slate-800">Total Estimasi Biaya</span>
                        <span class="text-lg font-black text-primary-700">Rp <span x-text="estimasi.toLocaleString('id-ID')"></span></span>
                    </div>
                </div>

                {{-- Komponen Dokumen Alerts / Kotak Pengingat Pembayaran Kasir --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex gap-3 shadow-sm">
                    <div class="w-8 h-8 bg-blue-500 text-white flex items-center justify-center rounded-lg shrink-0 mt-0.5">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h5 class="text-sm font-bold text-blue-900">Pemberitahuan Pembayaran Cash / Transfer</h5>
                        <p class="text-xs text-blue-700 mt-1 leading-relaxed">
                            Pelanggan Yth, mohon siapkan dana tunai/transfer sebesar <strong class="underline font-bold text-blue-900">Rp <span x-text="estimasi.toLocaleString('id-ID')"></span></strong> untuk diserahkan kepada petugas/kasir kami pada saat serah terima kunci kendaraan di lokasi rental.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Kolom Input Catatan Tambahan --}}
            <div class="mb-5">
                <label class="form-label">Catatan Pemesanan (Opsional)</label>
                <textarea name="catatan" rows="2" class="form-input" placeholder="Permintaan khusus, misal: lepas kunci, pakai supir, perjalanan luar kota, dll...">{{ old('catatan') }}</textarea>
            </div>

            {{-- Kotak Ketentuan Regulasi Validasi Identitas --}}
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-5 text-xs text-amber-700">
                <strong>PENTING:</strong> Kendaraan hanya akan diserahkan jika status dokumen KTP & SIM Anda sudah dinyatakan VALID oleh sistem admin kami.
            </div>

            {{-- Tombol Submit Form Pengajuan --}}
            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1">
                    <i data-lucide="calendar-plus" class="w-4 h-4 inline mr-1"></i> Ajukan Booking Sekarang
                </button>
                <a href="{{ route('pelanggan.katalog') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
/**
 * 1. FUNGSI PARSING & KALKULASI DURASI
 */
function hitungUlangDurasi() {
    const mulaiStr = document.getElementById('tanggal_mulai').value;
    const selesaiStr = document.getElementById('tanggal_selesai').value;

    if (mulaiStr && selesaiStr) {
        const parseDateTime = (str) => {
            let datePart = '';
            let timePart = '';

            if (str.includes(' ')) {
                [datePart, timePart] = str.split(' ');
            } else if (str.length >= 15) {
                datePart = str.substring(0, 10);
                timePart = str.substring(10);
            }

            if (!datePart || !timePart) return null;

            const [d, m, y] = datePart.split('-');
            const [h, i] = timePart.split(':');
            return new Date(y, m - 1, d, h, i);
        };

        const date1 = parseDateTime(mulaiStr);
        const date2 = parseDateTime(selesaiStr);

        if (date1 && date2 && date2 > date1) {
            const diffTime = date2 - date1;
            const diffHours = diffTime / (1000 * 60 * 60);
            const days = Math.ceil(diffHours / 24); 
            return days > 0 ? days : 1;
        }
    }
    return 0;
}

/**
 * 2. JEMBATAN STATE REAKTIF ALPINE.JS
 */
function updateAlpineState() {
    const alpineEl = document.querySelector('[x-data]');
    if (alpineEl && alpineEl.__x) {
        const durasiHari = hitungUlangDurasi();
        alpineEl.__x.$data.durasi = durasiHari;
        alpineEl.__x.$data.estimasi = durasiHari * alpineEl.__x.$data.tarif;
    }
}

/**
 * 3. INSTANSIASI & KONFIGURASI FLATPICKR RADIKAL
 */
const tanggalTerblokir = @json($bookingTerjadwal ?? []);

const configFlatpickr = {
    enableTime: true,
    time_24hr: true,
    allowInput: true,
    minDate: "today", 
    dateFormat: "d-m-Y H:i",
    
    // ❌ HAPUS opsi 'disable: tanggalTerblokir' bawaan Flatpickr di sini 
    // karena Flatpickr sering salah mengira tanggal 20 sebagai bagian dari hari libur/lock.
    
    onChange: function(selectedDates, dateStr, instance) {
        const input = instance.element;
        input.value = dateStr;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        
        if (input.id === 'tanggal_mulai') {
            fpSelesai.set('minDate', dateStr);
        }
        updateAlpineState();
    },
    onClose: function(selectedDates, dateStr, instance) {
        const input = instance.element;
        input.value = dateStr;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        updateAlpineState();
    }
};

const nilaiSaran = document.getElementById('tanggal_mulai').value;

// 1. Inisialisasi Tanggal Mulai dengan fungsi pengecekan manual penuh
const fpMulai = flatpickr("#tanggal_mulai", {
    ...configFlatpickr,
    defaultDate: nilaiSaran ? nilaiSaran : null,
    onDayCreate: function(dObj, dStr, fp, dayElem) {
        kunciJadwalTabrakan(fp, dayElem);
    }
});

// 2. Inisialisasi Tanggal Selesai dengan fungsi pengecekan manual penuh
const fpSelesai = flatpickr("#tanggal_selesai", {
    ...configFlatpickr,
    onDayCreate: function(dObj, dStr, fp, dayElem) {
        kunciJadwalTabrakan(fp, dayElem);
    }
});

/**
 * FUNGSI UTAMA UNTUK MEMAKSA LOCK HANYA TANGGAL 16-18
 */
/**
 * FUNGSI UTAMA UNTUK MEMAKSA LOCK JADWAL TABRAKAN & TANGGAL MUNDUR
 */
function kunciJadwalTabrakan(fp, dayElem) {
    // Ambil string tanggal lokal dari kalender (Format: YYYY-MM-DD)
    const y = dayElem.dateObj.getFullYear();
    const m = String(dayElem.dateObj.getMonth() + 1).padStart(2, '0');
    const d = String(dayElem.dateObj.getDate()).padStart(2, '0');
    const tglKalenderStr = `${y}-${m}-${d}`;

    // 1. Ambil tanggal mulai yang sedang dipilih user saat ini (jika ada)
    const inputMulai = document.getElementById('tanggal_mulai').value;
    let tglMulaiTerpilihStr = "";
    if (inputMulai) {
        // Ambil bagian tanggalnya saja 'DD-MM-YYYY' lalu ubah ke 'YYYY-MM-DD'
        const [datePart] = inputMulai.split(' ');
        const [day, month, year] = datePart.split('-');
        tglMulaiTerpilihStr = `${year}-${month}-${day}`;
    }

    // 2. Cek apakah tanggal di kalender menabrak database sewa 16-18 Juni
    const isTabrakan = tanggalTerblokir.some(range => {
        const mulaiStr = range.from.split(' ')[0];   // '2026-06-16'
        const selesaiStr = range.to.split(' ')[0];  // '2026-06-18'
        return tglKalenderStr >= mulaiStr && tglKalenderStr <= selesaiStr;
    });

    // 3. Cek apakah tanggal di kalender bernilai MUNDUR (khusus untuk input tanggal selesai)
    // Jika kita sedang merender kalender Selesai, dan user sudah pilih tanggal mulai, 
    // maka tanggal yang lebih kecil dari tanggal mulai wajib dikunci.
    const isMundur = (fp.element.id === 'tanggal_selesai' && tglMulaiTerpilihStr && tglKalenderStr < tglMulaiTerpilihStr);

    // KONDISI EKSEKUSI LOCK
    if (isTabrakan || isMundur) {
        // Kunci mati jika menabrak atau tanggalnya mundur ke belakang!
        dayElem.classList.add("flatpickr-disabled");
        dayElem.style.pointerEvents = "none";
        dayElem.style.opacity = "0.3";
    } else {
        // Jamin tetap terbuka untuk tanggal setelahnya yang aman (seperti tanggal 20)
        if (dayElem.dateObj >= fp.now) {
            dayElem.classList.remove("flatpickr-disabled");
            dayElem.style.pointerEvents = "auto";
            dayElem.style.opacity = "1";
        }
    }
}

// ... (Kondisi DOMContentLoaded nilaiSaran tetap sama di bawah) ...
</script>
@endpush