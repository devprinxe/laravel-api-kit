<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\VariationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Variation>
 */
class VariationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'variation_type_id' => VariationType::factory(),
            'title' => $this->faker->word(),
            'description' => $this->faker->sentence(),
        ];
    }
}
