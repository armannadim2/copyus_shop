<?php

namespace App\Services;

/**
 * Immutable value object returned by PrintPriceCalculator.
 */
readonly class PrintPriceResult
{
    public function __construct(
        public float  $unitPrice,
        public float  $totalPrice,
        public float  $tierDiscountPercent,
        public int    $productionDays,
        public array  $breakdown,   // [['label' => string, 'delta' => float], ...]
    ) {}

    public function toArray(): array
    {
        return [
            'unit_price'           => round($this->unitPrice, 4),
            'total_price'          => round($this->totalPrice, 4),
            'tier_discount_percent'=> $this->tierDiscountPercent,
            'production_days'      => $this->productionDays,
            'breakdown'            => $this->breakdown,
        ];
    }
}
