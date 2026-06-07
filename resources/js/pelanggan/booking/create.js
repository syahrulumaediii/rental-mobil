 function bookingForm() {
    return {
        mulai: @json(old('tanggal_mulai', '')),
        selesai: @json(old('tanggal_selesai', '')),
        durasi: 0,
        estimasi: 0,
        tarif: {{ $kendaraan->tarif_harian }},
        hitungEstimasi() {
            if (this.mulai && this.selesai) {
                const d1 = new Date(this.mulai);
                const d2 = new Date(this.selesai);
                this.durasi = Math.max(0, Math.round((d2 - d1) / (1000*60*60*24)));
                this.estimasi = this.durasi * this.tarif;
            }
        }
    }
}