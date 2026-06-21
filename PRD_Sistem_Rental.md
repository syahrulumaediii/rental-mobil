# Product Requirements Document: Sistem Transaksi Rental

## 1. Overview

Sistem ini bertujuan untuk mengotomatisasi alur penyewaan barang, mulai dari booking, pembayaran DP, serah terima barang, hingga pengembalian dengan sistem deposit dan perhitungan denda[cite: 1, 2].

## 2. Definisi Aktor

| Role          | Akses            | Tanggung Jawab                                                                                                          |
| :------------ | :--------------- | :---------------------------------------------------------------------------------------------------------------------- |
| **Pelanggan** | Web/App Frontend | Melakukan pemesanan (booking) dan pembayaran DP[cite: 1, 2].                                                            |
| **Kasir**     | Sistem Transaksi | Memproses serah terima, menerima pelunasan, mencatat deposit, dan memproses pengembalian[cite: 1, 2].                   |
| **Admin**     | Sistem Penuh     | Memiliki akses yang sama dengan Kasir, plus manajemen inventaris, laporan keuangan, dan konfigurasi sistem[cite: 1, 2]. |

---

## 3. Alur Kerja & Logika Transaksi

### A. Tahap Booking (Pelanggan)

Saat pelanggan melakukan pemesanan, sistem akan menghitung kewajiban pembayaran awal[cite: 1, 2].

- **Logika**:
    - `Total Biaya` = Harga Sewa per Hari × Durasi[cite: 1, 2].
    - `DP` (Down Payment) = 30% × `Total Biaya`[cite: 1, 2].
    - `Sisa Tagihan` = 70% × `Total Biaya`[cite: 1, 2].
- **Output**: Pelanggan mendapatkan invoice booking dan wajib melunasi DP untuk mengamankan barang[cite: 1, 2].

### B. Tahap Serah Terima (Kasir / Admin)

Dilakukan saat barang diambil oleh pelanggan[cite: 1, 2].

1.  **Verifikasi**: Cek status booking (sudah bayar DP atau belum)[cite: 1, 2].
2.  **Pelunasan**: Pelanggan membayar `Sisa Tagihan` (70%)[cite: 1, 2].
3.  **Deposit**: Kasir/Admin memasukkan nilai `Deposit` (uang jaminan) yang disetorkan pelanggan ke dalam sistem[cite: 1, 2].

- **Data Tersimpan**: Status barang menjadi "Dipinjam", nominal deposit tercatat di transaksi[cite: 1, 2].

### C. Booking Walk-in (Kasir / Admin)

Untuk pelanggan yang datang langsung tanpa booking online, Kasir/Admin dapat memproses pemesanan secara langsung di tempat[cite: 2].

1.  **Pengecekan Identitas**:
    - Kasir/Admin mencari data pelanggan berdasarkan nomor HP atau nama di database[cite: 2].
    - **Jika Pelanggan Sudah Ada**: Sistem menampilkan profil pelanggan dan lanjut ke proses pilih barang[cite: 2].
    - **Jika Pelanggan Belum Terdaftar**: Kasir/Admin melakukan registrasi cepat (Input: Nama, No HP, Alamat) dan sistem membuatkan akun baru[cite: 2].
2.  **Pembuatan Transaksi**:
    - Setelah teridentifikasi, Kasir/Admin membuat pesanan baru (input barang, durasi, total)[cite: 2].
    - Sistem mengarahkan ke pembayaran (DP 30% atau pelunasan)[cite: 2].
3.  **Integrasi**:
    - Transaksi walk-in masuk ke dashboard laporan yang sama dengan transaksi online[cite: 2].

### D. Logika Perhitungan Denda Keterlambatan (Otomatis)

Untuk efisiensi dan validasi data, sistem akan menghitung denda keterlambatan secara otomatis berdasarkan durasi keterlambatan.

- **Pengambilan Data**: Sistem secara otomatis menarik nilai `Denda_Per_Jam` dari tabel `Kendaraan` berdasarkan ID kendaraan yang disewa.
- **Logika Perhitungan**:
    - `Waktu Keterlambatan` = `Waktu Kembali Aktual` - `Waktu Kembali Estimasi`.
    - `Total Denda Keterlambatan` = `Durasi Keterlambatan (dalam jam)` × `Denda_Per_Jam` (dari tabel Kendaraan).
- **Integrasi Pengembalian**:
    - Nilai `Total Denda Keterlambatan` akan otomatis ditambahkan ke kolom `Denda Lain-lain` sebelum dihitung terhadap `Saldo Deposit`.

### E. Tahap Pengembalian (Kasir / Admin)

Dilakukan saat barang dikembalikan[cite: 1, 2].

1.  **Input Denda**: Kasir/Admin memasukkan nominal denda kerusakan fisik (jika ada). Denda keterlambatan dihitung otomatis sesuai sub-bab D[cite: 1, 2].
2.  **Perhitungan Akhir**:
    - `Saldo Deposit` = Deposit yang disetor[cite: 1, 2].
    - `Hasil Akhir` = `Saldo Deposit` - (`Denda Kerusakan` + `Denda Keterlambatan`)[cite: 1, 2].
3.  **Eksekusi**:
    - **Saldo Deposit > Denda**: Sisa deposit dikembalikan ke pelanggan[cite: 1, 2].
    - **Saldo Deposit < Denda**: Pelanggan membayar kekurangan[cite: 1, 2].
    - **Saldo Deposit == Denda**: Deposit hangus[cite: 1, 2].

---

## 4. Matriks Logika (Cheat Sheet untuk UI)

| Skenario                     | Input Sistem                | Hasil Perhitungan | Tindakan                  |
| :--------------------------- | :-------------------------- | :---------------- | :------------------------ |
| **Booking**                  | Total: Rp 100rb             | DP: Rp 30rb       | Bayar DP                  |
| **Serah Terima**             | Sisa: Rp 70rb, Dep: Rp 50rb | -                 | Bayar Pelunasan + Deposit |
| **Pengembalian (Terlambat)** | 2 Jam x Rp 10rb (Denda/Jam) | Denda: Rp 20rb    | Kurangi Deposit Rp 20rb   |
| **Pengembalian (Normal)**    | Denda: Rp 0                 | Deposit: Rp 50rb  | Kembalikan Rp 50rb        |

---

## 5. Fitur Kunci

1.  **Unified Transaction Interface**: Tampilan transaksi sama untuk Kasir dan Admin[cite: 1, 2].
2.  **Auto-Calculation**: Sistem otomatis menghitung 30% DP dan sisa tagihan[cite: 1, 2].
3.  **Dynamic Fine Calculation**: Denda per jam ditarik otomatis dari tabel kendaraan untuk memastikan validitas data[cite: 2].
4.  **Log Activity**: Admin dapat melihat catatan transaksi Kasir[cite: 1, 2].
5.  **Status Tracking**: Status barang dinamis (Tersedia -> Dibooking -> Dipinjam -> Dikembalikan)[cite: 1, 2].
