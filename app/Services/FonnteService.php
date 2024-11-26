<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FonnteService
{
    protected $token;
    protected $url;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
        $this->url = config('services.fonnte.url');
    }

    public function sendMessage($to, $message)
    {
        try {
            $target = '+' . preg_replace('/\D/', '', $to); // memastikan hanya nomor yang digunakan
            \Log::info('Target Nomor Telepon:', ['target' => $target]);
    
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->post($this->url, [
                'target' => $target,
                'message' => $message,
                'url' => 'https://md.fonnte.com/images/wa-logo.png', 
                'schedule' => 0,
                'typing' => false,
                'delay' => '2',
                'countryCode' => '62',
            ]);
    
            // Log untuk melihat status dan body respons secara menyeluruh
            \Log::info('Fonnte API Response:', [
                'response_body' => $response->body(), // Seluruh body respons
                'response_status' => $response->status() // Status HTTP dari API
            ]);
    
            if ($response->successful()) {
                $responseBody = $response->json();
                \Log::info('Fonnte API JSON Response:', ['response' => $responseBody]);
    
                if (isset($responseBody['status']) && $responseBody['status'] === true) {
                    return [
                        'status' => 'success',
                        'message' => $responseBody['message'] ?? 'Pesan berhasil dikirim.',
                    ];
                }
    
                // Jika status response API adalah false, log error lebih detail
                \Log::error('Fonnte API Error Response:', [
                    'error_message' => $responseBody['message'] ?? 'Gagal mengirim pesan.'
                ]);
                return [
                    'status' => 'error',
                    'message' => $responseBody['message'] ?? 'Gagal mengirim pesan.',
                ];
            }
    
            // Jika respons API gagal (status code 4xx atau 5xx)
            \Log::error('Fonnte API Error Response:', [
                'error_response' => $response->body(),
                'status_code' => $response->status() // Status code API, misalnya 400 atau 500
            ]);
            return [
                'status' => 'error',
                'message' => 'Kesalahan saat menghubungi API Fonnte.',
                'response' => $response->body(),
                'status_code' => $response->status(),
            ];
    
        } catch (\Exception $e) {
            // Menangkap exception dan log error detailnya
            \Log::error('Fonnte API Exception:', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);
            return [
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengirim pesan.',
            ];
        }
    }
    
    
    

}