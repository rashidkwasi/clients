<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS via SMSOnlineGH API
     *
     * @param string $to The recipient phone number
     * @param string $message The SMS body
     * @param string|null $sender The Sender ID (Business ID)
     * @return array|bool
     */
    public function sendSms($to, $message, $sender = null)
    {
        // Retrieve configs with fallbacks to avoid null errors
        $apiKey = config('services.sms_online.key');
        $baseUrl = config('services.sms_online.url', 'https://api.smsonlinegh.com/v4/message/sms/send');
        $defaultSender = config('services.sms_online.default_sender',);

        try {
            $response = Http::asForm()->post($baseUrl, [
                'key'    => $apiKey,
                'text'   => $message,
                'sender' => $sender ?? $defaultSender,
                'to'     => $to,
                'type'   => '0' // 0 for Plain text
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            // Only log errors if the response was not successful
            Log::error("SMS API Failure", [
                'status' => $response->status(),
                'body'   => $response->body(),
                'to'     => $to
            ]);

            return false;

        } catch (\Exception $e) {
            Log::critical("SMS Connection Exception: " . $e->getMessage());
            return false;
        }
    }
}
