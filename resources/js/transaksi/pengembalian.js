// resources/js/transaksi/pengembalian.js

document.addEventListener('DOMContentLoaded', function () {
    const elData = document.getElementById('pengembalian-data');
    if (!elData) return; 

    // Ambil variabel mentah dari HTML Atribut Data
    const oldDendas = JSON.parse(elData.dataset.oldDendas || '[]');
    const hariTelat = Number(elData.dataset.hariTelat || 0);
    const isTelat = elData.dataset.telat === 'true';
    const jumlahDeposit = Number(elData.dataset.jumlahDeposit || 0);

    // Ambil Element Kontrol DOM Utama
    const container = document.getElementById('denda-container');
    const btnTambah = document.getElementById('btn-tambah-denda');
    const infoKosong = document.getElementById('denda-kosong-info');
    const subtotalBox = document.getElementById('subtotal-denda-box');

    // Node Output Rekapitulasi Informasi
    const txtTotalDenda = document.getElementById('text-total-denda');
    const txtDepositTerpotong = document.getElementById('text-deposit-terpotong');
    const txtDepositKembali = document.getElementById('text-deposit-kembali');
    
    const ringkasanDenda = document.getElementById('ringkasan-total-denda');
    const ringkasanPotongan = document.getElementById('ringkasan-potongan-deposit');
    const labelStatusBayar = document.getElementById('label-status-bayar');
    const txtJumlahHarusBayar = document.getElementById('text-jumlah-harus-bayar');
    
    const boxPembayaranDenda = document.getElementById('box-pembayaran-denda');
    const selectMetode = document.getElementById('select-metode-pembayaran');
    const inputJumlahBayar = document.getElementById('input-jumlah-bayar');
    const boxInfoImpas = document.getElementById('box-info-impas');
    const textInfoImpas = document.getElementById('text-info-impas');
    const hiddenJumlahBayar = document.getElementById('hidden-jumlah-bayar');

    let counter = 0;

    function formatRupiah(angka) {
        return 'Rp ' + Math.abs(Number(angka)).toLocaleString('id-ID');
    }

    // --- UTILITY UTAMA HITUNG REAL-TIME ---
    function updateRingkasan() {
        let totalDenda = 0;
        const rows = container.querySelectorAll('.denda-row');

        rows.forEach(row => {
            const inputTotal = row.querySelector('.input-total-denda');
            totalDenda += Number(inputTotal.value) || 0;
        });

        // Toggle info visual baris denda kosong
        if (rows.length > 0) {
            infoKosong.style.display = 'none';
            subtotalBox.style.display = 'flex';
        } else {
            infoKosong.style.display = 'block';
            subtotalBox.style.display = 'none';
        }

        // LOGIKA UTAMA: Otomatisasi pemotongan deposit sebatas nilai denda
        const potonganDepositOtomatis = Math.min(totalDenda, jumlahDeposit);
        const sisaDepositDikembalikan = Math.max(0, jumlahDeposit - potonganDepositOtomatis);
        const jumlahHarusBayar = totalDenda - potonganDepositOtomatis;

        // Render teks di Card Info Pengembalian Deposit
        if (txtDepositTerpotong) txtDepositTerpotong.textContent = formatRupiah(potonganDepositOtomatis);
        if (txtDepositKembali) txtDepositKembali.textContent = formatRupiah(sisaDepositDikembalikan);

        // Render teks di Card Ringkasan Invoice Keuangan
        txtTotalDenda.textContent = formatRupiah(totalDenda);
        ringkasanDenda.textContent = '+ ' + formatRupiah(totalDenda);
        ringkasanPotongan.textContent = '− ' + formatRupiah(potonganDepositOtomatis);

        if (jumlahHarusBayar > 0) {
            labelStatusBayar.textContent = 'Sisa Kurang Bayar (Wajib Tunai/Transfer)';
            txtJumlahHarusBayar.textContent = formatRupiah(jumlahHarusBayar);
            txtJumlahHarusBayar.className = 'font-bold text-lg text-red-600';

            // Munculkan formulir input pelunasan kasir
            boxPembayaranDenda.classList.remove('hidden');
            boxInfoImpas.classList.add('hidden');
            if (selectMetode) selectMetode.required = true;
            if (inputJumlahBayar) {
                inputJumlahBayar.value = jumlahHarusBayar;
                inputJumlahBayar.required = true;
            }
            if (hiddenJumlahBayar) hiddenJumlahBayar.disabled = true;
        } else {
            // Kondisi Impas / Malah ada dana sisa kembalian deposit untuk pelanggan
            labelStatusBayar.textContent = 'Status Tagihan Tambahan';
            txtJumlahHarusBayar.textContent = formatRupiah(0);
            txtJumlahHarusBayar.className = 'font-bold text-lg text-emerald-700';

            boxPembayaranDenda.classList.add('hidden');
            boxInfoImpas.classList.remove('hidden');
            if (selectMetode) selectMetode.required = false;
            if (inputJumlahBayar) {
                inputJumlahBayar.required = false;
                inputJumlahBayar.value = 0;
            }
            if (hiddenJumlahBayar) {
                hiddenJumlahBayar.disabled = false;
                hiddenJumlahBayar.value = 0;
            }

            if (totalDenda === 0) {
                textInfoImpas.innerHTML = `Tidak ada tagihan tambahan. Uang jaminan deposit dikembalikan penuh sebesar <strong>${formatRupiah(jumlahDeposit)}</strong>.`;
            } else {
                textInfoImpas.innerHTML = `Denda tercover jaminan. Kembalikan sisa saldo deposit sebesar <strong>${formatRupiah(sisaDepositDikembalikan)}</strong> ke pelanggan.`;
            }
        }
    }

    // --- FUNGSIONALITAS BARIS INPUT DENDA DINAMIS ---
    function tambahBarisDenda(jenis = '', hari = 0, tarif = 0, total = 0, ket = '') {
        const idx = counter++;
        const row = document.createElement('div');
        row.className = 'border border-slate-200 rounded-xl p-4 bg-slate-50 relative denda-row';
        row.setAttribute('data-index', idx);

        row.innerHTML = `
            <button type="button" class="btn-hapus-denda absolute top-3 right-3 text-slate-400 hover:text-red-500 transition-colors" title="Hapus denda ini">
                <i data-lucide="x" class="w-4 h-4 pointer-events-none"></i>
            </button>

            <p class="text-xs font-semibold text-slate-500 mb-3">Denda #${container.children.length + 1}</p>

            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2 sm:col-span-1">
                    <label class="form-label">Jenis Denda</label>
                    <select name="dendas[${idx}][jenis_denda]" class="form-input select-jenis-denda" required>
                        <option value="">-- Pilih --</option>
                        <option value="keterlambatan" ${jenis === 'keterlambatan' ? 'selected' : ''}>Keterlambatan</option>
                        <option value="kerusakan" ${jenis === 'kerusakan' ? 'selected' : ''}>Kerusakan</option>
                        <option value="kehilangan" ${jenis === 'kehilangan' ? 'selected' : ''}>Kehilangan</option>
                        <option value="bahan_bakar" ${jenis === 'bahan_bakar' ? 'selected' : ''}>Kurang Bahan Bakar</option>
                        <option value="lainnya" ${jenis === 'lainnya' ? 'selected' : ''}>Lainnya</option>
                    </select>
                </div>

                <div class="box-pengali hidden">
                    <label class="form-label label-pengali">Jumlah</label>
                    <input type="number" name="dendas[${idx}][jumlah_hari_telat]" class="form-input input-pengali" value="${hari}" min="0" placeholder="0">
                </div>

                <div class="box-tarif hidden">
                    <label class="form-label label-tarif">Tarif Denda</label>
                    <input type="number" name="dendas[${idx}][tarif_denda]" class="form-input input-tarif-denda" value="${tarif}" min="0" step="1000" placeholder="Rp">
                </div>

                <div class="box-total col-span-2 sm:col-span-1">
                    <label class="form-label">Total Denda (Rp)</label>
                    <input type="number" name="dendas[${idx}][total_denda]" class="form-input input-total-denda" value="${total}" min="0" step="1000" placeholder="Masukkan total denda">
                </div>

                <div class="col-span-2">
                    <label class="form-label">Keterangan / Catatan Denda</label>
                    <input type="text" name="dendas[${idx}][keterangan]" class="form-input input-keterangan" value="${ket}" placeholder="Contoh: Gores bemper belakang, telat, dsb...">
                </div>
            </div>
        `;

        container.appendChild(row);
        sesuaikanLayoutBaris(row, jenis);

        if (typeof lucide !== 'undefined') lucide.createIcons();
        updateRingkasan();
    }

    function sesuaikanLayoutBaris(row, jenis) {
        const boxPengali = row.querySelector('.box-pengali');
        const boxTarif = row.querySelector('.box-tarif');
        const boxTotal = row.querySelector('.box-total');
        const labelPengali = row.querySelector('.label-pengali');
        const labelTarif = row.querySelector('.label-tarif');
        const inputPengali = row.querySelector('.input-pengali');
        const inputTarif = row.querySelector('.input-tarif-denda');
        const inputTotal = row.querySelector('.input-total-denda');

        if (jenis === 'keterlambatan') {
            boxPengali.classList.remove('hidden');
            boxTarif.classList.remove('hidden');
            boxTotal.className = 'box-total col-span-2 sm:col-span-1';
            labelPengali.textContent = 'Jumlah Hari Terlambat';
            labelTarif.textContent = 'Tarif Denda (Rp/Hari)';
            inputPengali.placeholder = 'Contoh: ' + hariTelat;
            inputTotal.readOnly = true;
            inputTotal.classList.add('bg-slate-100', 'cursor-not-allowed');
        } else if (jenis === 'bahan_bakar') {
            boxPengali.classList.remove('hidden');
            boxTarif.classList.remove('hidden');
            boxTotal.className = 'box-total col-span-2 sm:col-span-1';
            labelPengali.textContent = 'Jumlah Kekurangan (Liter)';
            labelTarif.textContent = 'Tarif per Liter (Rp/Liter)';
            inputPengali.placeholder = '5';
            inputTotal.readOnly = true;
            inputTotal.classList.add('bg-slate-100', 'cursor-not-allowed');
        } else {
            boxPengali.classList.add('hidden');
            boxTarif.classList.add('hidden');
            boxTotal.className = jenis !== '' ? 'box-total col-span-2' : 'box-total col-span-2 sm:col-span-1';
            inputTotal.readOnly = false;
            inputTotal.classList.remove('bg-slate-100', 'cursor-not-allowed');
        }
    }

    // --- EVENT LISTENERS HANDLING ---
    btnTambah.addEventListener('click', () => tambahBarisDenda());

    container.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-hapus-denda')) {
            e.target.closest('.denda-row').remove();
            
            // Re-index label denda agar berurutan kembali
            container.querySelectorAll('.denda-row').forEach((row, i) => {
                row.querySelector('p').textContent = `Denda #${i + 1}`;
            });
            updateRingkasan();
        }
    });

    container.addEventListener('change', function (e) {
        if (e.target.classList.contains('select-jenis-denda')) {
            const row = e.target.closest('.denda-row');
            sesuaikanLayoutBaris(row, e.target.value);
            
            // Reset nominal saat jenis denda berganti
            row.querySelector('.input-pengali').value = '';
            row.querySelector('.input-tarif-denda').value = '';
            row.querySelector('.input-total-denda').value = 0;
            updateRingkasan();
        }
    });

    container.addEventListener('input', function (e) {
        if (e.target.classList.contains('input-pengali') || e.target.classList.contains('input-tarif-denda')) {
            const row = e.target.closest('.denda-row');
            const p = parseFloat(row.querySelector('.input-pengali').value) || 0;
            const t = parseFloat(row.querySelector('.input-tarif-denda').value) || 0;
            
            row.querySelector('.input-total-denda').value = p * t;
            updateRingkasan();
        }
        if (e.target.classList.contains('input-total-denda')) {
            updateRingkasan();
        }
    });

    // Validasi input old array laravel jika redirect validation error terjadi
    if (oldDendas.length > 0) {
        oldDendas.forEach(d => {
            tambahBarisDenda(d.jenis_denda, d.jumlah_hari_telat, d.tarif_denda, d.total_denda, d.keterangan);
        });
    } else if (isTelat) {
        // Auto create row denda keterlambatan jika mobil memang telat dari jadwal plan
        tambahBarisDenda('keterlambatan', hariTelat, 0, 0, 'Keterlambatan otomatis sistem');
    } else {
        updateRingkasan();
    }
});