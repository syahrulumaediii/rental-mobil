<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfilController extends Controller
{
    /** @var \App\Models\User $user */

    public function show()
    {
        /** @var \App\Models\User $user */
        $user      = Auth::user();
        $pelanggan = $user->pelanggan()->with('dokumen')->firstOrCreate(['user_id' => $user->id]);

        return view('pelanggan.profil.show', compact('user', 'pelanggan'));
    }

    public function edit()
    {
        /** @var \App\Models\User $user */
        $user      = Auth::user();
        $pelanggan = $user->pelanggan()->firstOrCreate(['user_id' => $user->id]);

        return view('pelanggan.profil.edit', compact('user', 'pelanggan'));
    }

    // public function update(Request $request)
    // {
    //     /** @var User $user */
    //     $user = Auth::user();

    //     $request->validate([
    //         'name'           => 'required|string|max:255',
    //         'phone'          => 'required|string|max:20',
    //         'nik'            => [
    //             'required',
    //             'string',
    //             'size:16',
    //             Rule::unique('pelanggan', 'nik')->ignore($user->pelanggan?->id)
    //         ],
    //         'tempat_lahir'   => 'required|string|max:100',
    //         'tanggal_lahir'  => 'required|date|before:today',
    //         'jenis_kelamin'  => ['required', Rule::in(['laki-laki', 'perempuan'])],
    //         'alamat'         => 'required|string',
    //         'kota'           => 'required|string|max:100',
    //         'pekerjaan'      => 'nullable|string|max:100',
    //     ]);

    //     $user->update([
    //         'name'  => $request->name,
    //         'phone' => $request->phone,
    //     ]);

    //     $user->pelanggan()->updateOrCreate(
    //         ['user_id' => $user->id],
    //         [
    //             'nik'            => $request->nik,
    //             'tempat_lahir'   => $request->tempat_lahir,
    //             'tanggal_lahir'  => $request->tanggal_lahir,
    //             'jenis_kelamin'  => $request->jenis_kelamin,
    //             'alamat'         => $request->alamat,
    //             'kota'           => $request->kota,
    //             'pekerjaan'      => $request->pekerjaan,
    //         ]
    //     );

    //     return redirect()
    //         ->route('pelanggan.profil.show')
    //         ->with('success', 'Profil berhasil diperbarui.');
    // }

    public function update(Request $request)
    {
        $user = Auth::user();
        $pelanggan = $user->pelanggan; // Mengambil relasi data pelanggan

        // 1. Validasi Inputan Berkas & Teks
        $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'required|string|max:20',
            'nik'           => 'required|string|size:16',
            'tempat_lahir'  => 'required|string|max:100',
            'tanggal_lahir' => 'required|date_format:d-m-Y',
            'jenis_kelamin' => 'required|in:laki-laki,perempuan',
            'kota'          => 'required|string|max:100',
            'pekerjaan'     => 'nullable|string|max:100',
            'alamat'        => 'required|string',
            'foto_profil'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Batas maksimal 2MB
        ]);

        // 2. Logika Unggah Foto Profil ke Storage
        $dataPelanggan = $request->only([
            'nik',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'kota',
            'pekerjaan',
            'alamat'
        ]);

        // 2. Siapkan data, konversi tanggal lahir di sini
        $dataPelanggan = $request->except(['tanggal_lahir', 'foto_profil']);

        // Konversi string d-m-Y ke format database Y-m-d
        $dataPelanggan['tanggal_lahir'] = Carbon::createFromFormat('d-m-Y', $request->tanggal_lahir)->format('Y-m-d');

        if ($request->hasFile('foto_profil')) {
            // Hapus berkas foto lama jika ada di dalam folder
            if ($pelanggan->foto_profil && Storage::disk('public')->exists($pelanggan->foto_profil)) {
                Storage::disk('public')->delete($pelanggan->foto_profil);
            }

            // Simpan foto baru ke folder: storage/app/public/profile/pelanggan
            $path = $request->file('foto_profil')->store('profile/pelanggan', 'public');
            $dataPelanggan['foto_profil'] = $path;
        }

        // 3. Simpan perubahan ke tabel users dan pelanggan
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        $pelanggan->update($dataPelanggan);

        return redirect()->route('pelanggan.profil.show')
            ->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function editPassword()
    {
        return view('pelanggan.profil.edit-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required|string',
            'password'      => 'required|string|min:8|confirmed',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors([
                'password_lama' => 'Password lama salah.'
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()
            ->route('pelanggan.profil.show')
            ->with('success', 'Password berhasil diperbarui.');
    }
}
