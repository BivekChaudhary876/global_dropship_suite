<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.Str::random(6),
            'description' => $this->faker->sentence(15),
            'price' => $this->faker->randomFloat(2, 5, 200),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
        ];
    }
}