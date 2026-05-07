<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Global Price Visibility
    |--------------------------------------------------------------------------
    |
    | Set SHOW_PRICES=false in your .env to hide all prices site-wide —
    | on product pages, print templates, search results, wishlist, and the
    | navbar mini-cart. Admin views are never affected.
    | Per-user canSeePrices() checks still apply when this is true.
    |
    */

    'show_prices' => env('SHOW_PRICES', true),

];
