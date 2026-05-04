<?php
$header = ["\xEF\xBB\xBFsku;category_slug;brand;name_ca;name_es;name_en;short_description_ca;short_description_es;short_description_en;description_ca;description_es;description_en;price;vat_rate;stock;min_order_quantity;unit;is_active;is_featured;low_stock_threshold"];
$header = array_map(fn($h) => trim((string) $h), $header);
if (count($header) === 1 && str_contains($header[0], ';')) {
    $header = explode(';', $header[0]);
}
$header[0] = ltrim($header[0], "\xEF\xBB\xBF");
$header = array_map('trim', $header);
$required = ['sku', 'category_slug', 'brand', 'name_ca', 'name_es', 'price', 'vat_rate', 'stock', 'min_order_quantity', 'unit'];
var_dump($header);
var_dump($required);
var_dump(array_diff($required, $header));
