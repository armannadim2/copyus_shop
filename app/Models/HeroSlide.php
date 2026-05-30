<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroSlide extends Model
{
    protected $fillable = ['image', 'eyebrow', 'title', 'is_active', 'sort_order'];

    protected $casts = ['is_active' => 'boolean'];

    public function imageUrl(): string
    {
        return Storage::disk('public')->url($this->image);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
