<?php

namespace Database\Factories;

use App\Enum\ProductStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'regularPrice' => $this->faker->randomFloat(2, 10, 100),
            'salePrice' => $this->faker->randomFloat(2, 5, 90),
            'sku' => $this->faker->unique()->bothify('SKU-####'),
            'barcode' => $this->faker->unique()->ean13(),
            'featuredImage' => $this->faker->imageUrl(),
            'status' => $this->faker->randomElement(array_column(ProductStatus::cases(), 'name')),
        ];
    }
}
