<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:8000'],
            'status'      => ['required', Rule::in(['for_sale', 'under_offer', 'sold'])],
            'type'        => ['required', Rule::in(['house', 'apartment', 'townhouse', 'land', 'acreage'])],

            'price'       => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'price_label' => ['nullable', 'string', 'max:80'],
            'sold_price'  => ['nullable', 'integer', 'min:0', 'max:999999999'],
            'sold_at'     => ['nullable', 'date'],
            'days_on_market' => ['nullable', 'integer', 'min:0', 'max:2000'],

            'address'   => ['required', 'string', 'max:200'],
            'suburb'    => ['required', 'string', 'max:120'],
            'state'     => ['required', 'string', 'max:8'],
            'postcode'  => ['required', 'string', 'max:8'],
            'latitude'  => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            'bedrooms'   => ['required', 'integer', 'min:0', 'max:50'],
            'bathrooms'  => ['required', 'integer', 'min:0', 'max:50'],
            'carspaces'  => ['required', 'integer', 'min:0', 'max:50'],
            'land_size'  => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'floor_size' => ['nullable', 'integer', 'min:0', 'max:1000000'],

            'features'          => ['nullable', 'string', 'max:1000'],
            'inspection_times'  => ['nullable', 'string', 'max:180'],
            'meta_description'  => ['nullable', 'string', 'max:180'],

            'is_featured'  => ['boolean'],
            'is_published' => ['boolean'],

            'images'   => ['nullable', 'array', 'max:20'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured'  => $this->boolean('is_featured'),
            'is_published' => $this->boolean('is_published'),
        ]);
    }

    /**
     * Attributes ready for a mass assignment, with the comma-separated
     * feature list normalised into an array.
     */
    public function propertyAttributes(): array
    {
        $data = $this->safe()->except(['images', 'features']);

        $data['features'] = collect(explode(',', (string) $this->input('features')))
            ->map(fn ($f) => trim($f))
            ->filter()
            ->values()
            ->all();

        return $data;
    }
}
