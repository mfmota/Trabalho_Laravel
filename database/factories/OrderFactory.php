<?php

namespace Database\Factories;

use App\Models\Order; 
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'table' => fake()->numberBetween(1, 50),
            'status' => false,
            'draft' => true,
            'name' => fake()->optional()->name(),
        ];
    }
}