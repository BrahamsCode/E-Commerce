<?php
return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la integración con WhatsApp
    |
    */

    // Número de WhatsApp del negocio (sin el +)
    'business_number' => env('WHATSAPP_NUMBER', '51918887860'),

    // Configuración de la API (si usas WhatsApp Business API)
    'api' => [
        'url' => env('WHATSAPP_API_URL'),
        'token' => env('WHATSAPP_TOKEN'),
        'webhook_token' => env('WHATSAPP_WEBHOOK_TOKEN'),
    ],

    // Mensajes predefinidos
    'messages' => [
        'order_received' => '¡Hola! Hemos recibido tu pedido #:order_number. En breve nos comunicaremos contigo para coordinar el pago y envío.',
        'order_confirmed' => 'Tu pedido #:order_number ha sido confirmado. Procederemos con la preparación.',
        'order_shipped' => 'Tu pedido #:order_number ha sido enviado. Llegará en las próximas 24-48 horas.',
        'order_delivered' => '¡Tu pedido #:order_number ha sido entregado! Gracias por tu compra.',
    ],

    // Configuración de envío automático
    'auto_send' => [
        'new_orders' => true,
        'status_updates' => true,
    ],
];
