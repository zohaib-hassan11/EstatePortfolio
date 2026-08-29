<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyImage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function url(): string
    {
        // Seeded demo images ship in public/, uploaded images live on the public disk.
        if (Str::startsWith($this->path, ['http://', 'https://', 'images/'])) {
            return Str::startsWith($this->path, 'http') ? $this->path : asset($this->path);
        }

        return Storage::disk('public')->url($this->path);
    }

    public function isUpload(): bool
    {
        return ! Str::startsWith($this->path, ['http://', 'https://', 'images/']);
    }
}
