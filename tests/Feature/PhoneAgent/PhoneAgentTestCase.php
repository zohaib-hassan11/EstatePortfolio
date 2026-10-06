<?php

namespace Tests\Feature\PhoneAgent;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Shared setup: an API token, a few listings, and a realistic Retell payload. */
abstract class PhoneAgentTestCase extends TestCase
{
    use RefreshDatabase;

    protected const TOKEN = 'test-automation-token';

    protected function setUp(): void
    {
        parent::setUp();
        config(['integrations.automation_token' => self::TOKEN]);
    }

    protected function api(string $method, string $uri, array $data = [])
    {
        return $this->withToken(self::TOKEN)->json($method, '/api/v1/'.ltrim($uri, '/'), $data);
    }

    protected function listing(array $attributes = []): Property
    {
        return Property::create(array_merge([
            'title' => '10 Marla House in DHA Phase 6', 'type' => 'house',
            'status' => 'for_sale', 'is_published' => true,
            'address' => 'House 42, Block J', 'suburb' => 'DHA Phase 6',
            'state' => 'Punjab', 'postcode' => '54000',
            'price' => 32500000, 'bedrooms' => 4, 'bathrooms' => 3, 'carspaces' => 2,
            'land_size' => 10, 'description' => 'A corner house.',
        ], $attributes));
    }

    /** A Retell webhook body as delivered: {event, call}. */
    protected function retell(array $analysis = [], array $call = [], string $event = 'call_analyzed'): array
    {
        return [
            'event' => $event,
            'call'  => array_replace_recursive([
                'call_type'            => 'phone_call',
                'call_id'              => 'call_'.uniqid(),
                'agent_id'             => 'agent_123',
                'call_status'          => 'ended',
                'direction'            => 'inbound',
                'from_number'          => '+923001234567',
                'to_number'            => '+12137771234',
                'start_timestamp'      => 1760000000000,
                'end_timestamp'        => 1760000180000,
                'duration_ms'          => 180000,
                'disconnection_reason' => 'user_hangup',
                'transcript'           => "Agent: Hello, ZH Estates.\nUser: I want a house in DHA.",
                'recording_url'        => 'https://retellai.s3.us-west-2.amazonaws.com/call/recording.wav',
                'call_cost'            => ['combined_cost' => 42, 'total_duration_seconds' => 180],
                'call_analysis'        => [
                    'call_summary'         => 'Caller wants a 4 bedroom house in DHA up to 3.5 crore, buying within two months, paying cash.',
                    'user_sentiment'       => 'Positive',
                    'in_voicemail'         => false,
                    'call_successful'      => true,
                    'custom_analysis_data' => array_merge([
                        'name'          => 'Ayesha Khan',
                        'intent'        => 'buy',
                        'property_type' => 'house',
                        'areas'         => 'DHA',
                        'budget'        => 'up to 3.5 crore',
                        'bedrooms'      => 4,
                        'timeline'      => 'within 2 months',
                        'payment'       => 'cash',
                    ], $analysis),
                ],
            ], $call),
        ];
    }
}
