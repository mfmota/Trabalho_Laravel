<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category; 

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'Pizzas']);
        Category::create(['name' => 'Bebidas']);
        Category::create(['name' => 'Sobremesas']);
        Category::create(['name' => 'Porções']);
    }
}