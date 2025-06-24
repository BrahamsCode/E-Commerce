<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $total = 0;
        $items = [];

        foreach ($cart as $id => $item) {
            if (str_contains($id, 'variant_')) {
                $variantId = str_replace('variant_', '', $id);
                $variant = ProductVariant::with('product')->find($variantId);
                if ($variant) {
                    $items[] = [
                        'id' => $id,
                        'product' => $variant->product,
                        'variant' => $variant,
                        'name' => $variant->product->name . ' - ' . $variant->name,
                        'price' => $variant->price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $variant->price * $item['quantity']
                    ];
                    $total += $variant->price * $item['quantity'];
                }
            } else {
                $product = Product::find($id);
                if ($product) {
                    $items[] = [
                        'id' => $id,
                        'product' => $product,
                        'variant' => null,
                        'name' => $product->name,
                        'price' => $product->price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $product->price * $item['quantity']
                    ];
                    $total += $product->price * $item['quantity'];
                }
            }
        }

        return view('frontend.cart.index', compact('items', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required|integer|min:1',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        $cart = session('cart', []);
        $id = $request->variant_id ? 'variant_' . $request->variant_id : $request->product_id;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $request->quantity;
        } else {
            $cart[$id] = [
                'quantity' => $request->quantity,
                'added_at' => now()
            ];
        }

        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'count' => count($cart)
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = session('cart', []);

        if (isset($cart[$request->id])) {
            $cart[$request->id]['quantity'] = $request->quantity;
            session(['cart' => $cart]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Carrito actualizado'
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $cart = session('cart', []);

        if (isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session(['cart' => $cart]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado del carrito'
        ]);
    }

    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Carrito vaciado');
    }

    public function count()
    {
        $cart = session('cart', []);
        $count = 0;

        foreach ($cart as $item) {
            $count += $item['quantity'];
        }

        return response()->json(['count' => $count]);
    }
}
