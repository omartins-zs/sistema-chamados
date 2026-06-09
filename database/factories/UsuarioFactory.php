<?php

namespace Database\Factories;

use App\Enums\TipoUsuarioEnum;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition(): array
    {
        return [
            'setor_id' => Setor::factory(),
            'nome' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'senha' => 'password',
            'tipo_usuario' => TipoUsuarioEnum::TECNICO,
            'ativo' => true,
        ];
    }

    public function administrador(): static
    {
        return $this->state(fn (): array => [
            'tipo_usuario' => TipoUsuarioEnum::ADMINISTRADOR,
            'setor_id' => null,
        ]);
    }

    public function tecnico(): static
    {
        return $this->state(fn (): array => [
            'tipo_usuario' => TipoUsuarioEnum::TECNICO,
        ]);
    }
}
