<?php

namespace Database\Factories;

use App\Models\Registro;
use App\Models\Abono;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Registro>
 */
class RegistroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $precioUnitario = $this->faker->numberBetween(10000, 50000);
        $cantidad = $this->faker->numberBetween(1, 5);
        $valorTotal = $precioUnitario * $cantidad;

        return [
            'nombre' => $this->faker->name(),
            'celular' => $this->faker->numerify('3#########'), // Ej: 3191234567
            'descripcion' => $this->faker->word(),
            'valor_unitario' => $precioUnitario,
            'valor_total' => $valorTotal,
            'cantidad' => $cantidad,
            'id_estado' => $this->faker->randomElement([1, 2]),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Registro $registro) {
            //Solo si el estado es 2 (crédito), creamos un abono
            if ($registro->id_estado == 2) {
                $registro->abonos()->create([
                    'valor' => fake()->numberBetween(1000, $registro->valor_total),
                ]);
            }
        });
    }
}
