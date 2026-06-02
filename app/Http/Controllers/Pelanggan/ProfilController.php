<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name'           => 'required|string|max:255',
            'phone'          => 'required|string|max:20',
            'nik'            => [
                'required',
                'string',
                'size:16',
                Rule::unique('pelanggan', 'nik')->ignore($user->pelanggan?->id)
            ],
            'tempat_lahir'   => 'required|string|max:100',
            'tanggal_lahir'  => 'required|date|before:today',
            'jenis_kelamin'  => ['required', Rule::in(['laki-laki', 'perempuan'])],
            'alamat'         => 'required|string',
            'kota'           => 'required|string|max:100',
            'pekerjaan'      => 'nullable|string|max:100',
        ]);

        $user->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        $user->pelanggan()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'nik'            => $request->nik,
                'tempat_lahir'   => $request->tempat_lahir,
                'tanggal_lahir'  => $request->tanggal_lahir,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'alamat'         => $request->alamat,
                'kota'           => $request->kota,
                'pekerjaan'      => $request->pekerjaan,
            ]
        );

        return redirect()
            ->route('pelanggan.profil.show')
            ->with('success', 'Profil berhasil diperbarui.');
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
