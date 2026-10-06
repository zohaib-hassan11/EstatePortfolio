<?php

namespace App\Services\Listings;

use App\Models\Property;
use App\Support\PropertyFacts;
use Illuminate\Validation\Rule;

/**
 * Searching published listings, for every AI channel - the website chat, the
 * email replies and the phone agent - so a caller and a website visitor
 * asking the same question get the same answer.
 *
 * Only published listings exist as far as this class is concerned.
 */
class PropertySearch
{
    /** @return array<string, list<mixed>> validation rules for search criteria */
    public static function rules(): array
    {
        return [
            'area'         => ['nullable', 'string', 'max:80'],
            'type'         => ['nullable', Rule::in(array_keys(config('agent.property_types')))],
            'min_bedrooms' => ['nullable', 'integer', 'min:0', 'max:20'],
            'min_price'    => ['nullable', 'integer', 'min:0'],
            'max_price'    => ['nullable', 'integer', 'min:0'],
            'keywords'     => ['nullable', 'string', 'max:80'],
            'sold'         => ['nullable', 'boolean'],
        ];
    }

    /**
     * @param  array<string, mixed>  $criteria  validated against rules()
     * @return array{total: int, properties: list<array<string, mixed>>}
     */
    public function search(array $criteria, int $limit = 6): array
    {
        $query = Property::published()
            ->when($criteria['sold'] ?? false, fn ($q) => $q->sold(), fn ($q) => $q->forSale())
            // People say "DHA"; the listing says "DHA Phase 6". The site's own
            // filter matches areas exactly because it offers a dropdown.
            ->when($criteria['area'] ?? null, fn ($q, $v) => $q->where('suburb', 'like', '%'.self::stripWildcards($v).'%'))
            ->filter([
                'type' => $criteria['type'] ?? null,
                'beds' => $criteria['min_bedrooms'] ?? null,
                'min'  => $criteria['min_price'] ?? null,
                'max'  => $criteria['max_price'] ?? null,
                'q'    => isset($criteria['keywords']) ? self::stripWildcards($criteria['keywords']) : null,
            ]);

        $total = (clone $query)->count();

        return [
            'total'      => $total,
            'properties' => $query->orderByDesc('is_featured')->latest()->take($limit)->get()
                ->map(fn (Property $p) => self::summary($p))
                ->values()
                ->all(),
        ];
    }

    public function find(?string $slug): ?Property
    {
        return filled($slug) ? Property::published()->where('slug', $slug)->first() : null;
    }

    /** One line of a result list: enough to recommend it out loud. */
    public static function summary(Property $p): array
    {
        return array_filter([
            'slug'      => $p->slug,
            'title'     => $p->title,
            'url'       => route('properties.show', $p),
            'status'    => $p->statusLabel(),
            'price'     => $p->priceDisplay(),
            'price_pkr' => $p->status === 'sold' ? $p->sold_price : $p->price,
            'type'      => $p->typeLabel(),
            'area'      => $p->suburb,
            'bedrooms'  => $p->hasRooms() ? $p->bedrooms : null,
            'land'      => $p->landDisplay(),
        ], fn ($v) => $v !== null && $v !== '');
    }

    /** Everything recorded about one listing, as structured data and as sentences. */
    public static function details(Property $p): array
    {
        return self::summary($p) + array_filter([
            'address'          => $p->shortAddress(),
            'bathrooms'        => $p->hasRooms() ? $p->bathrooms : null,
            'car_spaces'       => $p->hasRooms() ? $p->carspaces : null,
            'covered_area'     => $p->floorDisplay(),
            'features'         => $p->features ?: null,
            'inspection_times' => $p->status !== 'sold' ? $p->inspection_times : null,
            'facts'            => PropertyFacts::lines($p),
        ], fn ($v) => $v !== null && $v !== '');
    }

    /**
     * Wildcards are dropped rather than escaped: SQLite and MySQL disagree on
     * the default LIKE escape character, and no area name contains either.
     */
    public static function stripWildcards(string $value): string
    {
        return trim(str_replace(['%', '_'], ' ', $value));
    }
}
