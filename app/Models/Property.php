<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Support\Format;
use Illuminate\Support\Str;

class Property extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'features'    => 'array',
            'sold_at'     => 'date',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $property) {
            if (blank($property->slug)) {
                // Append the area for uniqueness and for the keyword, but not when the
                // headline already names it - "Apartment in Gulberg" should not slug to
                // ...-gulberg-gulberg. Matching on the area's leading word is enough:
                // "DHA Lahore" is already covered by a title mentioning "DHA Phase 6".
                $title = Str::slug($property->title);
                $lead  = Str::before(Str::slug((string) $property->suburb), '-');

                $source = ($lead && ! Str::contains($title, $lead))
                    ? $property->title.' '.$property->suburb
                    : $property->title;

                $property->slug = static::uniqueSlug($source);
            }
        });
    }

    public static function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForSale(Builder $query): Builder
    {
        return $query->whereIn('status', ['for_sale', 'under_offer']);
    }

    public function scopeSold(Builder $query): Builder
    {
        return $query->where('status', 'sold');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['suburb'] ?? null, fn ($q, $v) => $q->where('suburb', $v))
            ->when($filters['type'] ?? null, fn ($q, $v) => $q->where('type', $v))
            ->when($filters['beds'] ?? null, fn ($q, $v) => $q->where('bedrooms', '>=', (int) $v))
            ->when($filters['baths'] ?? null, fn ($q, $v) => $q->where('bathrooms', '>=', (int) $v))
            ->when($filters['min'] ?? null, fn ($q, $v) => $q->where('price', '>=', (int) $v))
            ->when($filters['max'] ?? null, fn ($q, $v) => $q->where('price', '<=', (int) $v))
            ->when($filters['q'] ?? null, function ($q, $v) {
                $q->where(fn ($sub) => $sub
                    ->where('title', 'like', "%{$v}%")
                    ->orWhere('suburb', 'like', "%{$v}%")
                    ->orWhere('address', 'like', "%{$v}%"));
            });
    }

    public function heroImage(): ?PropertyImage
    {
        return $this->images->first();
    }

    public function heroImageUrl(): string
    {
        return $this->heroImage()?->url() ?? asset('images/placeholder-property.svg');
    }

    public function priceDisplay(): string
    {
        if ($this->status === 'sold') {
            return $this->sold_price ? 'Sold for '.Format::price($this->sold_price) : 'Sold';
        }

        if (filled($this->price_label)) {
            return $this->price_label;
        }

        return $this->price ? Format::price($this->price) : 'Contact for price';
    }

    /** Land size in the local unit - Marla/Kanal in Pakistan, m² elsewhere. */
    public function landDisplay(): ?string
    {
        return Format::area($this->land_size);
    }

    /** Covered area is quoted in square feet locally. */
    public function floorDisplay(): ?string
    {
        return $this->floor_size ? number_format($this->floor_size).' sq ft' : null;
    }

    /**
     * "House 88, Sector C" + area "Bahria Town" -> "House 88, Sector C, Bahria Town".
     * The area is left off when the street address already names it, so we never
     * print "Sector C, Bahria Town, Bahria Town".
     */
    public function shortAddress(): string
    {
        $lead = Str::before(Str::slug((string) $this->suburb), '-');

        return ($lead && Str::contains(Str::slug($this->address), $lead))
            ? $this->address
            : $this->address.', '.$this->suburb;
    }

    public function fullAddress(): string
    {
        return $this->shortAddress().', '.$this->state.' '.$this->postcode;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'for_sale'    => 'For Sale',
            'under_offer' => 'Under Offer',
            'sold'        => 'Sold',
            default       => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /** Land has no rooms to count, so the bed/bath/car row is hidden for it. */
    public function hasRooms(): bool
    {
        return $this->type !== 'land'
            && ($this->bedrooms || $this->bathrooms || $this->carspaces);
    }

    public function typeLabel(): string
    {
        return config('agent.property_types.'.$this->type, ucfirst($this->type));
    }
}
