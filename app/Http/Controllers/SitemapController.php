<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PrintTemplate;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $products = Product::where('is_active', true)
            ->select('slug', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->get();

        $categories = Category::where('is_active', true)
            ->select('slug', 'updated_at')
            ->get();

        $printTemplates = PrintTemplate::where('is_active', true)
            ->select('slug', 'updated_at')
            ->orderBy('sort_order')
            ->get();

        return response()
            ->view('sitemap', compact('products', 'categories', 'printTemplates'))
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
