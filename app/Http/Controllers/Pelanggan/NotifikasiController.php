<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;



class NotifikasiController extends Controller
{

    public function index()
    {

        $notifikasi = Auth::user()
            ->notifikasi()
            ->latest()
            ->paginate(20);

        return view('pelanggan.notifikasi.index', compact('notifikasi'));
    }

    public function markRead(Notifikasi $notifikasi)
    {
        if ($notifikasi->user_id !== Auth::id()) {
            abort(403);
        }

        $notifikasi->update(['read_at' => true]);

        if ($notifikasi->url) {
            return redirect($notifikasi->url);
        }

        return back();
    }

    public function markAllRead()
    {
        Auth::user()->notifikasi()->unread()->update(['read_at' => true]);

        return back()->with('success', 'Semua notifikasi ditandai telah dibaca.');
    }
}
