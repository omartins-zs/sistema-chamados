<?php

namespace Database\Factories;

use App\Enums\StatusChamadoEnum;
use App\Models\Chamado;
use App\Models\HistoricoChamado;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HistoricoChamado>
 */
class HistoricoChamadoFactory extends Factory
{
    protected $model = HistoricoChamado::class;

    public function definition(): array
    {
        return [
            'chamado_id' => Chamado::factory(),
            'tecnico_id' => Usuario::factory()->tecnico(),
            'status' => StatusChamadoEnum::EM_ANDAMENTO,
            'descricao' => fake()->sentence(),
            'visivel_solicitante' => false,
        ];
    }
}
