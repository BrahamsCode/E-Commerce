<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AdminUserSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            // SettingsSeeder::class,
            // ProductSeeder::class, // Crear este si quieres productos de prueba
        ]);
    }
}
