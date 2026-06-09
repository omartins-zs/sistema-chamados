<?php

namespace Database\Factories;

use App\Models\AvaliacaoChamado;
use App\Models\Chamado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AvaliacaoChamado>
 */
class AvaliacaoChamadoFactory extends Factory
{
    protected $model = AvaliacaoChamado::class;

    public function definition(): array
    {
        return [
            'chamado_id' => Chamado::factory()->finalizado(),
            'nota_satisfacao' => fake()->numberBetween(1, 5),
            'nota_tempo_resolucao' => fake()->numberBetween(1, 5),
            'comentario' => fake()->optional()->sentence(),
        ];
    }
}
