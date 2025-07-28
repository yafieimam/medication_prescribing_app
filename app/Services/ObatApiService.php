<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ObatApiService
{
    protected $token;

    public function __construct()
    {
        $this->authenticate();
    }

    protected function authenticate()
    {
        $response = Http::post(config('obatapi.url') . '/auth', [
            'email' => config('obatapi.email'),
            'password' => config('obatapi.phone'),
        ]);

        if ($response->successful()) {
            $this->token = $response['access_token'];
            Log::info('API Token retrieved successfully.');
        } else {
            Log::error('API authentication failed.', ['response' => $response->body()]);
            throw new \Exception('Failed to authenticate with external API.');
        }
    }

    public function getMedicines()
    {
        try {
            $response = Http::withToken($this->token)
                ->get(config('obatapi.url') . '/medicines');

            Log::info('Fetching medicines list.');

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Failed to fetch medicines list.', [
                'message' => $e->getMessage(),
            ]);
            
            return json_encode($e->getMessage());
        }
    }

    public function getMedicinePrice(string $medicineId, string $tanggal): ?float
    {
        try {
            $response = Http::withToken($this->token)
                ->get(config('obatapi.url') . "/medicines/{$medicineId}/prices");

            Log::info("Fetching price for medicine ID {$medicineId} tanggal {$tanggal}.");

            $prices = $response->json();

            foreach ($prices as $price) {
                if ($tanggal >= $price['start_date'] && $tanggal <= $price['end_date']) {
                    return (float) $price['price'];
                }
            }

            return (float) 0;
        } catch (\Throwable $e) {
            Log::error('Failed to fetch price', [
                'medicine_id' => $medicineId,
                'tanggal' => $tanggal,
                'message' => $e->getMessage(),
            ]);

            return (float) 0;
        }
    }
}
