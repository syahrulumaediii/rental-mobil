<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriKendaraan;
use Illuminate\Http\Request;

class KategoriKendaraanController extends Controller
{
    public function index()
    {
        $kategori = KategoriKendaraan::withCount('kendaraan')->latest()->paginate(15);

        return view('admin.kategori-kendaraan.index', compact('kategori'));
    }

    public function create()
    {
        return view('admin.kategori-kendaraan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|unique:kategori_kendaraan,nama|max:100',
            'deskripsi' => 'nullable|string',
        ]);

        KategoriKendaraan::create($request->only(['nama', 'deskripsi']));

        return redirect()->route('admin.kategori-kendaraan.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(KategoriKendaraan $kategoriKendaraan)
    {
        return view('admin.kategori-kendaraan.edit', compact('kategoriKendaraan'));
    }

    public function update(Request $request, KategoriKendaraan $kategoriKendaraan)
    {
        $request->validate([
            'nama'      => 'required|string|max:100|unique:kategori_kendaraan,nama,' . $kategoriKendaraan->id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategoriKendaraan->update($request->only(['nama', 'deskripsi']));

        return redirect()->route('admin.kategori-kendaraan.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(KategoriKendaraan $kategoriKendaraan)
    {
        if ($kategoriKendaraan->kendaraan()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki kendaraan.');
        }

        $kategoriKendaraan->delete();

        return redirect()->route('admin.kategori-kendaraan.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
