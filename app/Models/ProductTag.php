<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class ProductTag extends Model
{
    protected $fillable = ['name', 'slug'];

    public static function findOrCreateByName(string $name): static
    {
        $slug = Str::slug($name);
        return static::firstOrCreate(['slug' => $slug], ['name' => trim($name), 'slug' => $slug]);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tag', 'product_tag_id', 'product_id');
    }
}
