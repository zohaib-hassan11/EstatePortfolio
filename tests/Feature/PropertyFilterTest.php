<?php

namespace Tests\Feature;

use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PropertySeeder::class);
    }

    public function test_filters_by_suburb(): void
    {
        $response = $this->get('/properties?suburb=Bahria Town')->assertOk();

        foreach ($response->viewData('properties') as $property) {
            $this->assertSame('Bahria Town', $property->suburb);
        }
    }

    public function test_filters_by_minimum_bedrooms(): void
    {
        $response = $this->get('/properties?beds=4')->assertOk();

        $this->assertNotEmpty($response->viewData('properties'));
        foreach ($response->viewData('properties') as $property) {
            $this->assertGreaterThanOrEqual(4, $property->bedrooms);
        }
    }

    public function test_filters_by_price_range(): void
    {
        $response = $this->get('/properties?min=20000000&max=40000000')->assertOk();

        foreach ($response->viewData('properties') as $property) {
            $this->assertGreaterThanOrEqual(20000000, $property->price);
            $this->assertLessThanOrEqual(40000000, $property->price);
        }
    }

    public function test_search_matches_suburb_and_street(): void
    {
        $response = $this->get('/properties?q=Gulberg')->assertOk();

        $this->assertNotEmpty($response->viewData('properties'));
    }

    public function test_sorts_by_price_ascending(): void
    {
        $prices = collect($this->get('/properties?sort=price_asc')->viewData('properties')->items())
            ->pluck('price')
            ->filter()
            ->values();

        $this->assertEquals($prices->sort()->values()->all(), $prices->all());
    }

    public function test_no_matches_returns_an_empty_page_not_an_error(): void
    {
        $this->get('/properties?suburb=Nowhere Town')
            ->assertOk()
            ->assertSee('No properties match those filters');
    }

    public function test_filters_are_preserved_across_pagination_links(): void
    {
        $properties = $this->get('/properties?suburb=DHA Lahore')->viewData('properties');

        $this->assertStringContainsString('suburb=DHA', $properties->url(1));
    }
}
