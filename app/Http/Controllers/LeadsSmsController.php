<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeadsSmsController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send SMS to lead
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id',
            'phone_numbers' => 'required|array|min:1',
            'phone_numbers.*' => 'required|string',
            'template_key' => 'required|string',
            'message' => 'nullable|string',
            'template_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $lead = Lead::findOrFail($request->lead_id);

            // Get template configuration
            $templates = config('services.sms.templates', []);
            $templateKey = $request->template_key;

            if (!isset($templates[$templateKey])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid template selected',
                ], 400);
            }

            $template = $templates[$templateKey];

            // Prepare variables for template
            $variables = [
                'customer_name' => $lead->customer_name ?? $lead->first_name,
                'first_name' => $lead->first_name,
                'last_name' => $lead->last_name,
                'destination' => $lead->destination->name ?? 'your destination',
                'travel_date' => $lead->travel_date ? \Carbon\Carbon::parse($lead->travel_date)->format('d M Y') : '',
                'service' => $lead->service->name ?? 'our service',
            ];

            // Send SMS using template
            $result = $this->smsService->sendTemplatedSms(
                $request->phone_numbers,
                $templateKey,
                $variables
            );

            // Log SMS activity
            Log::info('SMS sent to lead', [
                'lead_id' => $lead->id,
                'phone_numbers' => $request->phone_numbers,
                'template' => $templateKey,
                'result' => $result,
            ]);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error sending SMS', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available SMS templates
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTemplates()
    {
        $templates = config('services.sms.templates', []);

        $formattedTemplates = [];
        foreach ($templates as $key => $template) {
            $formattedTemplates[] = [
                'key' => $key,
                'name' => $template['name'] ?? ucfirst(str_replace('_', ' ', $key)),
                'message' => $template['message'],
                'description' => $template['description'] ?? '',
            ];
        }

        return response()->json([
            'success' => true,
            'templates' => $formattedTemplates,
        ]);
    }

    /**
     * Send custom SMS (without template)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendCustomSms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_numbers' => 'required|array|min:1',
            'phone_numbers.*' => 'required|string',
            'message' => 'required|string|max:1000',
            'template_id' => 'required|string',
            'campaign_name' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $result = $this->smsService->sendSms(
                $request->phone_numbers,
                $request->message,
                $request->template_id,
                $request->campaign_name ?? 'CRM Custom SMS'
            );

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error sending custom SMS', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS: ' . $e->getMessage(),
            ], 500);
        }
    }
}
