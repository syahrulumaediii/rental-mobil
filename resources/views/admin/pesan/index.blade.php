@extends('layouts.app')

@section('title', 'Kirim Pesan Ke Pelanggan')
@section('page-title', 'Kirim Pesan Pelanggan')

@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-2 sm:px-0">
    <div class="card p-4 sm:p-6">
        <h3 class="text-sm sm:text-base font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
            <i data-lucide="send" class="w-4 h-4 text-blue-600"></i> Kirim Notifikasi & Pesan Sistem
        </h3>

        {{-- Daftarkan state baru di x-data untuk mendeteksi pelanggan Terpilih --}}
        <form action="{{ route('admin.pesan.store') }}" method="POST" class="space-y-4" x-data="{
            tipePesan: '',
            userId: '',
            getNamaPelanggan() {
                // Mencari elemen option yang sedang aktif/dipilih di dalam select name='user_id'
                const selectEl = document.querySelector('select[name=&quot;user_id&quot;]');
                if (!selectEl) return 'Pelanggan';
                
                const selectedOption = selectEl.options[selectEl.selectedIndex];
                // Mengambil isi atribut data-nama, jika kosong gunakan default 'Pelanggan'
                return selectedOption && selectedOption.getAttribute('data-nama') 
                    ? selectedOption.getAttribute('data-nama') 
                    : 'Pelanggan';
            },
            setTemplate() {
                // Ambil nama pelanggan secara dinamis
                const nama = this.getNamaPelanggan();

                if(this.tipePesan === 'dokumen') {
                    $refs.judul.value = '[DOKUMEN] Perbaikan Berkas Driver';
                    $refs.isi.value = `Halo ${nama}, mohon unggah kembali foto KTP/SIM Anda di menu Dokumen karena berkas sebelumnya buram atau tidak terbaca.`;
                } else if(this.tipePesan === 'denda') {
                    $refs.judul.value = '[DENDA] Pemberitahuan Denda Keterlambatan';
                    $refs.isi.value = `Halo ${nama}, Anda dikenakan denda keterlambatan pengembalian unit kendaraan. Silakan cek detail pengembalian pada riwayat sewa Anda.`;
                } else if(this.tipePesan === 'booking') {
                    $refs.judul.value = '[BOOKING] Status Reservasi Kendaraan';
                    $refs.isi.value = `Halo ${nama}, pengajuan booking kendaraan Anda telah ditinjau oleh tim kami. Silakan cek halaman detail booking Anda.`;
                } else if(this.tipePesan === 'pembayaran') {
                    $refs.judul.value = '[PEMBAYARAN] Konfirmasi Pembayaran Sewa';
                    $refs.isi.value = `Halo ${nama}, pembayaran DP / pelunasan Anda telah kami terima dan diverifikasi oleh sistem. Terima kasih.`;
                } else if(this.tipePesan === 'sistem') {
                    $refs.judul.value = '[SISTEM] Pemeliharaan Aplikasi';
                    $refs.isi.value = `Halo ${nama}, sistem aplikasi akan melakukan pemeliharaan rutin pada malam ini pukul 00:00 WIB selama 1 jam.`;
                }
            }
        }">
            @csrf

            {{-- Pilih Pelanggan --}}
            <div>
                <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Pilih Pelanggan Tujuan</label>
                {{-- Tambahkan x-model dan trigger @change agar jika ganti nama, isi template langsung menyesuaikan --}}
                <select name="user_id" x-model="userId" @change="setTemplate()" class="form-input w-full text-sm" required>
                    <option value="" data-nama="">-- Pilih Akun Pelanggan --</option>
                    @foreach($pelanggan as $p)
                        {{-- Kuncinya ada di atribut data-nama="{{ $p->name }}" dibawah ini --}}
                        <option value="{{ $p->id }}" data-nama="{{ $p->name }}">{{ $p->name }} ({{ $p->email }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Pilih Tipe Pesan --}}
            <div>
                <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Tipe Kategori Notifikasi</label>
                <select name="tipe" x-model="tipePesan" @change="setTemplate()" class="form-input w-full text-sm" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="booking">Booking / Reservasi</option>
                    <option value="denda">Denda / Pelanggaran</option>
                    <option value="dokumen">Dokumen / Berkas Persyaratan</option>
                    <option value="pembayaran">Pembayaran / Keuangan</option>
                    <option value="sistem">Sistem / Pengumuman</option>
                </select>
            </div>

            {{-- Judul Notifikasi --}}
            <div>
                <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Subjek / Judul Pesan</label>
                <input type="text" name="judul" x-ref="judul" class="form-input w-full text-sm" placeholder="Judul notifikasi..." required>
            </div>

            {{-- Isi Pesan --}}
            <div>
                <label class="form-label mb-1.5 block text-xs font-bold text-slate-500 uppercase tracking-wide">Isi Pesan Detail</label>
                <textarea name="isi" x-ref="isi" rows="5" class="form-input w-full text-sm py-2.5 leading-relaxed" placeholder="Tulis isi pesan secara detail di sini..." required></textarea>
            </div>

            {{-- Tombol Aksi Adaptif --}}
            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-3 mt-5 border-t border-slate-100">
                <a href="{{ route('admin.dashboard') }}" class="btn-secondary w-full sm:w-auto flex items-center justify-center text-sm font-semibold h-10 px-5">
                    Batal
                </a>
                <button type="submit" class="btn-primary w-full sm:w-auto flex items-center justify-center gap-2 text-sm font-semibold h-10 px-5">
                    <i data-lucide="navigation" class="w-4 h-4"></i> Kirim Pemberitahuan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection