<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
        }

        $items = [];
        $subtotal = 0;

        foreach ($cart as $id => $item) {
            if (str_contains($id, 'variant_')) {
                $variantId = str_replace('variant_', '', $id);
                $variant = ProductVariant::with('product')->find($variantId);
                if ($variant) {
                    $items[] = [
                        'type' => 'variant',
                        'product' => $variant->product,
                        'variant' => $variant,
                        'quantity' => $item['quantity'],
                        'price' => $variant->price,
                        'subtotal' => $variant->price * $item['quantity']
                    ];
                    $subtotal += $variant->price * $item['quantity'];
                }
            } else {
                $product = Product::find($id);
                if ($product) {
                    $items[] = [
                        'type' => 'product',
                        'product' => $product,
                        'variant' => null,
                        'quantity' => $item['quantity'],
                        'price' => $product->price,
                        'subtotal' => $product->price * $item['quantity']
                    ];
                    $subtotal += $product->price * $item['quantity'];
                }
            }
        }

        // Calcular IGV (18% en Perú)
        $tax_rate = 0.18;
        $tax_amount = $subtotal * $tax_rate;
        $total = $subtotal + $tax_amount;

        return view('frontend.checkout.index', compact('items', 'subtotal', 'tax_amount', 'total'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'document_type' => 'nullable|in:dni,ruc,ce,passport',
            'document_number' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'company_ruc' => 'nullable|string|max:20',
            'billing_address' => 'required|string',
            'shipping_address' => 'required|string',
            'notes' => 'nullable|string',
            'coupon_code' => 'nullable|string'
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
        }

        DB::beginTransaction();

        try {
            // Crear o actualizar cliente
            $customer = Customer::updateOrCreate(
                ['phone' => $request->phone],
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'document_type' => $request->document_type,
                    'document_number' => $request->document_number,
                    'company_name' => $request->company_name,
                    'company_ruc' => $request->company_ruc,
                    'billing_address' => $request->billing_address,
                    'shipping_address' => $request->shipping_address,
                ]
            );

            // Calcular totales
            $subtotal = 0;
            $items = [];

            foreach ($cart as $id => $item) {
                if (str_contains($id, 'variant_')) {
                    $variantId = str_replace('variant_', '', $id);
                    $variant = ProductVariant::with('product')->find($variantId);
                    if ($variant && $variant->stock >= $item['quantity']) {
                        $items[] = [
                            'product' => $variant->product,
                            'variant' => $variant,
                            'quantity' => $item['quantity'],
                            'price' => $variant->price
                        ];
                        $subtotal += $variant->price * $item['quantity'];
                    } else {
                        throw new \Exception('Producto sin stock: ' . $variant->product->name);
                    }
                } else {
                    $product = Product::find($id);
                    if ($product && $product->stock >= $item['quantity']) {
                        $items[] = [
                            'product' => $product,
                            'variant' => null,
                            'quantity' => $item['quantity'],
                            'price' => $product->price
                        ];
                        $subtotal += $product->price * $item['quantity'];
                    } else {
                        throw new \Exception('Producto sin stock: ' . $product->name);
                    }
                }
            }

            // Aplicar cupón si existe
            $discount_amount = 0;
            $coupon = null;

            if ($request->coupon_code) {
                $coupon = Coupon::where('code', $request->coupon_code)
                    ->where('is_active', true)
                    ->where('valid_from', '<=', now())
                    ->where('valid_until', '>=', now())
                    ->first();

                if ($coupon && $coupon->usage_limit > $coupon->used_count) {
                    if ($coupon->minimum_amount && $subtotal < $coupon->minimum_amount) {
                        throw new \Exception('El cupón requiere un monto mínimo de S/ ' . $coupon->minimum_amount);
                    }

                    if ($coupon->type === 'percentage') {
                        $discount_amount = $subtotal * ($coupon->value / 100);
                    } else {
                        $discount_amount = min($coupon->value, $subtotal);
                    }
                }
            }

            // Calcular IGV
            $tax_amount = ($subtotal - $discount_amount) * 0.18;
            $total_amount = $subtotal - $discount_amount + $tax_amount;

            // Crear orden
            $order = Order::create([
                'customer_id' => $customer->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $tax_amount,
                'discount_amount' => $discount_amount,
                'total_amount' => $total_amount,
                'customer_data' => $customer->toArray(),
                'billing_address' => [
                    'address' => $request->billing_address,
                    'reference' => $request->billing_reference ?? null
                ],
                'shipping_address' => [
                    'address' => $request->shipping_address,
                    'reference' => $request->shipping_reference ?? null
                ],
                'notes' => $request->notes,
                'created_by' => auth()->id()
            ]);

            // Crear items de la orden
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_variant_id' => $item['variant']?->id,
                    'product_name' => $item['product']->name . ($item['variant'] ? ' - ' . $item['variant']->name : ''),
                    'product_sku' => $item['variant']?->sku ?? $item['product']->sku,
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['price'] * $item['quantity'],
                    'product_data' => [
                        'product' => $item['product']->toArray(),
                        'variant' => $item['variant']?->toArray()
                    ]
                ]);

                // Actualizar stock
                if ($item['variant']) {
                    $item['variant']->decrement('stock', $item['quantity']);
                } else {
                    $item['product']->decrement('stock', $item['quantity']);
                }
            }

            // Registrar uso del cupón
            if ($coupon) {
                $coupon->increment('used_count');
                $order->couponUsages()->create([
                    'coupon_id' => $coupon->id,
                    'customer_id' => $customer->id,
                    'discount_amount' => $discount_amount
                ]);
            }

            // Limpiar carrito
            session()->forget('cart');

            DB::commit();

            // Enviar a WhatsApp
            $this->sendOrderToWhatsApp($order);

            return redirect()->route('checkout.confirmation', $order)
                ->with('success', 'Tu pedido ha sido procesado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    private function sendOrderToWhatsApp($order)
    {
        $whatsappController = new WhatsAppController();
        $whatsappController->sendOrder($order);
    }

    public function confirmation(Order $order)
    {
        return view('frontend.checkout.confirmation', compact('order'));
    }
}
