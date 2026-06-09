<?php

namespace App\Filament\Livewire;

use App\Filament\Support\CriarChamadoAcao;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class NovoChamadoTopbarButton extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public function criarChamadoAction(): Action
    {
        return CriarChamadoAcao::make();
    }

    public function render(): View
    {
        return view('filament.livewire.novo-chamado-topbar-button');
    }
}
