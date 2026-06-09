<?php

namespace Database\Factories;

use App\Enums\ComplexidadeChamadoEnum;
use App\Enums\StatusChamadoEnum;
use App\Models\Chamado;
use App\Models\Setor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Chamado>
 */
class ChamadoFactory extends Factory
{
    protected $model = Chamado::class;

    public function definition(): array
    {
        return [
            'protocolo' => 'CHM-'.now()->year.'-'.str_pad((string) fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'nome_solicitante' => fake()->name(),
            'email_solicitante' => fake()->safeEmail(),
            'telefone_solicitante' => fake()->phoneNumber(),
            'titulo' => fake()->sentence(4),
            'descricao' => fake()->paragraph(),
            'complexidade' => fake()->randomElement(ComplexidadeChamadoEnum::cases()),
            'setor_id' => Setor::factory(),
            'tecnico_responsavel_id' => null,
            'status' => StatusChamadoEnum::EM_ABERTO,
            'token_avaliacao' => null,
            'expira_token_avaliacao_em' => null,
            'finalizado_em' => null,
        ];
    }

    public function finalizado(): static
    {
        return $this->state(fn (): array => [
            'status' => StatusChamadoEnum::FINALIZADO,
            'finalizado_em' => now(),
            'token_avaliacao' => fake()->sha256(),
            'expira_token_avaliacao_em' => now()->addDays(30),
        ]);
    }
}
