<?php

namespace App\Models;

use App\Enums\TipoUsuarioEnum;
use Database\Factories\UsuarioFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property int|null $setor_id
 * @property string $nome
 * @property string $email
 * @property string $senha
 * @property TipoUsuarioEnum $tipo_usuario
 * @property bool $ativo
 * @property-read Setor|null $setor
 */
class Usuario extends Authenticatable implements CanResetPasswordContract, FilamentUser, HasName
{
    /** @use HasFactory<UsuarioFactory> */
    use CanResetPassword, HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'setor_id',
        'nome',
        'email',
        'senha',
        'tipo_usuario',
        'ativo',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'senha' => 'hashed',
            'tipo_usuario' => TipoUsuarioEnum::class,
            'ativo' => 'boolean',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'senha';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->ativo;
    }

    public function getFilamentName(): string
    {
        return $this->nome;
    }

    public function ehAdministrador(): bool
    {
        return $this->tipo_usuario === TipoUsuarioEnum::ADMINISTRADOR;
    }

    public function ehTecnico(): bool
    {
        return $this->tipo_usuario === TipoUsuarioEnum::TECNICO;
    }

    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class, 'setor_id');
    }

    public function chamadosResponsaveis(): HasMany
    {
        return $this->hasMany(Chamado::class, 'tecnico_responsavel_id');
    }

    public function historicos(): HasMany
    {
        return $this->hasMany(HistoricoChamado::class, 'tecnico_id');
    }
}
