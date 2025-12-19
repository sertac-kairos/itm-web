<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FCMService
{
    private $projectId;
    private $serviceAccountPath;
    private $messagingSenderId;
    private $accessToken;

    public function __construct()
    {
        $this->projectId = config('services.fcm.project_id');
        $this->serviceAccountPath = config('services.fcm.service_account_path');
        $this->messagingSenderId = config('services.fcm.messaging_sender_id');
    }

    /**
     * Get access token for FCM API
     */
    private function getAccessToken(): string
    {
        if ($this->accessToken && $this->isTokenValid()) {
            return $this->accessToken;
        }

        try {
            if (!file_exists($this->serviceAccountPath)) {
                throw new Exception("Service account file not found: {$this->serviceAccountPath}");
            }
            
            $serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON in service account file: " . json_last_error_msg());
            }
            
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $this->createJWT($serviceAccount)
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                $this->tokenExpiry = now()->addSeconds($data['expires_in'] - 60); // 60 saniye erken yenile
                return $this->accessToken;
            }

            throw new Exception('Failed to get access token: ' . $response->body());
        } catch (Exception $e) {
            Log::error('FCM Access Token Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Check if current token is still valid
     */
    private function isTokenValid(): bool
    {
        return isset($this->tokenExpiry) && $this->tokenExpiry > now();
    }

    /**
     * Create JWT for service account authentication
     */
    private function createJWT(array $serviceAccount): string
    {
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT'
        ];

        $now = time();
        $payload = [
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600
        ];

        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        $signature = '';
        openssl_sign(
            $headerEncoded . '.' . $payloadEncoded,
            $signature,
            $serviceAccount['private_key'],
            'SHA256'
        );

        $signatureEncoded = $this->base64UrlEncode($signature);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }

    /**
     * Base64 URL encode
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Send notification to single device
     */
    public function sendToDevice(string $token, string $title, string $body, array $data = []): bool
    {
        try {
            $accessToken = $this->getAccessToken();

            // Convert data array to string values for FCM
            $fcmData = [];
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $fcmData[$key] = json_encode($value);
                } else {
                    $fcmData[$key] = (string) $value;
                }
            }

            $message = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body
                    ],
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                        ]
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1
                            ]
                        ]
                    ]
                ]
            ];

            // Only add data field if it's not empty
            if (!empty($fcmData)) {
                $message['message']['data'] = $fcmData;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", $message);

            if ($response->successful()) {
                Log::info('FCM notification sent successfully', [
                    'token' => substr($token, 0, 20) . '...',
                    'title' => $title
                ]);
                return true;
            }

            Log::error('FCM notification failed', [
                'token' => substr($token, 0, 20) . '...',
                'response' => $response->body(),
                'status' => $response->status()
            ]);
            return false;

        } catch (Exception $e) {
            Log::error('FCM send error', [
                'token' => substr($token, 0, 20) . '...',
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices(array $tokens, string $title, string $body, array $data = []): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($tokens as $token) {
            if ($this->sendToDevice($token, $title, $body, $data)) {
                $results['success']++;
            } else {
                $results['failed']++;
                $results['errors'][] = "Failed to send to token: " . substr($token, 0, 20) . '...';
            }
        }

        return $results;
    }

    /**
     * Send notification to topic
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        try {
            $accessToken = $this->getAccessToken();

            // Convert data array to string values for FCM
            $fcmData = [];
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $fcmData[$key] = json_encode($value);
                } else {
                    $fcmData[$key] = (string) $value;
                }
            }

            $message = [
                'message' => [
                    'topic' => $topic,
                    'notification' => [
                        'title' => $title,
                        'body' => $body
                    ],
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                        ]
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1
                            ]
                        ]
                    ]
                ]
            ];

            // Only add data field if it's not empty
            if (!empty($fcmData)) {
                $message['message']['data'] = $fcmData;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post("https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send", $message);

            if ($response->successful()) {
                Log::info('FCM topic notification sent successfully', [
                    'topic' => $topic,
                    'title' => $title
                ]);
                return true;
            }

            Log::error('FCM topic notification failed', [
                'topic' => $topic,
                'response' => $response->body(),
                'status' => $response->status()
            ]);
            return false;

        } catch (Exception $e) {
            Log::error('FCM topic send error', [
                'topic' => $topic,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
