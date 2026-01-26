<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmsIntegrationTest extends TestCase
{
    /**
     * Test SMS service can be instantiated
     */
    public function test_sms_service_can_be_instantiated()
    {
        $smsService = app(SmsService::class);
        $this->assertInstanceOf(SmsService::class, $smsService);
    }

    /**
     * Test SMS templates are loaded from config
     */
    public function test_sms_templates_loaded_from_config()
    {
        $templates = config('services.sms.templates');

        $this->assertIsArray($templates);
        $this->assertArrayHasKey('welcome', $templates);
        $this->assertArrayHasKey('follow_up', $templates);
        $this->assertArrayHasKey('quote_ready', $templates);
    }

    /**
     * Test get templates endpoint
     */
    public function test_get_templates_endpoint()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('leads.sms.templates'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'templates' => [
                '*' => [
                    'key',
                    'name',
                    'message',
                    'description',
                ]
            ]
        ]);
    }

    /**
     * Test send SMS endpoint requires authentication
     */
    public function test_send_sms_requires_authentication()
    {
        $response = $this->postJson(route('leads.sms.send'), [
            'lead_id' => 1,
            'phone_numbers' => ['9876543210'],
            'template_key' => 'welcome',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test send SMS endpoint validates required fields
     */
    public function test_send_sms_validates_required_fields()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('leads.sms.send'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['lead_id', 'phone_numbers', 'template_key']);
    }

    /**
     * Test send SMS with valid data
     */
    public function test_send_sms_with_valid_data()
    {
        $user = User::factory()->create();
        $lead = Lead::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'primary_phone' => '9876543210',
        ]);

        $response = $this->actingAs($user)->postJson(route('leads.sms.send'), [
            'lead_id' => $lead->id,
            'phone_numbers' => ['9876543210'],
            'template_key' => 'welcome',
        ]);

        // Note: This will fail if SMS API credentials are not configured
        // In a real test environment, you would mock the SMS service
        $response->assertStatus(200);
    }

    /**
     * Test send custom SMS validates required fields
     */
    public function test_send_custom_sms_validates_required_fields()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('leads.sms.send-custom'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['phone_numbers', 'message', 'template_id']);
    }
}
