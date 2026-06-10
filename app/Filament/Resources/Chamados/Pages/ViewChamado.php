<?php

namespace App\Filament\Resources\Chamados\Pages;

use App\Enums\StatusChamadoEnum;
use App\Filament\Resources\Chamados\ChamadoResource;
use App\Filament\Support\FinalizarChamadoFormulario;
use App\Models\Chamado;
use App\Services\ChamadoService;
use App\Services\HistoricoChamadoService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class ViewChamado extends ViewRecord
{
    protected static string $resource = ChamadoResource::class;

    protected static ?string $breadcrumb = '';

    protected string $view = 'filament.chamados.visualizar';

    public function getRecord(): Chamado
    {
        /** @var Chamado $record */
        $record = parent::getRecord();

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Editar')
                ->visible(fn (): bool => auth()->user()->can('update', $this->getRecord())),
            Action::make('relatorioPdf')
                ->label('PDF')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->color('gray')
                ->url(fn (): string => route('filament.admin.chamados.relatorio-pdf-individual', $this->getRecord())),
            DeleteAction::make()
                ->label('Excluir')
                ->visible(fn (): bool => auth()->user()->can('delete', $this->getRecord())),
            Action::make('assumir')
                ->label('Assumir Chamado')
                ->icon(Heroicon::OutlinedHandRaised)
                ->color('primary')
                ->visible(fn (): bool => $this->getRecord()->tecnico_responsavel_id === null
                    && auth()->user()->can('assumir', $this->getRecord()))
                ->action(function (): void {
                    app(HistoricoChamadoService::class)->assumirChamado($this->getRecord(), auth()->user());
                    $this->recarregarChamado();
                }),
            Action::make('adicionarHistorico')
                ->label('Adicionar Histórico')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('info')
                ->visible(fn (): bool => auth()->user()->can('adicionarHistorico', $this->getRecord()))
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options(StatusChamadoEnum::opcoes())
                        ->required(),
                    Textarea::make('descricao')
                        ->label('Descrição')
                        ->required()
                        ->rows(4),
                    Toggle::make('visivel_solicitante')
                        ->label('Visível ao solicitante')
                        ->default(false),
                ])
                ->action(function (array $data): void {
                    app(HistoricoChamadoService::class)->adicionar($this->getRecord(), auth()->user(), $data);
                    $this->recarregarChamado();
                }),
            Action::make('finalizar')
                ->label('Finalizar Chamado')
                ->icon(Heroicon::OutlinedCheckCircle)
                ->color('success')
                ->modalHeading('Finalizar chamado')
                ->modalDescription('Informe o motivo e o texto da finalização. O registro será adicionado ao histórico e o solicitante receberá o link de avaliação.')
                ->schema(FinalizarChamadoFormulario::campos())
                ->visible(fn (): bool => $this->getRecord()->status !== StatusChamadoEnum::FINALIZADO
                    && auth()->user()->can('finalizar', $this->getRecord()))
                ->action(function (array $data): void {
                    app(ChamadoService::class)->finalizar($this->getRecord(), auth()->user(), $data);
                    $this->recarregarChamado();
                }),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->titulo;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->getRecord()->protocolo;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $chamado = $this->getRecord()->load(['setor', 'tecnicoResponsavel', 'avaliacao']);

        $historicos = $chamado->historicos()
            ->with(['tecnico.setor'])
            ->orderBy('created_at')
            ->get();

        return [
            'chamado' => $chamado,
            'historicos' => $historicos,
        ];
    }

    protected function recarregarChamado(): void
    {
        $this->getRecord()->refresh();
    }
}
