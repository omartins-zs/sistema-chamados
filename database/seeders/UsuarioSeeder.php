<?php

namespace Database\Seeders;

use App\Enums\TipoUsuarioEnum;
use App\Models\Setor;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::query()->updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'nome' => 'Administrador',
                'senha' => Hash::make('password'),
                'tipo_usuario' => TipoUsuarioEnum::ADMINISTRADOR,
                'setor_id' => null,
                'ativo' => true,
            ]
        );

        $tecnicos = [
            'Gerência de TI' => ['Carlos Almeida', 'Fernanda Souza', 'Renato Lima'],
            'Desenvolvimento' => ['Lucas Martins', 'Ana Beatriz', 'Rafael Oliveira'],
            'Telefonia/CFTV' => ['Jorge Santos', 'Camila Rocha', 'Diego Pereira'],
            'Suporte Técnico/Infra' => ['Marcos Silva', 'Patrícia Gomes', 'Bruno Henrique'],
        ];

        foreach ($tecnicos as $setorNome => $nomes) {
            $setor = Setor::query()->where('nome', $setorNome)->firstOrFail();

            foreach ($nomes as $nome) {
                $slug = Str::slug($nome);

                Usuario::query()->updateOrCreate(
                    ['email' => "{$slug}@chamados.local"],
                    [
                        'nome' => $nome,
                        'senha' => Hash::make('password'),
                        'tipo_usuario' => TipoUsuarioEnum::TECNICO,
                        'setor_id' => $setor->id,
                        'ativo' => true,
                    ]
                );
            }
        }
    }
}
