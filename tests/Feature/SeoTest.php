<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PropertySeeder::class);
    }

    /** Pull the single JSON-LD graph out of a response. */
    private function graph(string $uri): array
    {
        $html = $this->get($uri)->assertOk()->getContent();

        // Located by offset rather than a lazy regex: the graph is long enough to
        // trip PCRE's backtrack limit.
        $open = '<script type="application/ld+json">';
        $start = strpos($html, $open);
        $this->assertNotFalse($start, "no JSON-LD found on {$uri}");

        $start += strlen($open);
        $end = strpos($html, '</script>', $start);
        $this->assertNotFalse($end, "unterminated JSON-LD on {$uri}");

        $decoded = json_decode(trim(substr($html, $start, $end - $start)), true);
        $this->assertSame(JSON_ERROR_NONE, json_last_error(), 'JSON-LD is not valid JSON');

        return $decoded;
    }

    private function types(array $graph): array
    {
        return array_column($graph['@graph'], '@type');
    }

    public function test_the_home_page_publishes_a_connected_graph(): void
    {
        $graph = $this->graph('/');

        $this->assertSame('https://schema.org', $graph['@context']);
        $this->assertContains('RealEstateAgent', $this->types($graph));
        $this->assertContains('WebSite', $this->types($graph));

        $agent = collect($graph['@graph'])->firstWhere('@type', 'RealEstateAgent');
        $site  = collect($graph['@graph'])->firstWhere('@type', 'WebSite');

        // The website points at the business by id rather than repeating it.
        $this->assertSame($agent['@id'], $site['publisher']['@id']);
        $this->assertSame(config('agent.phone'), $agent['telephone']);
        $this->assertNotEmpty($agent['areaServed']);
    }

    public function test_opening_hours_are_machine_readable(): void
    {
        $agent = collect($this->graph('/')['@graph'])->firstWhere('@type', 'RealEstateAgent');

        $this->assertNotEmpty($agent['openingHoursSpecification']);

        foreach ($agent['openingHoursSpecification'] as $spec) {
            $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $spec['opens']);
            $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $spec['closes']);
            $this->assertNotEmpty($spec['dayOfWeek']);
        }
    }

    public function test_a_listing_carries_its_own_node_and_breadcrumbs(): void
    {
        $property = Property::published()->forSale()->whereNotNull('price')->first();
        $graph = $this->graph("/properties/{$property->slug}");
        $types = $this->types($graph);

        $this->assertContains('BreadcrumbList', $types);

        $listing = collect($graph['@graph'])
            ->first(fn ($n) => isset($n['@id']) && str_contains($n['@id'], '#property'));

        $this->assertNotNull($listing);
        $this->assertSame($property->title, $listing['name']);
        $this->assertSame($property->price, $listing['offers']['price']);
        $this->assertSame(config('agent.format.price.currency'), $listing['offers']['priceCurrency']);
        // The offer names the agent by reference, not by duplicating them.
        $this->assertSame(\App\Support\Seo::agentId(), $listing['offers']['seller']['@id']);
    }

    public function test_the_results_pages_declare_an_item_list(): void
    {
        foreach (['/properties', '/recently-sold'] as $uri) {
            $this->assertContains('ItemList', $this->types($this->graph($uri)), $uri);
        }
    }

    public function test_the_selling_page_still_publishes_faqs(): void
    {
        $this->assertContains('FAQPage', $this->types($this->graph('/selling')));
    }

    public function test_filtered_pages_are_not_indexed_but_are_followed(): void
    {
        $this->get('/properties?suburb=Gulberg')
            ->assertOk()
            ->assertSee('content="noindex, follow"', false);

        $this->get('/properties')
            ->assertOk()
            ->assertSee('content="index, follow', false);
    }

    public function test_a_filtered_page_canonicalises_to_the_clean_url(): void
    {
        $this->get('/properties?suburb=Gulberg&beds=3')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('properties').'">', false);
    }

    public function test_page_two_canonicalises_to_itself(): void
    {
        $this->get('/properties?page=2')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('properties').'?page=2">', false);
    }

    public function test_every_page_has_a_title_description_and_canonical(): void
    {
        foreach (['/', '/about', '/properties', '/recently-sold', '/buying', '/selling', '/testimonials', '/contact'] as $uri) {
            $html = $this->get($uri)->assertOk()->getContent();

            $this->assertMatchesRegularExpression('/<title>.{10,}<\/title>/', $html, $uri);
            $this->assertStringContainsString('<meta name="description"', $html, $uri);
            $this->assertStringContainsString('<link rel="canonical"', $html, $uri);
            $this->assertSame(1, substr_count($html, '<h1'), "{$uri} should have exactly one h1");
        }
    }

    public function test_the_sitemap_lists_listing_images(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk();
        $xml = $response->getContent();

        $this->assertStringContainsString('sitemap-image/1.1', $xml);
        $this->assertStringContainsString('<image:loc>', $xml);
        $this->assertNotFalse(simplexml_load_string($xml), 'sitemap is not well-formed XML');
    }

    public function test_the_404_page_is_branded_and_not_indexed(): void
    {
        $this->get('/properties/no-such-listing')
            ->assertNotFound()
            ->assertSee('That page has moved on.')
            ->assertSee('content="noindex, follow"', false);
    }
}
