<?php

namespace Tests\Feature\Admin;

use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageEnquiriesTest extends TestCase
{
    use RefreshDatabase;

    private User $agent;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agent = User::factory()->create();
    }

    private function enquiry(array $attributes = []): Enquiry
    {
        return Enquiry::create(array_merge([
            'type' => 'contact', 'name' => 'Pat Buyer', 'email' => 'pat@example.com',
            'message' => 'Just checking in.',
        ], $attributes));
    }

    public function test_opening_an_enquiry_marks_it_read(): void
    {
        $enquiry = $this->enquiry();

        $this->actingAs($this->agent)->get("/admin/enquiries/{$enquiry->id}")->assertOk();

        $this->assertNotNull($enquiry->fresh()->read_at);
    }

    public function test_reopening_an_enquiry_keeps_the_original_read_timestamp(): void
    {
        $enquiry = $this->enquiry(['read_at' => now()->subDays(3)]);
        $first = $enquiry->read_at;

        $this->actingAs($this->agent)->get("/admin/enquiries/{$enquiry->id}");

        $this->assertTrue($first->equalTo($enquiry->fresh()->read_at));
    }

    public function test_the_inbox_can_be_filtered_to_unread(): void
    {
        $this->enquiry(['name' => 'Unread Person']);
        $this->enquiry(['name' => 'Read Person', 'read_at' => now()]);

        $this->actingAs($this->agent)->get('/admin/enquiries?unread=1')
            ->assertOk()
            ->assertSee('Unread Person')
            ->assertDontSee('Read Person');
    }

    public function test_the_inbox_can_be_filtered_by_type(): void
    {
        $this->enquiry(['type' => 'appraisal', 'name' => 'Appraisal Person']);
        $this->enquiry(['type' => 'contact', 'name' => 'Contact Person']);

        $this->actingAs($this->agent)->get('/admin/enquiries?type=appraisal')
            ->assertOk()
            ->assertSee('Appraisal Person')
            ->assertDontSee('Contact Person');
    }

    public function test_an_enquiry_can_be_deleted(): void
    {
        $enquiry = $this->enquiry();

        $this->actingAs($this->agent)->delete("/admin/enquiries/{$enquiry->id}")->assertRedirect();

        $this->assertSame(0, Enquiry::count());
    }

    public function test_guests_cannot_read_enquiries(): void
    {
        $enquiry = $this->enquiry();

        $this->get("/admin/enquiries/{$enquiry->id}")->assertRedirect(route('admin.login'));
        $this->assertNull($enquiry->fresh()->read_at);
    }
}
