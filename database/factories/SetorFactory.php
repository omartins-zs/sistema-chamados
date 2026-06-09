<?php

namespace Database\Factories;

use App\Models\Setor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Setor>
 */
class SetorFactory extends Factory
{
    protected $model = Setor::class;

    public function definition(): array
    {
        $nome = fake()->unique()->company();

        return [
            'nome' => $nome,
            'slug' => Str::slug($nome),
            'descricao' => fake()->sentence(),
            'ativo' => true,
        ];
    }
}
