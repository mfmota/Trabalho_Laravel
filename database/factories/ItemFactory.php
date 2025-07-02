<?php

namespace Database\Factories;

use App\Models\Item; 
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'amount' => fake()->numberBetween(1, 5),
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
        ];
    }
}