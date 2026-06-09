<?php

namespace App\Models;

use App\Enums\StatusChamadoEnum;
use Database\Factories\HistoricoChamadoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $chamado_id
 * @property int $tecnico_id
 * @property StatusChamadoEnum $status
 * @property string $descricao
 * @property bool $visivel_solicitante
 * @property Carbon|null $created_at
 * @property-read Chamado $chamado
 * @property-read Usuario $tecnico
 */
class HistoricoChamado extends Model
{
    /** @use HasFactory<HistoricoChamadoFactory> */
    use HasFactory;

    protected $table = 'historicos_chamados';

    protected $fillable = [
        'chamado_id',
        'tecnico_id',
        'status',
        'descricao',
        'visivel_solicitante',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => StatusChamadoEnum::class,
            'visivel_solicitante' => 'boolean',
        ];
    }

    public function chamado(): BelongsTo
    {
        return $this->belongsTo(Chamado::class, 'chamado_id');
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'tecnico_id');
    }
}
