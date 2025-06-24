<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    private $whatsappNumber;
    private $whatsappApiUrl;
    private $whatsappToken;

    public function __construct()
    {
        $this->whatsappNumber = config('services.whatsapp.number', '51999999999');
        $this->whatsappApiUrl = config('services.whatsapp.api_url');
        $this->whatsappToken = config('services.whatsapp.token');
    }

    public function sendOrder($order)
    {
        if ($order instanceof Order) {
            $message = $this->formatOrderMessage($order);

            // Si tienes WhatsApp Business API
            if ($this->whatsappApiUrl && $this->whatsappToken) {
                return $this->sendViaAPI($order->customer->phone, $message);
            }

            // Si no, generar link para WhatsApp Web
            return $this->generateWhatsAppLink($order->customer->phone, $message);
        }
    }

    private function formatOrderMessage($order)
    {
        $message = "🛒 *NUEVO PEDIDO #{$order->order_number}*\n\n";
        $message .= "👤 *Cliente:* {$order->customer->name}\n";
        $message .= "📱 *Teléfono:* {$order->customer->phone}\n";

        if ($order->customer->email) {
            $message .= "📧 *Email:* {$order->customer->email}\n";
        }

        if ($order->customer->document_number) {
            $message .= "🆔 *{$order->customer->document_type}:* {$order->customer->document_number}\n";
        }

        $message .= "\n📦 *PRODUCTOS:*\n";

        foreach ($order->items as $item) {
            $message .= "▪️ {$item->product_name}\n";
            $message .= "   Cantidad: {$item->quantity} x S/ {$item->unit_price}\n";
            $message .= "   Subtotal: S/ {$item->total_price}\n\n";
        }

        $message .= "💰 *RESUMEN:*\n";
        $message .= "Subtotal: S/ {$order->subtotal}\n";

        if ($order->discount_amount > 0) {
            $message .= "Descuento: -S/ {$order->discount_amount}\n";
        }

        $message .= "IGV (18%): S/ {$order->tax_amount}\n";
        $message .= "*TOTAL: S/ {$order->total_amount}*\n\n";

        $message .= "📍 *DIRECCIÓN DE ENVÍO:*\n";
        $message .= "{$order->shipping_address['address']}\n";

        if (isset($order->shipping_address['reference'])) {
            $message .= "Referencia: {$order->shipping_address['reference']}\n";
        }

        if ($order->notes) {
            $message .= "\n📝 *NOTAS:*\n{$order->notes}\n";
        }

        $message .= "\n⏰ Fecha: " . $order->created_at->format('d/m/Y H:i');

        return $message;
    }

    private function sendViaAPI($phone, $message)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->whatsappToken,
                'Content-Type' => 'application/json',
            ])->post($this->whatsappApiUrl . '/messages', [
                'messaging_product' => 'whatsapp',
                'to' => '51' . ltrim($phone, '51'),
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => $response->json()['messages'][0]['id'] ?? null
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['error']['message'] ?? 'Error al enviar mensaje'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function generateWhatsAppLink($phone, $message)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (!str_starts_with($phone, '51')) {
            $phone = '51' . $phone;
        }

        $encodedMessage = urlencode($message);

        return "https://wa.me/{$phone}?text={$encodedMessage}";
    }
}
