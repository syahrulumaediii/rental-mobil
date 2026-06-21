<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\KategoriKendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kendaraan::with('kategori');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', "%{$request->search}%")
                    ->orWhere('plat_nomor', 'like', "%{$request->search}%")
                    ->orWhere('merk', 'like', "%{$request->search}%");
            });
        }

        $kendaraan  = $query->latest()->paginate(15);
        $kategori   = KategoriKendaraan::all();

        return view('admin.kendaraan.index', compact('kendaraan', 'kategori'));
    }

    public function create()
    {
        $kategori = KategoriKendaraan::all();

        return view('admin.kendaraan.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori_id'  => 'required|exists:kategori_kendaraan,id',
            'nama'         => 'required|string|max:255',
            'merk'         => 'required|string|max:100',
            'model'        => 'required|string|max:100',
            'tahun'        => 'required|integer|min:1990|max:' . date('Y'),
            'plat_nomor'   => 'required|string|unique:kendaraan,plat_nomor|max:20',
            'warna'        => 'required|string|max:50',
            'kapasitas'    => 'required|integer|min:1',
            'transmisi'    => ['required', Rule::in(['manual', 'otomatis'])],
            'bahan_bakar'  => ['required', Rule::in(['bensin', 'solar', 'listrik', 'hybrid'])],
            'tarif_harian' => 'required|numeric|min:0',
            'status'       => ['required', Rule::in(['aktif', 'non-aktif', 'disewa', 'servis'])],
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'deskripsi'    => 'nullable|string',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('kendaraan', 'public');
        }

        Kendaraan::create($data);

        return redirect()->route('admin.kendaraan.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function show(Kendaraan $kendaraan)
    {
        $kendaraan->load(['kategori', 'bookings.pelanggan.user']);

        return view('admin.kendaraan.show', compact('kendaraan'));
    }

    public function edit(Kendaraan $kendaraan)
    {
        $kategori = KategoriKendaraan::all();

        return view('admin.kendaraan.edit', compact('kendaraan', 'kategori'));
    }

    public function update(Request $request, Kendaraan $kendaraan)
    {
        $request->validate([
            'kategori_id'  => 'required|exists:kategori_kendaraan,id',
            'nama'         => 'required|string|max:255',
            'merk'         => 'required|string|max:100',
            'model'        => 'required|string|max:100',
            'tahun'        => 'required|integer|min:1990|max:' . date('Y'),
            'plat_nomor'   => ['required', 'string', 'max:20', Rule::unique('kendaraan')->ignore($kendaraan->id)],
            'warna'        => 'required|string|max:50',
            'kapasitas'    => 'required|integer|min:1',
            'transmisi'    => ['required', Rule::in(['manual', 'otomatis'])],
            'bahan_bakar'  => ['required', Rule::in(['bensin', 'solar', 'listrik', 'hybrid'])],
            'tarif_harian' => 'required|numeric|min:0',
            'status'       => ['required', Rule::in(['aktif', 'non-aktif', 'disewa', 'servis'])],
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'deskripsi'    => 'nullable|string',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            if ($kendaraan->foto) {
                Storage::disk('public')->delete($kendaraan->foto);
            }
            $data['foto'] = $request->file('foto')->store('kendaraan', 'public');
        }

        $kendaraan->update($data);

        return redirect()->route('admin.kendaraan.index')
            ->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        if ($kendaraan->booking()->whereIn('status', ['pending', 'disetujui'])->exists()) {
            return back()->with('error', 'Kendaraan tidak dapat dihapus karena masih memiliki booking aktif.');
        }

        if ($kendaraan->foto) {
            Storage::disk('public')->delete($kendaraan->foto);
        }

        $kendaraan->delete();

        return redirect()->route('admin.kendaraan.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }
}
