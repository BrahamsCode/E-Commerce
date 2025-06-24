<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['primaryImage', 'category', 'brand'])
            ->active();

        // Filtros
        if ($request->has('categoria')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->categoria);
            });
        }

        if ($request->has('marca')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->marca);
            });
        }

        if ($request->has('precio_min')) {
            $query->where('price', '>=', $request->precio_min);
        }

        if ($request->has('precio_max')) {
            $query->where('price', '<=', $request->precio_max);
        }

        if ($request->has('en_stock')) {
            $query->inStock();
        }

        // Ordenamiento
        switch ($request->get('ordenar', 'relevancia')) {
            case 'precio_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'precio_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'nombre':
                $query->orderBy('name', 'asc');
                break;
            case 'nuevo':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('is_featured', 'desc')
                    ->orderBy('sort_order', 'asc');
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->roots()->with('children')->get();
        $brands = Brand::active()->orderBy('name')->get();

        return view('frontend.products.index', compact('products', 'categories', 'brands'));
    }

    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->load(['images', 'category', 'brand', 'variants']);

        // Productos relacionados
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        if (!$query) {
            return redirect()->route('products.index');
        }

        $products = Product::active()
            ->with(['primaryImage', 'category', 'brand'])
            ->search($query)
            ->paginate(12)
            ->withQueryString();

        return view('frontend.products.search', compact('products', 'query'));
    }

    public function category(Category $category)
    {
        if (!$category->is_active) {
            abort(404);
        }

        $products = Product::active()
            ->with(['primaryImage', 'brand'])
            ->where('category_id', $category->id)
            ->orderBy('sort_order')
            ->paginate(12);

        return view('frontend.products.category', compact('category', 'products'));
    }

    public function brand(Brand $brand)
    {
        if (!$brand->is_active) {
            abort(404);
        }

        $products = Product::active()
            ->with(['primaryImage', 'category'])
            ->where('brand_id', $brand->id)
            ->orderBy('sort_order')
            ->paginate(12);

        return view('frontend.products.brand', compact('brand', 'products'));
    }
}
