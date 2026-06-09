<?php

namespace App\Models;

use App\Enums\ComplexidadeChamadoEnum;
use App\Enums\StatusChamadoEnum;
use Database\Factories\ChamadoFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $protocolo
 * @property string $nome_solicitante
 * @property string $email_solicitante
 * @property string $telefone_solicitante
 * @property string $titulo
 * @property string $descricao
 * @property ComplexidadeChamadoEnum $complexidade
 * @property int $setor_id
 * @property int|null $tecnico_responsavel_id
 * @property StatusChamadoEnum $status
 * @property string|null $token_avaliacao
 * @property Carbon|null $expira_token_avaliacao_em
 * @property Carbon|null $finalizado_em
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Setor $setor
 * @property-read Usuario|null $tecnicoResponsavel
 * @property-read Collection<int, HistoricoChamado> $historicos
 * @property-read AvaliacaoChamado|null $avaliacao
 */
class Chamado extends Model
{
    /** @use HasFactory<ChamadoFactory> */
    use HasFactory;

    protected $table = 'chamados';

    protected $fillable = [
        'protocolo',
        'nome_solicitante',
        'email_solicitante',
        'telefone_solicitante',
        'titulo',
        'descricao',
        'complexidade',
        'setor_id',
        'tecnico_responsavel_id',
        'status',
        'token_avaliacao',
        'expira_token_avaliacao_em',
        'finalizado_em',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'complexidade' => ComplexidadeChamadoEnum::class,
            'status' => StatusChamadoEnum::class,
            'expira_token_avaliacao_em' => 'datetime',
            'finalizado_em' => 'datetime',
        ];
    }

    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class, 'setor_id');
    }

    public function tecnicoResponsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'tecnico_responsavel_id');
    }

    public function historicos(): HasMany
    {
        return $this->hasMany(HistoricoChamado::class, 'chamado_id')->orderByDesc('created_at');
    }

    public function historicosPublicos(): HasMany
    {
        return $this->hasMany(HistoricoChamado::class, 'chamado_id')
            ->where('visivel_solicitante', true)
            ->orderBy('created_at');
    }

    public function avaliacao(): HasOne
    {
        return $this->hasOne(AvaliacaoChamado::class, 'chamado_id');
    }

    public function estaFinalizado(): bool
    {
        return $this->status === StatusChamadoEnum::FINALIZADO;
    }

    public function podeSerAvaliado(): bool
    {
        return $this->estaFinalizado() && $this->avaliacao === null;
    }
}
