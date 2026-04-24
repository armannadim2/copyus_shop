<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    public array $translatable = [
        'name',
        'short_description',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $fillable = [
        'category_id',
        'sku',
        'slug',
        'brand',
        'name',
        'short_description',
        'description',
        'price',
        'vat_rate',
        'stock',
        'min_order_quantity',
        'unit',
        'image',
        'is_active',
        'is_featured',
        'is_seasonal',
        'low_stock_threshold',
        'notify_low_stock',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'price'               => 'decimal:4',
        'vat_rate'            => 'decimal:2',
        'stock'               => 'integer',
        'min_order_quantity'  => 'integer',
        'is_active'           => 'boolean',
        'is_featured'         => 'boolean',
        'is_seasonal'         => 'boolean',
        'notify_low_stock'    => 'boolean',
        'low_stock_threshold' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function quotationItems(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function priceTiers(): HasMany
    {
        return $this->hasMany(ProductPriceTier::class)->orderBy('min_quantity');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class, 'product_tag', 'product_id', 'product_tag_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true)->latest();
    }

    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->approvedReviews()->avg('rating');
        return $avg ? round((float) $avg, 1) : null;
    }

    public function getReviewCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereNotNull('low_stock_threshold')
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->where('stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getPriceWithVatAttribute(): float
    {
        return round($this->price * (1 + $this->vat_rate / 100), 4);
    }

    public function getVatAmountAttribute(): float
    {
        return round($this->price * ($this->vat_rate / 100), 4);
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->low_stock_threshold !== null
            && $this->stock > 0
            && $this->stock <= $this->low_stock_threshold;
    }

    /**
     * Resolved SEO title for the current locale — falls back to product name.
     */
    public function getSeoTitleAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->getTranslation('meta_title', $locale, false)
            ?: $this->getTranslation('name', $locale);
    }

    /**
     * Resolved SEO description — falls back to short_description then description.
     */
    public function getSeoDescriptionAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->getTranslation('meta_description', $locale, false)
            ?: $this->getTranslation('short_description', $locale, false)
            ?: mb_substr(strip_tags($this->getTranslation('description', $locale, false) ?? ''), 0, 160);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : null;
    }

    /**
     * All gallery image URLs — falls back to the main thumbnail.
     */
    public function getAllImagesAttribute(): \Illuminate\Support\Collection
    {
        $gallery = $this->images->map(fn($img) => (object)[
            'url' => asset('storage/' . $img->path),
            'alt' => $img->alt ?? $this->getTranslation('name', app()->getLocale()),
        ]);

        if ($gallery->isEmpty() && $this->image) {
            $gallery->push((object)[
                'url' => asset('storage/' . $this->image),
                'alt' => $this->getTranslation('name', app()->getLocale()),
            ]);
        }

        return $gallery;
    }

    /**
     * Resolve the effective price for a given user and quantity,
     * applying price tiers if available.
     */
    public function effectivePrice(?User $user = null, int $quantity = 1): float
    {
        $tier = ProductPriceTier::resolvePrice($this, $quantity, $user);
        return $tier ?? $this->price;
    }

    /**
     * Variant types grouped, e.g. ['size' => ['A4','A3'], 'colour' => ['Red','Blue']]
     */
    public function getVariantTypesAttribute(): array
    {
        return $this->variants
            ->where('is_active', true)
            ->groupBy('type')
            ->map(fn($group) => $group->pluck('value', 'id')->toArray())
            ->toArray();
    }
}
