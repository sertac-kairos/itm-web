<?php

namespace App\Services;

use App\Models\QrCode;
use App\Models\ArchaeologicalSite;
use App\Models\Model3d;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class QrCodeScanService
{
    /**
     * Scan QR code and return archaeological site data
     *
     * @param string $qrContent
     * @param string $locale
     * @return JsonResponse
     */
    public function scanQrCode(string $qrContent, string $locale = 'tr'): JsonResponse
    {
        try {
            // Set locale
            app()->setLocale($locale);

            // Find 3D model by QR UUID
            $model3d = Model3d::with([
                'archaeologicalSite.translations',
                'archaeologicalSite.subRegion.translations',
                'archaeologicalSite.subRegion.region.translations',
                'archaeologicalSite.models3d' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order')->with('translations');
                },
                'archaeologicalSite.audioGuides' => function ($query) {
                    $query->where('is_active', true)->with('translations');
                }
            ])
            ->where('qr_uuid', $qrContent)
            ->active()
            ->first();

            if (!$model3d) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR kod bulunamadı veya aktif değil.',
                    'error_code' => 'QR_NOT_FOUND'
                ], 404);
            }

            if (!$model3d->archaeologicalSite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu QR kod için arkeolojik alan bulunamadı.',
                    'error_code' => 'ARCHAEOLOGICAL_SITE_NOT_FOUND'
                ], 404);
            }

            $archaeologicalSite = $model3d->archaeologicalSite;

            if (!$archaeologicalSite->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arkeolojik alan aktif değil.',
                    'error_code' => 'SITE_INACTIVE'
                ], 404);
            }
    // Log successful scan
            Log::info('QR Code scanned successfully', [
                'qr_uuid' => $qrContent,
                'model3d_id' => $model3d->id,
                'archaeological_site_id' => $archaeologicalSite->id,
                'locale' => $locale
            ]);


            
            // Prepare response data - return as array with single element
            return response()->json([
                'success' => true,
                'locale' => $locale,
                'data' => [
                    [
                        'id' => $archaeologicalSite->id,
                        'sub_region' => [
                            'id' => $archaeologicalSite->subRegion?->id,
                            'name' => $archaeologicalSite->subRegion?->name,
                        ],
                        'name' => $archaeologicalSite->name,
                        'description' => $archaeologicalSite->description,
                        'latitude' => $archaeologicalSite->latitude,
                        'longitude' => $archaeologicalSite->longitude,
                        'image' => $archaeologicalSite->image ? url('storage/' . $archaeologicalSite->image) : null,
                        'models_3d' => $archaeologicalSite->models3d->map(function ($model) {
                            return [
                                'id' => $model->id,
                                'name' => $model->name,
                                'description' => $model->description,
                                'sketchfab_model_id' => $model->sketchfab_model_id,
                                'thumbnail' => $model->sketchfab_thumbnail_url,
                                'sort_order' => $model->sort_order,
                            ];
                        }),
                    ]
                ]
            ]);

        
           

        } catch (\Exception $e) {
            Log::error('QR Code scan error', [
                'qr_uuid' => $qrContent,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'QR kod okunurken bir hata oluştu.',
                'error_code' => 'SCAN_ERROR'
            ], 500);
        }
    }

    /**
     * Validate QR code content format
     *
     * @param string $qrContent
     * @return bool
     */
    public function isValidQrContent(string $qrContent): bool
    {
        // Basic validation - can be extended based on QR content format
        return !empty($qrContent) && strlen($qrContent) <= 255;
    }

    /**
     * Get QR code statistics
     *
     * @return array
     */
    public function getQrCodeStats(): array
    {
        return [
            'total_qr_codes' => Model3d::whereNotNull('qr_uuid')->count(),
            'active_qr_codes' => Model3d::whereNotNull('qr_uuid')->active()->count(),
            'qr_codes_with_sites' => Model3d::whereNotNull('qr_uuid')->whereNotNull('archaeological_site_id')->count(),
            'total_models_3d' => Model3d::count(),
            'active_models_3d' => Model3d::active()->count(),
        ];
    }
}
