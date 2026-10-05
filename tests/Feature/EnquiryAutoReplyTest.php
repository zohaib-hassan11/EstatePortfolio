<?php

namespace Tests\Feature;

use App\Mail\EnquiryReceived;
use App\Models\Enquiry;
use App\Models\Property;
use App\Models\User;
use App\Support\Ai\AiConnector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Support\FakeAiConnector;
use Tests\TestCase;

/**
 * The AI-written first reply to a form enquiry: when it is used, when it is
 * refused, and that the client always gets exactly one email either way.
 */
class EnquiryAutoReplyTest extends TestCase
{
    use RefreshDatabase;

    private Property $property;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        $this->property = Property::create([
            'title' => '10 Marla House in DHA Phase 6', 'type' => 'house',
            'status' => 'for_sale', 'is_published' => true,
            'address' => 'House 42, Block J', 'suburb' => 'DHA Phase 6',
            'state' => 'Punjab', 'postcode' => '54000',
            'price' => 32500000, 'bedrooms' => 4, 'bathrooms' => 3, 'carspaces' => 2,
            'land_size' => 10, 'description' => 'A corner house.',
        ]);
    }

    private function ai(string $reply): FakeAiConnector
    {
        $fake = new FakeAiConnector($reply);
        $this->app->instance(AiConnector::class, $fake);

        return $fake;
    }

    private function enquire(string $message = 'Is it still available and how many bedrooms does it have?', array $fields = [])
    {
        return $this->postJson('/enquiries', array_merge([
            'type' => 'property', 'property_id' => $this->property->id,
            'name' => 'Ayesha Khan', 'email' => 'ayesha@example.com', 'phone' => '0300 1234567',
            'message' => $message,
        ], $fields))->assertCreated();
    }

    private static function goodReply(): string
    {
        return "Dear Ayesha,\n\nThank you for your enquiry. Yes, the house is still for sale at PKR 3.25 Crore, and it has 4 bedrooms "
            ."on 10 Marla. Whether there is room on the price is something I will confirm personally when we speak.\n\n"
            ."I will call you on 0300 1234567 shortly.\n\nRegards,\nZohaib Hassan";
    }

    public function test_the_client_gets_an_ai_answer_to_their_question(): void
    {
        $fake = $this->ai(self::goodReply());

        $this->enquire();

        Mail::assertSent(EnquiryReceived::class, function (EnquiryReceived $mail) {
            $html = $mail->render();

            return $mail->hasTo('ayesha@example.com')
                && $mail->hasSubject('Re: your enquiry about 10 Marla House in DHA Phase 6')
                && str_contains($html, 'it has 4 bedrooms')
                && str_contains($html, 'prepared automatically')       // they are told it is automated
                && str_contains($html, 'View the listing');            // the card is still from the records
        });
        Mail::assertSentCount(1);

        // The model saw the question and the real listing facts.
        $prompt = $fake->messages[0]['content'];
        $this->assertStringContainsString('how many bedrooms', $prompt);
        $this->assertStringContainsString('PKR 3.25 Crore', $prompt);
        $this->assertStringContainsString('do NOT guess', $fake->system);

        $this->assertSame(self::goodReply(), Enquiry::sole()->auto_reply);
    }

    public function test_the_ai_may_only_read_listings_never_write(): void
    {
        $fake = $this->ai(self::goodReply());

        $this->enquire();

        $this->assertSame(['search_properties', 'get_property'], array_column($fake->tools, 'name'));
    }

    public function test_a_search_result_counts_as_a_record_the_reply_can_quote(): void
    {
        $other = Property::create([
            'title' => '1 Kanal House in Bahria Town', 'type' => 'house', 'status' => 'for_sale', 'is_published' => true,
            'address' => 'House 9, Sector C', 'suburb' => 'Bahria Town', 'state' => 'Punjab', 'postcode' => '53720',
            'price' => 65000000, 'bedrooms' => 5, 'bathrooms' => 5, 'land_size' => 20, 'description' => 'Large.',
        ]);

        $this->ai("Dear Ayesha,\n\nThank you for asking about other options. I also have a 1 Kanal house in Bahria Town "
            ."with 5 bedrooms at PKR 6.5 Crore:\n".route('properties.show', $other)."\n\nRegards,\nZohaib Hassan")
            ->callsTool('search_properties', ['area' => 'Bahria']);

        $this->enquire('Do you have anything bigger in Bahria Town?');

        $this->assertStringContainsString('PKR 6.5 Crore', Enquiry::sole()->auto_reply);
    }

    public function test_the_reply_may_state_the_budget_it_searched(): void
    {
        $this->ai("Dear Ayesha,\n\nThank you for your enquiry. I searched Bahria Town for houses in a similar price range "
            ."(PKR 3–5 Crore) and nothing matches right now, but this house is still for sale at PKR 3.25 Crore.\n\nRegards,\nZohaib Hassan")
            ->callsTool('search_properties', ['area' => 'Bahria', 'min_price' => 30000000, 'max_price' => 50000000]);

        $this->enquire('Anything similar in Bahria Town?');

        $this->assertStringContainsString('PKR 3–5 Crore', (string) Enquiry::sole()->auto_reply);
    }

    public function test_an_invented_price_falls_back_to_the_template(): void
    {
        $this->ai("Dear Ayesha,\n\nGood news - the owner will accept PKR 3 Crore if you can move this week. "
            ."It has 4 bedrooms on 10 Marla.\n\nRegards,\nZohaib Hassan");

        $this->enquire('Can you do a better price?');

        Mail::assertSent(EnquiryReceived::class, fn (EnquiryReceived $mail) => str_contains($mail->render(), 'Thanks, Ayesha Khan')
            && ! str_contains($mail->render(), 'PKR 3 Crore'));
        Mail::assertSentCount(1);
        $this->assertNull(Enquiry::sole()->auto_reply);
    }

    public function test_an_ai_outage_falls_back_to_the_template(): void
    {
        $this->app->instance(AiConnector::class, FakeAiConnector::failing());

        $this->enquire();

        Mail::assertSent(EnquiryReceived::class, fn (EnquiryReceived $mail) => str_contains($mail->render(), 'Thanks, Ayesha Khan'));
        $this->assertNotNull(Enquiry::sole()->confirmation_sent_at);
        $this->assertNull(Enquiry::sole()->auto_reply);
    }

    public function test_no_question_means_no_ai_call(): void
    {
        $fake = $this->ai(self::goodReply());

        $this->enquire('', ['message' => null]);

        $this->assertSame(0, $fake->calls);
        Mail::assertSent(EnquiryReceived::class);
    }

    public function test_appraisals_get_the_template_not_an_ai_valuation(): void
    {
        $fake = $this->ai(self::goodReply());

        $this->enquire('What is my house worth?', ['type' => 'appraisal', 'property_id' => null, 'suburb' => 'Wapda Town']);

        $this->assertSame(0, $fake->calls);
    }

    public function test_it_can_be_switched_off(): void
    {
        $fake = $this->ai(self::goodReply());
        config(['ai.enquiry_reply.enabled' => false]);

        $this->enquire();

        $this->assertSame(0, $fake->calls);
        $this->assertNull(Enquiry::sole()->auto_reply);
    }

    public function test_a_flood_of_enquiries_cannot_run_up_the_ai_bill(): void
    {
        $fake = $this->ai(self::goodReply());

        for ($i = 0; $i < 5; $i++) {
            $this->enquire();
        }

        // Three emails an hour per address - and no model call for the rest.
        $this->assertSame(3, $fake->calls);
    }

    public function test_the_agent_sees_exactly_what_was_sent_and_a_resend_repeats_it(): void
    {
        $fake = $this->ai(self::goodReply());
        $this->enquire();
        $enquiry = Enquiry::sole();
        $agent = User::factory()->create();

        $this->actingAs($agent)->get("/admin/enquiries/{$enquiry->id}")
            ->assertOk()
            ->assertSee('Automatic reply sent')
            ->assertSee('it has 4 bedrooms')
            ->assertSee('AI reply sent');

        $this->actingAs($agent)->post("/admin/enquiries/{$enquiry->id}/confirmation")->assertRedirect();

        $this->assertSame(1, $fake->calls); // the resend did not ask the model again
        Mail::assertSent(EnquiryReceived::class, 2);
    }

    public function test_the_inbox_draft_follows_on_from_the_automatic_reply(): void
    {
        $fake = $this->ai(self::goodReply());
        $this->enquire();
        $enquiry = Enquiry::sole();

        $this->actingAs(User::factory()->create())->postJson("/admin/enquiries/{$enquiry->id}/draft")->assertOk();

        $this->assertStringContainsString('THE AUTOMATIC REPLY THEY ALREADY RECEIVED', $fake->prompt);
        $this->assertStringContainsString('it has 4 bedrooms', $fake->prompt);
    }
}
