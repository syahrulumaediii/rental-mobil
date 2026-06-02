<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetodePembayaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MetodePembayaranController extends Controller
{
    public function index()
    {
        $metode = MetodePembayaran::withCount('pembayaran')->latest()->paginate(15);

        return view('admin.metode-pembayaran.index', compact('metode'));
    }

    public function create()
    {
        return view('admin.metode-pembayaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'  => 'required|string|unique:metode_pembayaran,nama|max:100',
            'tipe'  => ['required', Rule::in(['tunai', 'transfer', 'e-wallet', 'kartu'])],
            'is_active' => 'boolean',
        ]);

        MetodePembayaran::create([
            'nama'      => $request->nama,
            'tipe'      => $request->tipe,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.metode-pembayaran.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function edit(MetodePembayaran $metodePembayaran)
    {
        return view('admin.metode-pembayaran.edit', compact('metodePembayaran'));
    }

    public function update(Request $request, MetodePembayaran $metodePembayaran)
    {
        $request->validate([
            'nama'  => ['required', 'string', 'max:100', Rule::unique('metode_pembayaran')->ignore($metodePembayaran->id)],
            'tipe'  => ['required', Rule::in(['tunai', 'transfer', 'e-wallet', 'kartu'])],
            'is_active' => 'boolean',
        ]);

        $metodePembayaran->update([
            'nama'      => $request->nama,
            'tipe'      => $request->tipe,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.metode-pembayaran.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function destroy(MetodePembayaran $metodePembayaran)
    {
        if ($metodePembayaran->pembayaran()->exists()) {
            return back()->with('error', 'Metode pembayaran tidak dapat dihapus karena sudah digunakan dalam transaksi.');
        }

        $metodePembayaran->delete();

        return redirect()->route('admin.metode-pembayaran.index')
            ->with('success', 'Metode pembayaran berhasil dihapus.');
    }
}
