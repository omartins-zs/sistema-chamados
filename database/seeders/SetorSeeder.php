<?php

namespace Database\Seeders;

use App\Models\Setor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SetorSeeder extends Seeder
{
    public function run(): void
    {
        $setores = [
            'Gerência de TI',
            'Desenvolvimento',
            'Telefonia/CFTV',
            'Suporte Técnico/Infra',
        ];

        foreach ($setores as $nome) {
            Setor::query()->updateOrCreate(
                ['slug' => Str::slug($nome)],
                [
                    'nome' => $nome,
                    'descricao' => "Setor responsável por {$nome}.",
                    'ativo' => true,
                ]
            );
        }
    }
}
