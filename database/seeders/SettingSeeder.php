<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Mi Tienda Online',
                'type' => 'string',
                'description' => 'Nombre del sitio web'
            ],
            [
                'key' => 'site_description',
                'value' => 'Tu tienda online de confianza en Perú',
                'type' => 'text',
                'description' => 'Descripción del sitio'
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@mitienda.pe',
                'type' => 'string',
                'description' => 'Email de contacto'
            ],
            [
                'key' => 'contact_phone',
                'value' => '51999999999',
                'type' => 'string',
                'description' => 'Teléfono de contacto'
            ],
            [
                'key' => 'whatsapp_number',
                'value' => '51999999999',
                'type' => 'string',
                'description' => 'Número de WhatsApp'
            ],
            [
                'key' => 'shipping_cost',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Costo de envío en Lima (S/)'
            ],
            [
                'key' => 'free_shipping_amount',
                'value' => '150',
                'type' => 'integer',
                'description' => 'Monto mínimo para envío gratis (S/)'
            ],
            [
                'key' => 'business_hours',
                'value' => json_encode([
                    'monday' => '9:00 AM - 6:00 PM',
                    'tuesday' => '9:00 AM - 6:00 PM',
                    'wednesday' => '9:00 AM - 6:00 PM',
                    'thursday' => '9:00 AM - 6:00 PM',
                    'friday' => '9:00 AM - 6:00 PM',
                    'saturday' => '9:00 AM - 2:00 PM',
                    'sunday' => 'Cerrado'
                ]),
                'type' => 'json',
                'description' => 'Horario de atención'
            ],
            [
                'key' => 'social_media',
                'value' => json_encode([
                    'facebook' => 'https://facebook.com/mitienda',
                    'instagram' => 'https://instagram.com/mitienda',
                    'whatsapp' => '51999999999'
                ]),
                'type' => 'json',
                'description' => 'Redes sociales'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
