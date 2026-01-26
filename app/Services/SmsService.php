<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiUrl;
    protected $authKey;
    protected $sender;
    protected $route;

    public function __construct()
    {
        $this->apiUrl = config('services.sms.api_url', 'http://hostname.in/api/send/sms');
        $this->authKey = config('services.sms.auth_key');
        $this->sender = config('services.sms.sender', 'ABCXYZ');
        $this->route = config('services.sms.route', 'TR'); // TR for transactional, PR for promotional
    }

    /**
     * Send SMS to single or multiple receivers
     *
     * @param string|array $receivers Phone number(s)
     * @param string $message Message content
     * @param string $templateId DLT approved template ID
     * @param string $campaignName Campaign name
     * @param string $scheduleTime Optional schedule time (format: dd-mm-yyyy hh:mm:ss)
     * @param int $coding 1 for English, 2 for other languages
     * @return array Response from SMS API
     */
    public function sendSms(
        $receivers,
        string $message,
        string $templateId,
        string $campaignName = 'CRM Lead SMS',
        string $scheduleTime = '',
        int $coding = 1
    ): array {
        try {
            // Convert array of receivers to comma-separated string
            if (is_array($receivers)) {
                $receivers = implode(',', $receivers);
            }

            // Prepare post data
            $postData = [
                'campaign_name' => $campaignName,
                'auth_key' => $this->authKey,
                'receivers' => $receivers,
                'sender' => $this->sender,
                'route' => $this->route,
                'message' => [
                    'msgdata' => $message,
                    'Template_ID' => $templateId,
                    'coding' => (string) $coding,
                ],
                'scheduleTime' => $scheduleTime,
            ];

            // Initialize cURL
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $this->apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($postData),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json'
                ],
            ]);

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);

            curl_close($curl);

            // Log the request and response
            Log::info('SMS API Request', [
                'receivers' => $receivers,
                'campaign_name' => $campaignName,
                'template_id' => $templateId,
            ]);

            if ($error) {
                Log::error('SMS API Error', [
                    'error' => $error,
                    'receivers' => $receivers,
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send SMS: ' . $error,
                    'error' => $error,
                ];
            }

            Log::info('SMS API Response', [
                'http_code' => $httpCode,
                'response' => $response,
            ]);

            // Parse response
            $responseData = json_decode($response, true);

            return [
                'success' => $httpCode >= 200 && $httpCode < 300,
                'message' => $httpCode >= 200 && $httpCode < 300 ? 'SMS sent successfully' : 'Failed to send SMS',
                'http_code' => $httpCode,
                'response' => $responseData ?? $response,
            ];

        } catch (\Exception $e) {
            Log::error('SMS Service Exception', [
                'error' => $e->getMessage(),
                'receivers' => $receivers,
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send SMS using predefined template
     *
     * @param string|array $receivers Phone number(s)
     * @param string $templateKey Template key from config
     * @param array $variables Variables to replace in template
     * @return array Response from SMS API
     */
    public function sendTemplatedSms($receivers, string $templateKey, array $variables = []): array
    {
        $templates = config('services.sms.templates', []);

        if (!isset($templates[$templateKey])) {
            return [
                'success' => false,
                'message' => 'Template not found: ' . $templateKey,
            ];
        }

        $template = $templates[$templateKey];
        $message = $template['message'];

        // Replace variables in message
        foreach ($variables as $key => $value) {
            $message = str_replace('{{' . $key . '}}', $value, $message);
        }

        return $this->sendSms(
            $receivers,
            $message,
            $template['template_id'],
            $template['campaign_name'] ?? 'CRM Lead SMS',
            '',
            $template['coding'] ?? 1
        );
    }
}
