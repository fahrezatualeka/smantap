<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FonnteService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function sendMessage($phoneNumber, $message)
    {
        try {
            $response = $this->client->post(config('services.fonnte.url'), [
                'headers' => [
                    'Authorization' => config('services.fonnte.token'),
                ],
                'form_params' => [
                    'target' => $phoneNumber,
                    'message' => $message,
                    'countryCode' => '62',
                ],
            ]);
    
            $result = json_decode($response->getBody(), true);
    
            // Tangani jika API Fonnte memberikan respons gagal
            if (!$result['status']) {
                throw new \Exception('Fonnte API gagal: ' . ($result['message'] ?? 'Unknown error'));
            }
    
            return $result;
        } catch (\Exception $e) {
            // Tangkap error untuk ditampilkan
            dd([
                'error_message' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]);
        }
    }
}