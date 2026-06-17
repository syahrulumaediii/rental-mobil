@extends('layouts.app')

@section('title', 'Katalog Kendaraan')
@section('page-title', 'Katalog Kendaraan')

@section('sidebar-nav')
    @include('components.sidebar-pelanggan')
@endsection

@section('content')

{{-- Load Library Flatpickr untuk Kebutuhan Asset Kalender --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- Filter Pencarian --}}
{{-- Filter Pencarian --}}
<div class="card p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-35">
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-input w-full">
                <option value="">Semua</option>
                @foreach(\App\Models\KategoriKendaraan::all() as $k)
                <option value="{{ $k->id }}" {{ request('kategori_id')==$k->id?'selected':'' }}>{{ $k->nama }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="flex-1 min-w-30">
            <label class="form-label">Transmisi</label>
            <select name="transmisi" class="form-input w-full">
                <option value="">Semua</option>
                <option value="manual" {{ request('transmisi')==='manual'?'selected':'' }}>Manual</option>
                <option value="otomatis" {{ request('transmisi')==='otomatis'?'selected':'' }}>Otomatis</option>
            </select>
        </div>

        <div class="flex-1 min-w-35">
            <label class="form-label">Maks. Tarif (Rp)</label>
            <input type="number" name="max_tarif" value="{{ request('max_tarif') }}" class="form-input w-full" placeholder="500.000" step="50000">
        </div>

        <div class="flex-1 min-w-45">
            <label class="form-label">Tgl Mulai</label>
            <input type="text" name="tgl_mulai" value="{{ request('tgl_mulai') }}" class="form-input w-full input-date-custom" placeholder="Pilih Tanggal">
        </div>

        <div class="flex-1 min-w-45">
            <label class="form-label">Tgl Selesai</label>
            <input type="text" name="tgl_selesai" value="{{ request('tgl_selesai') }}" class="form-input w-full input-date-custom" placeholder="Pilih Tanggal">
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="btn-primary">Filter</button>
            @if(request()->anyFilled(['kategori_id','transmisi','max_tarif','tgl_mulai','tgl_selesai']))
            <a href="{{ route('pelanggan.katalog') }}" class="btn-secondary text-slate-500">Reset</a>
            @endif
        </div>
    </form>
</div>

{{-- Grid Kendaraan --}}
<div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5" x-data="{ openModal: false, selectedCar: {} }">
    @forelse($kendaraan as $k)
    @php
        // 1. HAPUS variabel $bookingAktif karena sudah di-filter di Controller.
        
        // 2. Cari tanggal selesai sewa paling akhir dari booking MASA DEPAN 
        // untuk disarankan ke calon penyewa berikutnya.
        $bookingTerakhir = $k->bookings->sortByDesc('tanggal_selesai')->first();
        $saranTanggalMulai = $bookingTerakhir ? \Carbon\Carbon::parse($bookingTerakhir->tanggal_selesai)->addHour()->format('d-m-Y H:i') : '';
    @endphp

    <div class="card overflow-hidden group hover:shadow-md transition-shadow flex flex-col justify-between">
        
        {{-- Area Foto Kendaraan --}}
        <div class="relative h-44 bg-slate-100 overflow-hidden cursor-pointer"
            @click="
                openModal = true; 
                selectedCar = {
                    id: '{{ $k->id }}',
                    nama: '{{ $k->nama }}',
                    merk: '{{ $k->merk }}',
                    model: '{{ $k->model }}',
                    tahun: '{{ $k->tahun }}',
                    plat: '{{ $k->plat_nomor }}',
                    warna: '{{ $k->warna }}',
                    bbm: '{{ $k->bahan_bakar }}',
                    transmisi: '{{ $k->transmisi }}',
                    kapasitas: '{{ $k->kapasitas }}',
                    tarif: '{{ number_format($k->tarif_harian, 0, ',', '.') }}',
                    denda: '{{ number_format($k->denda_per_jam, 0, ',', '.') }}', 
                    foto: '{{ $k->foto ? Storage::url($k->foto) : '' }}',
                    kategori: '{{ $k->kategori->nama ?? '-' }}',
                    jadwal: [
                        @foreach($k->bookings as $b)
                        { mulai: '{{ \Carbon\Carbon::parse($b->tanggal_mulai)->format('d M Y H:i') }}', selesai: '{{ \Carbon\Carbon::parse($b->tanggal_selesai)->format('d M Y H:i') }}', status: '{{ $b->status }}' },
                        @endforeach
                    ]
                }
            ">

            @if($k->foto)
            <img src="{{ Storage::url($k->foto) }}" alt="{{ $k->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <i data-lucide="car" class="w-12 h-12 text-slate-300"></i>
                </div>
            @endif
            
            {{-- Badge Aktif (Sekarang terkunci di dalam div relative di atas) --}}
            <div class="absolute top-3 left-3 z-10">
                <span class="inline-flex items-center gap-1 bg-emerald-500 text-white text-[11px] px-2 py-1 rounded-md shadow-sm font-bold uppercase tracking-wide">
                    <i data-lucide="check-circle" class="w-3 h-3"></i> Aktif
                </span>
            </div>

            <div class="absolute top-3 right-3 z-10">
                <span class="badge badge-blue text-[11px]">{{ $k->kategori->nama ?? '-' }}</span>
            </div>
        </div>
        
        {{-- Ringkasan Info Kartu Kendaraan --}}
        <div class="p-4 flex flex-col flex-1 justify-between">
            <div>
                <h3 class="font-bold text-slate-800 truncate">{{ $k->nama }}</h3>
                <p class="text-xs text-slate-400 mb-2">{{ $k->merk }} {{ $k->model }} · {{ $k->tahun }}</p>

                {{-- 🌟 LIST INI SEKARANG HANYA MENAMPILKAN JADWAL SEWA YANG AKAN DATANG (FUTURE BOOKINGS) --}}
                @if($k->bookings->count() > 0)
                <div class="mb-3 bg-rose-50 border border-rose-100 p-2 rounded-xl text-[11px]">
                    <p class="text-rose-700 font-bold mb-1 flex items-center gap-1">
                        <i data-lucide="calendar-range" class="w-3 h-3"></i> Jadwal Booking Terisi (Akan Datang):
                    </p>
                    <ul class="text-rose-600 list-disc list-inside space-y-0.5">
                        @foreach($k->bookings->take(2) as $b)
                            <li>{{ \Carbon\Carbon::parse($b->tanggal_mulai)->format('d/m (H:i)') }} s/d {{ \Carbon\Carbon::parse($b->tanggal_selesai)->format('d/m (H:i)') }}</li>
                        @endforeach
                        @if($k->bookings->count() > 2)
                            <li class="list-none font-medium text-slate-400 mt-0.5">+{{ $k->bookings->count() - 2 }} jadwal lainnya...</li>
                        @endif
                    </ul>
                </div>
                @endif

                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach([[$k->kapasitas.' Orang','users'],[$k->transmisi,'settings'],[$k->bahan_bakar,'fuel']] as [$val,$ic])
                    <span class="flex items-center gap-1 text-xs text-slate-500 bg-slate-50 px-2 py-1 rounded-lg">
                        <i data-lucide="{{ $ic }}" class="w-3 h-3"></i> {{ ucfirst($val) }}
                    </span>
                    @endforeach
                </div>
            </div>

            {{-- Bagian Tarif dan Tombol Pesan Langsung --}}
            <div class="flex items-end justify-between border-t border-slate-50 pt-3">
                <div>
                    <p class="text-xs text-slate-400">Tarif Sewa</p>
                    <p class="font-extrabold text-primary-700">Rp {{ number_format($k->tarif_harian, 0, ',', '.') }}</p>
                    <p class="text-[10px] text-slate-400">per hari</p>
                </div>
                
                <a href="{{ route('pelanggan.booking.create', [$k->id, 'tgl_mulai' => request('tgl_mulai'), 'tgl_selesai' => request('tgl_selesai')]) }}" class="btn-primary text-xs px-4 py-2 flex items-center gap-1">
                    <i data-lucide="calendar-plus" class="w-3 h-3"></i> Pesan
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full text-center py-16">
        <i data-lucide="car-off" class="w-12 h-12 text-slate-200 mx-auto mb-3"></i>
        <p class="text-slate-400">Tidak ada kendaraan tersedia saat ini</p>
    </div>
    @endforelse
    
    {{-- ... sisa kode modal Alpine.js ke bawah biarkan tetap sama ... --}}

    {{-- INTERAKTIF MODAL POP-UP --}}
    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-transition style="display: none;">
        <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-xl border border-slate-100 flex flex-col" @click.away="openModal = false">
            
            <div class="relative h-56 bg-slate-100 shrink-0">
                <template x-if="selectedCar.foto">
                    <img :src="selectedCar.foto" class="w-full h-full object-cover">
                </template>
                <template x-if="!selectedCar.foto">
                    <div class="w-full h-full flex items-center justify-center bg-slate-50">
                        <i data-lucide="car" class="w-16 h-16 text-slate-300"></i>
                    </div>
                </template>
                <button @click="openModal = false" class="absolute top-4 right-4 w-8 h-8 bg-black/50 text-white rounded-full flex items-center justify-center hover:bg-black/70 backdrop-blur-xs transition-colors">✕</button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(100vh-16rem)]">
                <span class="badge badge-blue text-xs mb-1" x-text="selectedCar.kategori"></span>
                <h3 class="text-xl font-black text-slate-800 tracking-tight" x-text="selectedCar.nama"></h3>
                <p class="text-sm text-slate-400 mb-4" x-text="selectedCar.merk + ' ' + selectedCar.model + ' · Th ' + selectedCar.tahun"></p>
                
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Spesifikasi Kendaraan</h4>
                <div class="grid grid-cols-2 gap-3 mb-4 text-sm bg-slate-50 p-3.5 rounded-xl border border-slate-100">
                    <div>
                        <span class="text-slate-400 text-[11px] block">Nomor Plat</span> 
                        <p class="font-bold text-slate-700 mt-0.5" x-text="selectedCar.plat ?? '-'"></p>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Warna Mobil</span> 
                        <p class="font-semibold text-slate-700 mt-0.5" x-text="selectedCar.warna ?? '-'"></p>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Sistem Transmisi</span> 
                        <p class="font-semibold text-slate-700 mt-0.5 capitalize" x-text="selectedCar.transmisi"></p>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Bahan Bakar</span> 
                        <p class="font-semibold text-slate-700 mt-0.5 capitalize" x-text="selectedCar.bbm"></p>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Kapasitas Maksimal</span> 
                        <p class="font-semibold text-slate-700 mt-0.5" x-text="selectedCar.kapasitas + ' Penumpang'"></p>
                    </div>
                </div>

                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Regulasi Keterlambatan Pengembalian</h4>
                <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-xl p-3 mb-4 text-xs text-amber-800">
                    <div class="w-7 h-7 bg-amber-500 rounded-lg flex items-center justify-center text-white shrink-0">
                        <i data-lucide="clock-alert" class="w-4 h-4"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Dikenakan denda sebesar <strong class="text-amber-900">Rp <span x-text="selectedCar.denda"></span> / Jam</strong> jika mengembalikan mobil melebihi batas waktu sewa.</p>
                    </div>
                </div>

                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Semua Jadwal Sewa Mendatang</h4>
                <div class="max-h-28 overflow-y-auto space-y-1.5 mb-2 border-b pb-4 pr-1">
                    <template x-if="selectedCar.jadwal && selectedCar.jadwal.length === 0">
                        <p class="text-xs text-slate-400 italic bg-slate-50 p-3 rounded-lg text-center">Belum ada jadwal booking. Mobil bebas dipesan kapan saja!</p>
                    </template>
                    <template x-for="j in selectedCar.jadwal">
                        <div class="flex justify-between items-center bg-rose-50 text-rose-700 px-3 py-2 rounded-xl text-xs border border-rose-100">
                            <span class="font-semibold" x-text="j.mulai + ' s/d ' + j.selesai"></span>
                            <span class="text-[10px] uppercase font-bold bg-rose-200 px-1.5 py-0.5 rounded-md shadow-2xs" x-text="j.status"></span>
                        </div>
                    </template>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0">
                <div>
                    <span class="text-[11px] text-slate-400 block font-medium">Biaya Sewa</span>
                    <p class="text-lg font-black text-primary-700 leading-none">Rp <span x-text="selectedCar.tarif"></span><span class="text-xs font-normal text-slate-400">/hari</span></p>
                </div>
                <button @click="openModal = false; window.location.href='{{ url('pelanggan/booking/buat') }}/' + selectedCar.id" class="btn-primary text-sm px-6 py-2.5 rounded-xl font-bold flex items-center gap-1.5 shadow-sm">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i> Pesan Sekarang
                </button>
            </div>
            
        </div>
    </div>

</div>

@if($kendaraan->hasPages())
<div class="mt-6">{{ $kendaraan->withQueryString()->links() }}</div>
@endif


<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr(".input-date-custom", {
            enableTime: true,
            dateFormat: "Y-m-d H:i", // Format yang dikirim ke Server (Database)
            altInput: true,         // Menampilkan format manusiawi
            altFormat: "d F Y, H:i", // Format yang dilihat user: 15 Juni 2026, 03:51
            time_24hr: true,
            locale: {
                firstDayOfWeek: 1,
                weekdays: {
                    shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                },
                months: {
                    shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                },
            },
        });
    });
</script>
@endsection