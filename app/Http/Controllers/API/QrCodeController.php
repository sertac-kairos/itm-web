<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\QrCodeScanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QrCodeController extends Controller
{
    protected QrCodeScanService $qrCodeScanService;

    public function __construct(QrCodeScanService $qrCodeScanService)
    {
        $this->qrCodeScanService = $qrCodeScanService;
    }

    /**
     * Scan QR code by UUID and return archaeological site data
     *
     * @param Request $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function scanByUuid(Request $request, string $uuid): JsonResponse
    {
        $locale = $request->input('locale', app()->getLocale());

        // Validate UUID format
        if (!$this->qrCodeScanService->isValidQrContent($uuid)) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz QR kod formatı.',
                'error_code' => 'INVALID_QR_FORMAT'
            ], 400);
        }

        return $this->qrCodeScanService->scanQrCode($uuid, $locale);
    }
}
