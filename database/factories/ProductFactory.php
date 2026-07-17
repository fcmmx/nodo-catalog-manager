<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'sku' => 'SKU-'.strtoupper(Str::random(8)),
            'name' => ucfirst($name),
            'slug' => Product::uniqueSlug(Str::slug($name)),
            'type' => fake()->randomElement(Product::TYPES),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 500, 20000),
            'currency' => 'MXN',
            'availability' => 'disponible',
            'status' => 'borrador',
            'is_featured' => false,
        ];
    }
}
