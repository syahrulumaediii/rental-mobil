<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Kendaraan;
use App\Models\Pelanggan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class WalkInController extends Controller
{
    /**
     * Step 1: Form pencarian / input data pelanggan baru
     */
    public function step1()
    {
        return view('kasir.walkin.step1');
    }

    /**
     * AJAX: Cari pelanggan berdasarkan keyword (NIK / Telepon / Nama)
     */
    public function cariPelanggan(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|min:2|max:100',
        ]);

        $keyword = $request->keyword;

        $results = Pelanggan::with('user')
            ->where(function ($query) use ($keyword) {
                $query->where('nik', 'like', "%{$keyword}%")
                    ->orWhereHas('user', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                            ->orWhere('phone', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
            })
            ->limit(10)
            ->get()
            ->map(function ($p) {
                return [
                    'id'    => $p->id,
                    'nama'  => $p->user->name ?? '-',
                    'email' => $p->user->email ?? '-',
                    'phone' => $p->user->phone ?? '-',
                    'nik'   => $p->nik ?? '-',
                    'alamat' => $p->alamat ?? '-',
                    'is_blacklisted' => $p->isBlacklisted(),
                ];
            });

        return response()->json($results);
    }

    /**
     * Step 2: Simpan/tentukan pelanggan → tampilkan form booking kendaraan.
     * Menerima pelanggan_id (existing) atau data pelanggan baru.
     */
    public function step2(Request $request)
    {
        // Pelanggan existing dipilih
        if ($request->filled('pelanggan_id')) {
            $pelanggan = Pelanggan::with('user')->findOrFail($request->pelanggan_id);

            if ($pelanggan->isBlacklisted()) {
                return back()->with('error', 'Pelanggan ini ada dalam daftar blacklist. Tidak bisa dilanjutkan.');
            }
        } else {
            // Pelanggan baru — validasi data
            $request->validate([
                'nama'           => 'required|string|max:100',
                'email'          => 'required|email|max:100|unique:users,email',
                'phone'          => 'required|string|max:20',
                'nik'            => 'nullable|string|max:20|unique:pelanggan,nik',
                'jenis_kelamin'  => 'nullable|in:laki-laki,perempuan',
                'alamat'         => 'nullable|string|max:500',
                'kota'           => 'nullable|string|max:100',
                'tempat_lahir'   => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date|before:today|after:1900-01-01',
                'pekerjaan'      => 'nullable|string|max:100',
            ]);

            // Buat User + Pelanggan dalam satu transaksi
            $pelanggan = DB::transaction(function () use ($request) {
                $user = User::create([
                    'name'      => $request->nama,
                    'email'     => $request->email,
                    'phone'     => $request->phone,
                    'password'  => Hash::make('password123'), // Password default untuk walk-in
                    'role'      => 'pelanggan',
                    'is_active' => true,
                ]);

                $pelanggan = Pelanggan::create([
                    'user_id'            => $user->id,
                    'nik'                => $request->nik,
                    'jenis_kelamin'      => $request->jenis_kelamin,
                    'alamat'             => $request->alamat,
                    'kota'               => $request->kota,
                    'tempat_lahir'       => $request->tempat_lahir,
                    'tanggal_lahir'      => $request->tanggal_lahir,
                    'pekerjaan'          => $request->pekerjaan,
                    'status_verifikasi'  => 'verified',
                ]);

                return $pelanggan->load('user');
            });
        }

        // Ambil kendaraan yang tersedia
        $kendaraan = Kendaraan::with('kategori')
            ->where('status', 'tersedia')
            ->orderBy('nama')
            ->get();

        return view('kasir.walkin.step2', compact('pelanggan', 'kendaraan'));
    }

    /**
     * Store: Buat booking walk-in langsung disetujui → redirect ke serah terima
     */
    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id'    => 'required|exists:pelanggan,id',
            'kendaraan_id'    => 'required|exists:kendaraan,id',
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'catatan'         => 'nullable|string|max:500',
        ]);

        $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);
        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        if (! $kendaraan->isTersedia()) {
            return back()->with('error', 'Kendaraan tidak tersedia saat ini.')->withInput();
        }

        if ($pelanggan->isBlacklisted()) {
            return back()->with('error', 'Pelanggan ini ada dalam daftar blacklist.')->withInput();
        }

        // Hitung durasi dan estimasi biaya
        $mulai   = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);
        $durasi  = $mulai->diffInDays($selesai);
        $estimasi = $durasi * $kendaraan->tarif_harian;

        $booking = DB::transaction(function () use ($request, $pelanggan, $kendaraan, $durasi, $estimasi) {
            // Buat booking langsung status disetujui
            $booking = Booking::create([
                'kode_booking'    => 'WLK-' . strtoupper(Str::random(8)),
                'pelanggan_id'    => $pelanggan->id,
                'kendaraan_id'    => $kendaraan->id,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'durasi_hari'     => $durasi,
                'estimasi_biaya'  => $estimasi,
                'catatan'         => $request->catatan ?? 'Walk-in oleh kasir',

                // --- TAMBAHKAN DUA BARIS INI ---
                'sumber_booking'  => 'walkin',
                'dibuat_oleh'     => Auth::id(), // Mengambil ID kasir yang sedang login
                // -------------------------------

                'status'          => 'disetujui',
                'disetujui_oleh'  => Auth::id(),
                'disetujui_at'    => now(),
            ]);

            // Update status kendaraan
            $kendaraan->update(['status' => 'disewa']);

            return $booking;
        });

        // Redirect langsung ke form serah terima yang sudah ada
        return redirect()
            ->route('kasir.transaksi.serah-terima', $booking->id)
            ->with('success', 'Booking walk-in berhasil dibuat (' . $booking->kode_booking . '). Silakan lanjutkan proses serah terima kendaraan.');
    }
}
