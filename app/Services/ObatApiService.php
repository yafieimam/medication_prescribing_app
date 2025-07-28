<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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

    public function getMedicines(): array
    {
        return Cache::remember('obatapi_medicines', now()->addMinutes(10), function () {
            try {
                $response = Http::withToken($this->token)
                    ->get(config('obatapi.url') . '/medicines');

                Log::info('Fetching medicines list.');

                return $response->json() ?? [];
            } catch (\Throwable $e) {
                Log::error('Failed to fetch medicines list.', ['error' => $e->getMessage()]);
                return [];
            }
        });
    }

    public function searchMedicines(string $keyword = ''): array
    {
        try {
            $data = $this->getMedicines();

            if ($keyword !== '') {
                $keywordLower = strtolower($keyword);

                $data = array_filter($data['medicines'], function ($item) use ($keywordLower) {
                    return str_contains(strtolower($item['name']), $keywordLower);
                });

                return array_values($data);
            }

            return array_values($data['medicines']);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch medicines list.', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    public function getMedicinePrice(string $medicineId, string $tanggal): ?float
    {
        try {
            $response = Http::withToken($this->token)
                ->get(config('obatapi.url') . "/medicines/{$medicineId}/prices");

            Log::info("Fetching price for medicine ID {$medicineId} tanggal {$tanggal}.");

            $prices = $response->json();

            $fallbackPrice = null;
            $latestStartDate = null;

            foreach ($prices['prices'] as $price) {
                $start = $price['start_date']['value'] ?? null;
                $end = $price['end_date']['value'] ?? null;

                if ($start && $end) {
                    if ($tanggal >= $start && $tanggal <= $end) {
                        return (float) $price['unit_price'];
                    }

                    if (is_null($latestStartDate) || $start > $latestStartDate) {
                        $latestStartDate = $start;
                        $fallbackPrice = (float) $price['unit_price'];
                    }
                }
            }

            return $fallbackPrice;
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
