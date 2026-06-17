document.addEventListener('DOMContentLoaded', function () {
    const elData = document.getElementById('pengembalian-data');
    if (!elData) return; 

    const oldDendas = JSON.parse(elData.dataset.oldDendas || '[]');
    const jamTelat = Number(elData.dataset.jamTelat || 0);
    const tarifDendaPerJam = Number(elData.dataset.tarifDendaJam || 0);
    const isTelat = elData.dataset.telat === 'true';
    const jumlahDeposit = Number(elData.dataset.jumlahDeposit || 0);

    const container = document.getElementById('denda-container');
    const btnTambah = document.getElementById('btn-tambah-denda');
    const infoKosong = document.getElementById('denda-kosong-info');
    const subtotalBox = document.getElementById('subtotal-denda-box');

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

    function updateRingkasan() {
        let totalDenda = 0;
        const rows = container.querySelectorAll('.denda-row');

        rows.forEach(row => {
            const inputTotal = row.querySelector('.input-total-denda');
            totalDenda += Number(inputTotal.value) || 0;
        });

        if (rows.length > 0) {
            infoKosong.style.display = 'none';
            subtotalBox.style.display = 'flex';
        } else {
            infoKosong.style.display = 'block';
            subtotalBox.style.display = 'none';
        }

        const potonganDepositOtomatis = Math.min(totalDenda, jumlahDeposit);
        const sisaDepositDikembalikan = Math.max(0, jumlahDeposit - potonganDepositOtomatis);
        const jumlahHarusBayar = totalDenda - potonganDepositOtomatis;

        if (txtDepositTerpotong) txtDepositTerpotong.textContent = formatRupiah(potonganDepositOtomatis);
        if (txtDepositKembali) txtDepositKembali.textContent = formatRupiah(sisaDepositDikembalikan);
        txtTotalDenda.textContent = formatRupiah(totalDenda);
        ringkasanDenda.textContent = '+ ' + formatRupiah(totalDenda);
        ringkasanPotongan.textContent = '− ' + formatRupiah(potonganDepositOtomatis);

        if (jumlahHarusBayar > 0) {
            labelStatusBayar.textContent = 'Sisa Kurang Bayar (Wajib Tunai/Transfer)';
            txtJumlahHarusBayar.textContent = formatRupiah(jumlahHarusBayar);
            txtJumlahHarusBayar.className = 'font-bold text-lg text-red-600';
            boxPembayaranDenda.classList.remove('hidden');
            boxInfoImpas.classList.add('hidden');
            if (selectMetode) selectMetode.required = true;
            if (inputJumlahBayar) {
                inputJumlahBayar.value = jumlahHarusBayar;
                inputJumlahBayar.required = true;
            }
            if (hiddenJumlahBayar) hiddenJumlahBayar.disabled = true;
        } else {
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

    function tambahBarisDenda(jenis = '', jam = 0, tarif = 0, total = 0, ket = '') {
        const idx = counter++;
        const row = document.createElement('div');
        row.className = 'border border-slate-200 rounded-xl p-4 bg-slate-50 relative denda-row';
        row.setAttribute('data-index', idx);

        row.innerHTML = `
            <button type="button" class="btn-hapus-denda absolute top-3 right-3 text-slate-400 hover:text-red-500 transition-colors">
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
                    <input type="number" name="dendas[${idx}][jumlah_jam_telat]" class="form-input input-pengali" value="${jam}" min="0">
                </div>
                <div class="box-tarif hidden">
                    <label class="form-label label-tarif">Tarif Denda</label>
                    <input type="number" name="dendas[${idx}][tarif_denda]" class="form-input input-tarif-denda" value="${tarif}" min="0" step="1000">
                </div>
                <div class="box-total col-span-2 sm:col-span-1">
                    <label class="form-label">Total Denda (Rp)</label>
                    <input type="number" name="dendas[${idx}][total_denda]" class="form-input input-total-denda" value="${total}" min="0" step="1000">
                </div>
                <div class="col-span-2">
                    <label class="form-label">Keterangan</label>
                    <input type="text" name="dendas[${idx}][keterangan]" class="form-input input-keterangan" value="${ket}">
                </div>
            </div>`;

        container.appendChild(row);
        sesuaikanLayoutBaris(row, jenis);
        if (typeof lucide !== 'undefined') lucide.createIcons();
        updateRingkasan();
    }

    function sesuaikanLayoutBaris(row, jenis) {
        const boxPengali = row.querySelector('.box-pengali');
        const boxTarif = row.querySelector('.box-tarif');
        const inputPengali = row.querySelector('.input-pengali');
        const inputTarif = row.querySelector('.input-tarif-denda');
        const inputTotal = row.querySelector('.input-total-denda');

        if (jenis === 'keterlambatan') {
            boxPengali.classList.remove('hidden');
            boxTarif.classList.remove('hidden');
            inputTarif.value = tarifDendaPerJam;
            const p = parseFloat(inputPengali.value) || 0;
            inputTotal.value = p * tarifDendaPerJam;
        } else if (jenis === 'bahan_bakar') {
            boxPengali.classList.remove('hidden');
            boxTarif.classList.remove('hidden');
        } else {
            boxPengali.classList.add('hidden');
            boxTarif.classList.add('hidden');
        }
    }

    btnTambah.addEventListener('click', () => tambahBarisDenda());

    container.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-hapus-denda')) {
            e.target.closest('.denda-row').remove();
            updateRingkasan();
        }
    });

    container.addEventListener('change', (e) => {
        if (e.target.classList.contains('select-jenis-denda')) {
            const row = e.target.closest('.denda-row');
            row.querySelector('.input-pengali').value = 0;
            row.querySelector('.input-tarif-denda').value = 0;
            row.querySelector('.input-total-denda').value = 0;
            sesuaikanLayoutBaris(row, e.target.value);
            updateRingkasan();
        }
    });

    container.addEventListener('input', (e) => {
        const row = e.target.closest('.denda-row');
        if (e.target.classList.contains('input-pengali') || e.target.classList.contains('input-tarif-denda')) {
            const p = Math.max(0, parseFloat(row.querySelector('.input-pengali').value) || 0);
            const t = Math.max(0, parseFloat(row.querySelector('.input-tarif-denda').value) || 0);
            row.querySelector('.input-total-denda').value = p * t;
        }
        updateRingkasan();
    });

    if (oldDendas.length > 0) {
        oldDendas.forEach(d => tambahBarisDenda(d.jenis_denda, d.jumlah_jam_telat, d.tarif_denda, d.total_denda, d.keterangan));
    } else if (isTelat) {
        tambahBarisDenda('keterlambatan', jamTelat, tarifDendaPerJam, jamTelat * tarifDendaPerJam, 'Keterlambatan otomatis sistem');
    }
});