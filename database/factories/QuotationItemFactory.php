<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotationItemFactory extends Factory
{
    protected $model = QuotationItem::class;

    public function definition(): array
    {
        $qty       = fake()->numberBetween(1, 20);
        $unitPrice = fake()->randomFloat(4, 1, 200);

        return [
            'quotation_id'     => Quotation::factory(),
            'product_id'       => Product::factory(),
            'product_snapshot' => [
                'name' => 'Test Product',
                'sku'  => 'SKU-0001',
                'unit' => 'unit',
            ],
            'quantity'         => $qty,
            'unit_price'       => $unitPrice,
            'quoted_price'     => null,
            'vat_rate'         => 21.00,
            'vat_amount'       => null,
            'total'            => null,
            'notes'            => null,
        ];
    }

    public function withQuotedPrice(float $price): static
    {
        return $this->state(fn() => ['quoted_price' => $price]);
    }
}
