<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BiteshipService
{
    private readonly string $apiKey;

    private readonly string $baseUrl;

    public function __construct()
    {
        $this->apiKey = (string) config('services.biteship.key');
        $this->baseUrl = rtrim((string) config('services.biteship.base_url'), '/');
    }

    /**
     * Fetch available couriers & shipping cost for the given destination and cart items.
     *
     * @param  array{latitude: float, longitude: float}  $destination
     * @param  array<int, array{name: string, value: float, quantity: int, weight: int}>  $items
     * @return array<int, array<string, mixed>> Biteship pricing options, empty when no courier covers the destination.
     *
     * @throws RuntimeException when the API key is missing or the request fails.
     */
    public function getRates(array $destination, array $items): array
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('Biteship API key is not configured.');
        }

        $origin = config('services.biteship.origin');

        $response = Http::withHeaders(['Authorization' => $this->apiKey])
            ->timeout(15)
            ->post("{$this->baseUrl}/rates/couriers", [
                'origin_latitude' => $origin['latitude'],
                'origin_longitude' => $origin['longitude'],
                'destination_latitude' => $destination['latitude'],
                'destination_longitude' => $destination['longitude'],
                'couriers' => config('services.biteship.couriers'),
                'items' => $items,
            ]);

        if ($response->failed()) {
            Log::warning('Biteship rates request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Unable to reach the courier service.');
        }

        $data = $response->json();

        if (! ($data['success'] ?? false)) {
            return [];
        }

        return $data['pricing'] ?? [];
    }

    /**
     * Look up Indonesian administrative areas (with their official postal code) matching
     * free-text input — e.g. a city/district name pulled from the map's reverse-geocode —
     * so the checkout form's postal code comes from Biteship's own delivery-area database
     * instead of a third-party geocoder's guess.
     *
     * @return array<int, array{id: string, name: string, postal_code: int|string, administrative_division_level_1_name: ?string, administrative_division_level_2_name: ?string, administrative_division_level_3_name: ?string}>
     *
     * @throws RuntimeException when the API key is missing or the request fails.
     */
    public function searchAreas(string $query): array
    {
        if ($this->apiKey === '') {
            throw new RuntimeException('Biteship API key is not configured.');
        }

        $response = Http::withHeaders(['Authorization' => $this->apiKey])
            ->timeout(10)
            ->get("{$this->baseUrl}/maps/areas", [
                'countries' => 'ID',
                'input' => $query,
                'type' => 'single',
            ]);

        if ($response->failed()) {
            Log::warning('Biteship area search failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Unable to reach the area lookup service.');
        }

        $data = $response->json();

        if (! ($data['success'] ?? false)) {
            return [];
        }

        return $data['areas'] ?? [];
    }
}
