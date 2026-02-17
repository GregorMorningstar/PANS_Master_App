<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ItemsFinishedGood;

class ItemsFinishedGoodFactory extends Factory
{
    protected $model = ItemsFinishedGood::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'image_path' => null,
            'barcode' => null,
            // production time expressed as minutes (1..1440)
            'time_of_production' => $this->faker->numberBetween(1, 1440),
            'price' => $this->faker->randomFloat(2, 1, 500),
            'stock' => $this->faker->numberBetween(0, 200),
        ];
    }
}
