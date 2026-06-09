<?php

namespace App\Models;

use Database\Factories\SetorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setor extends Model
{
    /** @use HasFactory<SetorFactory> */
    use HasFactory;

    protected $table = 'setores';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'ativo',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
        ];
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'setor_id');
    }

    public function chamados(): HasMany
    {
        return $this->hasMany(Chamado::class, 'setor_id');
    }
}
