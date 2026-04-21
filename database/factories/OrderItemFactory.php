<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $product   = Product::factory()->make();
        $qty       = fake()->numberBetween(1, 10);
        $unitPrice = fake()->randomFloat(4, 1, 200);
        $vatRate   = 21.00;
        $subtotal  = round($unitPrice * $qty, 4);
        $vatAmount = round($subtotal * ($vatRate / 100), 4);
        $total     = round($subtotal + $vatAmount, 4);

        return [
            'order_id'         => Order::factory(),
            'product_id'       => Product::factory(),
            'product_snapshot' => [
                'name' => 'Test Product',
                'sku'  => 'SKU-0001',
                'unit' => 'unit',
            ],
            'quantity'         => $qty,
            'unit_price'       => $unitPrice,
            'vat_rate'         => $vatRate,
            'vat_amount'       => $vatAmount,
            'subtotal'         => $subtotal,
            'total'            => $total,
        ];
    }
}
