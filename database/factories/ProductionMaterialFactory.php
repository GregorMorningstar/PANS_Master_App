<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductionMaterial;
use App\Models\Operationmachine;
use App\Enums\MaterialGroup;
use App\Enums\MaterialForm;

class ProductionMaterialFactory extends Factory
{
    protected $model = ProductionMaterial::class;

    public function definition()
    {
        $shortNames = [
            'Profil', 'Krzyżak', 'Rama', 'Pianka', 'Siedzisko', 'Kolumna', 'Tilt', 'Siatka', 'Nici', 'Zestaw', 'Belka', 'Granulat'
        ];

        return [
            // minimalne krótkie nazwy pasujące do operacji produkcyjnych
            'name' => $this->faker->randomElement($shortNames) . ' ' . $this->faker->numberBetween(1, 99),
            'description' => $this->faker->optional()->word(),
            'barcode' => null,
            'group_material' => $this->faker->randomElement(MaterialGroup::values()),
            'material_form' => $this->faker->randomElement(MaterialForm::values()),
            'stock_empty_alarm' => $this->faker->numberBetween(0, 20),
            'available_quantity' => $this->faker->randomFloat(2, 0, 200),
        ];
    }

    public function configure()
    {
        $faker = $this->faker;

        return $this->afterCreating(function (ProductionMaterial $material) use ($faker) {
            // Prefer to attach existing operationmachines so materials match real operations.
            $count = $faker->numberBetween(1, 2);

            $ops = Operationmachine::inRandomOrder()->limit($count)->get();

            // If no existing operations, create minimal ones as fallback
            if ($ops->isEmpty()) {
                $ops = Operationmachine::factory()->count($count)->create([
                    'production_material_id' => $material->id,
                ]);
            }

            foreach ($ops as $op) {
                $material->operations()->attach($op->id, [
                    'quantity' => $faker->randomFloat(2, 0.1, 20),
                    'unit' => $faker->randomElement(['pcs', 'kg', 'm']),
                ]);
            }
        });
    }
}
