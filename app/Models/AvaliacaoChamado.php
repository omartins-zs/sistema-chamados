<?php

namespace App\Models;

use Database\Factories\AvaliacaoChamadoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $chamado_id
 * @property int $nota_satisfacao
 * @property int $nota_tempo_resolucao
 * @property string|null $comentario
 * @property-read Chamado $chamado
 */
class AvaliacaoChamado extends Model
{
    /** @use HasFactory<AvaliacaoChamadoFactory> */
    use HasFactory;

    protected $table = 'avaliacoes_chamados';

    protected $fillable = [
        'chamado_id',
        'nota_satisfacao',
        'nota_tempo_resolucao',
        'comentario',
    ];

    public function chamado(): BelongsTo
    {
        return $this->belongsTo(Chamado::class, 'chamado_id');
    }
}
