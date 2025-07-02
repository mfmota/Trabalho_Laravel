<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category; 
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryPizzas = Category::where('name', 'Pizzas')->first();
        $categoryBebidas = Category::where('name', 'Bebidas')->first();

        if ($categoryPizzas) {
            Product::create([
                'name' => 'Pizza Calabresa',
                'price' => '45.50',
                'description' => 'Molho de tomate, muçarela, calabresa e orégano.',
                'banner' => 'path/to/banner-calabresa.jpg',
                'category_id' => $categoryPizzas->id, 
            ]);

            Product::create([
                'name' => 'Pizza 4 Queijos',
                'price' => '55.00',
                'description' => 'Molho de tomate, muçarela, provolone, parmesão e gorgonzola.',
                'banner' => 'path/to/banner-4queijos.jpg',
                'category_id' => $categoryPizzas->id,
            ]);
        }
        
        if ($categoryBebidas) {
            Product::create([
                'name' => 'Coca-Cola 2L',
                'price' => '10.00',
                'description' => 'Refrigerante de cola gelado.',
                'banner' => 'path/to/banner-coca.jpg',
                'category_id' => $categoryBebidas->id,
            ]);
        }
    }
}