<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class HomeController extends Controller
{
    public function index()
    {
        // Productos destacados
        $featuredProducts = Product::active()
            ->featured()
            ->with('primaryImage')
            ->inRandomOrder()
            ->limit(8)
            ->get();

        // Nuevos productos
        $newProducts = Product::active()
            ->with('primaryImage')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Productos en oferta
        $saleProducts = Product::active()
            ->with('primaryImage')
            ->whereNotNull('compare_price')
            ->whereColumn('compare_price', '>', 'price')
            ->inRandomOrder()
            ->limit(8)
            ->get();

        // Categorías principales
        $mainCategories = Category::active()
            ->roots()
            ->withCount('activeProducts')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        // Marcas populares
        // $popularBrands = Brand::active()
        //     ->withCount('activeProducts')
        //     ->having('active_products_count', '>', 0)
        //     ->orderBy('active_products_count', 'desc')
        //     ->limit(8)
        //     ->get();

        return view('frontend.home.index', compact(
            'featuredProducts',
            'newProducts',
            'saleProducts',
            'mainCategories',
            // 'popularBrands'
        ));
    }
}
