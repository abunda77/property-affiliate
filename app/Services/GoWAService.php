<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\GoWAApiFailureNotification;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class GoWAService
{
    private string $username;

    private string $password;

    private string $apiUrl;

    // Retry configuration
    private const MAX_RETRIES = 3;

    private const INITIAL_RETRY_DELAY = 1000; // milliseconds

    private const FAILURE_THRESHOLD = 5; // Number of failures before admin notification

    private const FAILURE_WINDOW = 3600; // Time window in seconds (1 hour)

    public function __construct(?string $username = null, ?string $password = null, ?string $apiUrl = null)
    {
        if ($username && $password && $apiUrl) {
            // Use provided credentials (for testing)
            $this->username = $username;
            $this->password = $password;
            $this->apiUrl = $apiUrl;
        } else {
            // Use settings from database, fallback to config if not set
            try {
                $settings = app(GeneralSettings::class);
                $this->username = $settings->gowa_username ?? config('services.gowa.username');
                $this->password = $settings->gowa_password ?? config('services.gowa.password');
                $this->apiUrl = $settings->gowa_api_url ?? config('services.gowa.api_url');
            } catch (\Spatie\LaravelSettings\Exceptions\MissingSettings $e) {
                // Fallback to config if settings are missing
                $this->username = config('services.gowa.username');
                $this->password = config('services.gowa.password');
                $this->apiUrl = config('services.gowa.api_url');
            }
        }
    }

    /**
     * Send a WhatsApp message via GoWA API with retry mechanism
     *
     * @param  string  $phone  Phone number to send message to
     * @param  string  $message  Message content
     * @param  string|null  $replyMessageId  Optional message ID to reply to
     * @param  bool  $isForwarded  Whether the message is forwarded
     * @param  int  $duration  Message duration in seconds
     * @param  array  $context  Additional context for logging (e.g., lead_id, property_id)
     * @return bool Success status
     */
    public function sendMessage(
        string $phone,
        string $message,
        ?string $replyMessageId = null,
        bool $isForwarded = false,
        int $duration = 3600,
        array $context = []
    ): bool {
        $formattedPhone = $this->formatPhone($phone);
        $attempt = 0;
        $lastException = null;

        while ($attempt < self::MAX_RETRIES) {
            try {
                $attempt++;

                // Prepare request data
                $data = [
                    'phone' => $formattedPhone,
                    'message' => $message,
                    'is_forwarded' => $isForwarded,
                    'duration' => $duration,
                ];

                // Add reply_message_id if provided
                if ($replyMessageId) {
                    $data['reply_message_id'] = $replyMessageId;
                }

                // Send request to GoWA API with Basic Auth
                $response = Http::withBasicAuth($this->username, $this->password)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->timeout(10)
                    ->post($this->apiUrl.'/send/message', $data);

                if ($response->successful()) {
                    Log::info('WhatsApp message sent successfully', array_merge([
                        'phone' => $formattedPhone,
                        'message_length' => strlen($message),
                        'attempt' => $attempt,
                    ], $context));

                    // Reset failure counter on success
                    $this->resetFailureCounter();

                    return true;
                }

                // Log warning for failed attempt
                Log::warning('GoWA API request failed', array_merge([
                    'phone' => $formattedPhone,
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'attempt' => $attempt,
                    'max_retries' => self::MAX_RETRIES,
                ], $context));

                // If not the last attempt, wait before retrying with exponential backoff
                if ($attempt < self::MAX_RETRIES) {
                    $delay = self::INITIAL_RETRY_DELAY * pow(2, $attempt - 1);
                    usleep($delay * 1000); // Convert to microseconds
                }

            } catch (\Exception $e) {
                $lastException = $e;

                Log::error('GoWA API Error', array_merge([
                    'phone' => $phone,
                    'message' => $e->getMessage(),
                    'attempt' => $attempt,
                    'max_retries' => self::MAX_RETRIES,
                    'trace' => $e->getTraceAsString(),
                ], $context));

                // If not the last attempt, wait before retrying with exponential backoff
                if ($attempt < self::MAX_RETRIES) {
                    $delay = self::INITIAL_RETRY_DELAY * pow(2, $attempt - 1);
                    usleep($delay * 1000); // Convert to microseconds
                }
            }
        }

        // All retries failed, increment failure counter and check threshold
        $this->incrementFailureCounter($context);

        // Log final failure
        Log::error('GoWA API failed after all retries', array_merge([
            'phone' => $phone,
            'attempts' => self::MAX_RETRIES,
            'last_exception' => $lastException ? $lastException->getMessage() : 'Unknown error',
        ], $context));

        return false;
    }

    /**
     * Increment the failure counter and notify admin if threshold is reached
     *
     * @param  array  $context  Additional context for the failure
     */
    private function incrementFailureCounter(array $context = []): void
    {
        $cacheKey = 'gowa_api_failures';
        $failures = Cache::get($cacheKey, 0);
        $failures++;

        Cache::put($cacheKey, $failures, self::FAILURE_WINDOW);

        if ($failures >= self::FAILURE_THRESHOLD) {
            $this->notifyAdminOfRepeatedFailures($failures, $context);

            // Reset counter after notification to avoid spam
            Cache::put($cacheKey, 0, self::FAILURE_WINDOW);
        }
    }

    /**
     * Reset the failure counter
     */
    private function resetFailureCounter(): void
    {
        Cache::forget('gowa_api_failures');
    }

    /**
     * Notify admin users of repeated GoWA API failures
     *
     * @param  int  $failureCount  Number of failures
     * @param  array  $context  Additional context
     */
    private function notifyAdminOfRepeatedFailures(int $failureCount, array $context = []): void
    {
        try {
            // Get all super admin users
            $admins = User::role('super_admin')->get();

            if ($admins->isEmpty()) {
                Log::warning('No super admin users found to notify about GoWA API failures');

                return;
            }

            // Log the notification
            Log::critical('GoWA API repeated failures - notifying admins', [
                'failure_count' => $failureCount,
                'threshold' => self::FAILURE_THRESHOLD,
                'window_seconds' => self::FAILURE_WINDOW,
                'context' => $context,
            ]);

            // Send notification to each admin
            Notification::send($admins, new GoWAApiFailureNotification($failureCount, $context));

            Log::info('Admin notification sent for GoWA API failures', [
                'admin_count' => $admins->count(),
                'failure_count' => $failureCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to notify admins of GoWA API failures', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Test the GoWA API connection
     *
     * @param  string  $testPhone  Phone number to send test message to
     * @return array Response with success status and message
     */
    public function testConnection(string $testPhone): array
    {
        try {
            // Validate credentials
            if (empty($this->username) || empty($this->password) || empty($this->apiUrl)) {
                return [
                    'success' => false,
                    'message' => 'Kredensial GoWA tidak lengkap. Pastikan username, password, dan URL API sudah diisi.',
                ];
            }

            // Format phone number
            $formattedPhone = $this->formatPhone($testPhone);

            // Prepare test message
            $data = [
                'phone' => $formattedPhone,
                'message' => 'Test koneksi GoWA API - '.now()->format('Y-m-d H:i:s'),
                'is_forwarded' => false,
                'duration' => 3600,
            ];

            // Send test request
            $response = Http::withBasicAuth($this->username, $this->password)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->timeout(10)
                ->post($this->apiUrl.'/send/message', $data);

            if ($response->successful()) {
                $responseData = $response->json();

                return [
                    'success' => true,
                    'message' => 'Koneksi berhasil! Pesan test telah dikirim ke '.$testPhone,
                    'data' => $responseData,
                ];
            }

            return [
                'success' => false,
                'message' => 'Koneksi gagal. Status: '.$response->status().' - '.$response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Format phone number to WhatsApp format (e.g., 6289685028129@s.whatsapp.net)
     * Converts Indonesian phone numbers starting with 0 to 62
     *
     * @param  string  $phone  Phone number to format
     * @return string Formatted phone number for WhatsApp
     */
    private function formatPhone(string $phone): string
    {
        // Remove any whitespace, dashes, parentheses, and + sign
        $phone = preg_replace('/[\s\-\(\)\+]/', '', $phone);

        // If phone starts with 0, replace with 62 (Indonesia)
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        // If phone doesn't start with 62, assume Indonesian number
        if (! str_starts_with($phone, '62')) {
            $phone = '62'.$phone;
        }

        // Add WhatsApp suffix
        return $phone.'@s.whatsapp.net';
    }
}
