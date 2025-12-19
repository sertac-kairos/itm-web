<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SketchfabService
{
    private string $baseUrl = 'https://api.sketchfab.com/v3';
    private string $token;

    public function __construct()
    {
        $this->token = config('services.sketchfab.token', '');
    }

    /**
     * Get 3D model file URLs from Sketchfab API
     *
     * @param string $sketchfabId
     * @return array|null
     */
    public function getModelFileUrl(string $sketchfabId): ?array
    {
        try {
            if (empty($this->token)) {
                Log::error('Sketchfab token not configured');
                return null;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->token,
            ])->get("{$this->baseUrl}/models/{$sketchfabId}/download");

            if (!$response->successful()) {
                Log::error('Sketchfab API request failed', [
                    'sketchfab_id' => $sketchfabId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            // Check if USDZ format is available
            if (isset($data['usdz']) && isset($data['usdz']['url'])) {
                return [
                    'format' => 'usdz',
                    'url' => $data['usdz']['url'],
                    'size' => $data['usdz']['size'] ?? null,
                    'expires' => $data['usdz']['expires'] ?? null,
                    'all_formats' => $data
                ];
            }

            // If USDZ not available, return available formats
            $availableFormats = [];
            foreach (['gltf', 'glb', 'source'] as $format) {
                if (isset($data[$format]) && isset($data[$format]['url'])) {
                    $availableFormats[$format] = [
                        'url' => $data[$format]['url'],
                        'size' => $data[$format]['size'] ?? null,
                        'expires' => $data[$format]['expires'] ?? null,
                    ];
                }
            }

            if (empty($availableFormats)) {
                Log::warning('No downloadable formats found for model', [
                    'sketchfab_id' => $sketchfabId,
                    'response' => $data
                ]);
                return null;
            }

            // Return first available format with all formats info
            $firstFormat = array_key_first($availableFormats);
            return [
                'format' => $firstFormat,
                'url' => $availableFormats[$firstFormat]['url'],
                'size' => $availableFormats[$firstFormat]['size'],
                'expires' => $availableFormats[$firstFormat]['expires'],
                'available_formats' => $availableFormats,
                'all_formats' => $data
            ];

        } catch (Exception $e) {
            Log::error('Error fetching Sketchfab model file URL', [
                'sketchfab_id' => $sketchfabId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Get only USDZ format URL
     *
     * @param string $sketchfabId
     * @return string|null
     */
    public function getUsdzUrl(string $sketchfabId): ?string
    {
        $result = $this->getModelFileUrl($sketchfabId);
        return $result && $result['format'] === 'usdz' ? $result['url'] : null;
    }

    /**
     * Check if model has USDZ format
     *
     * @param string $sketchfabId
     * @return bool
     */
    public function hasUsdzFormat(string $sketchfabId): bool
    {
        $result = $this->getModelFileUrl($sketchfabId);
        return $result && $result['format'] === 'usdz';
    }

    /**
     * Get all available formats for a model
     *
     * @param string $sketchfabId
     * @return array|null
     */
    public function getAvailableFormats(string $sketchfabId): ?array
    {
        $result = $this->getModelFileUrl($sketchfabId);
        return $result ? $result['available_formats'] ?? [] : null;
    }
}
