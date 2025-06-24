<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Electrónica',
                'slug' => 'electronica',
                'description' => 'Productos electrónicos y tecnología',
                'children' => [
                    ['name' => 'Celulares', 'slug' => 'celulares'],
                    ['name' => 'Laptops', 'slug' => 'laptops'],
                    ['name' => 'Accesorios', 'slug' => 'accesorios-electronicos'],
                ]
            ],
            [
                'name' => 'Moda',
                'slug' => 'moda',
                'description' => 'Ropa y accesorios de moda',
                'children' => [
                    ['name' => 'Ropa Hombre', 'slug' => 'ropa-hombre'],
                    ['name' => 'Ropa Mujer', 'slug' => 'ropa-mujer'],
                    ['name' => 'Calzado', 'slug' => 'calzado'],
                ]
            ],
            [
                'name' => 'Hogar',
                'slug' => 'hogar',
                'description' => 'Productos para el hogar',
                'children' => [
                    ['name' => 'Muebles', 'slug' => 'muebles'],
                    ['name' => 'Decoración', 'slug' => 'decoracion'],
                    ['name' => 'Cocina', 'slug' => 'cocina'],
                ]
            ],
        ];

        foreach ($categories as $index => $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = Category::create(array_merge($categoryData, [
                'is_active' => true,
                'sort_order' => $index
            ]));

            foreach ($children as $childIndex => $childData) {
                Category::create(array_merge($childData, [
                    'parent_id' => $category->id,
                    'is_active' => true,
                    'sort_order' => $childIndex
                ]));
            }
        }
    }
}
