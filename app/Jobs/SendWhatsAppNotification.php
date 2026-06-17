<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $message;

    // Terima data yang dibutuhkan
    public function __construct($user, $message)
    {
        $this->user = $user;
        $this->message = $message;
    }


    // public function handle()
    // {
    //     Http::withHeaders([
    //         'Authorization' => config('services.whatsapp.token'), // Ambil dari .env
    //     ])->post('https://api.fonnte.com/send', [
    //         'target'  => $this->user->no_hp,
    //         'message' => $this->message,
    //         'countryCode' => '62', // Optional: Fonnte sering butuh ini jika nomor tidak pakai 62
    //     ]);
    // }


    // public function handle()
    // {
    //     $response = Http::withHeaders([
    //         'Authorization' => config('services.whatsapp.token'), // Pastikan ini benar
    //     ])->post('https://api.fonnte.com/send', [
    //         'target'  => $this->user->no_hp,
    //         'message' => $this->message,
    //     ]);

    //     // Tambahkan ini untuk melihat apa yang sebenarnya dikembalikan oleh Fonnte
    //     \Illuminate\Support\Facades\Log::info("Respon Fonnte: " . $response->body());

    //     if ($response->failed()) {
    //         \Illuminate\Support\Facades\Log::error("Gagal kirim WA ke Fonnte: " . $response->body());
    //     }
    // }

    public function handle()
    {
        // GANTI SESUAI DATA ANDA
        $myToken = "31ub3L7GyJYxaDdZ1JM4wPCqWC71zJk9Pmv";
        $myTarget = "6281315797025"; // Gunakan nomor HP Anda sendiri yang ada WA-nya

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => $myToken,
        ])->post('https://api.fonnte.com/send', [
            'target'  => $myTarget,
            'message' => 'Tes kirim pesan dari Laravel',
        ]);


        // Simpan respon ke log supaya kita tahu kenapa gagal
        \Illuminate\Support\Facades\Log::info("DEBUG FONNTE: " . $response->body());
    }
}
