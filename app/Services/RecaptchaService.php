<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    private const MINIMUM_SCORE = 0.5;

    /**
     * Verify a reCAPTCHA v3 token.
     *
     * Returns the score (0.0–1.0) or null on failure/disabled.
     */
    public function verify(string $token, string $ipAddress): ?float
    {
        $secretKey = config('services.recaptcha.secret');

        // If reCAPTCHA is not configured, skip verification gracefully
        if (empty($secretKey)) {
            return null;
        }

        try {
            $response = Http::timeout(5)->asForm()->post(self::VERIFY_URL, [
                'secret'   => $secretKey,
                'response' => $token,
                'remoteip' => $ipAddress,
            ]);

            if (! $response->ok()) {
                Log::warning('reCAPTCHA API returned non-200 status', [
                    'status' => $response->status(),
                ]);

                return null;
            }

            $data = $response->json();

            if (! ($data['success'] ?? false)) {
                Log::warning('reCAPTCHA verification failed', [
                    'error_codes' => $data['error-codes'] ?? [],
                ]);

                return null;
            }

            return (float) ($data['score'] ?? 0.0);
        } catch (ConnectionException $e) {
            Log::error('reCAPTCHA connection error', ['message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Determine if a score represents a human (passes threshold).
     */
    public function isHuman(?float $score): bool
    {
        if ($score === null) {
            // reCAPTCHA disabled; allow submission
            return true;
        }

        return $score >= self::MINIMUM_SCORE;
    }
}
