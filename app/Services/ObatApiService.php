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
        $response = Http::withToken($this->token)
            ->get(config('obatapi.url') . '/medicines');

        Log::info('Fetching medicines list.');

        return $response->json();
    }

    public function getMedicinePrice($medicineId)
    {
        $response = Http::withToken($this->token)
            ->get(config('obatapi.url') . "/medicines/{$medicineId}/prices");

        Log::info("Fetching price for medicine ID {$medicineId}.");

        return $response->json();
    }
}
