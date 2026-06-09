<?php

use App\Http\Controllers\AvaliacaoPublicaController;
use App\Http\Controllers\ChamadoPublicoController;
use App\Http\Controllers\PainelController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/chamados/novo');

Route::get('/painel', [PainelController::class, 'redirecionar'])->name('painel');

Route::prefix('chamados')->name('chamados.')->group(function (): void {
    Route::get('/novo', [ChamadoPublicoController::class, 'criar'])->name('criar');
    Route::post('/novo', [ChamadoPublicoController::class, 'salvar'])->name('salvar');
    Route::get('/consultar', [ChamadoPublicoController::class, 'consultar'])->name('consultar');
    Route::post('/consultar', [ChamadoPublicoController::class, 'consultarResultado'])->name('consultar.buscar');
    Route::get('/{protocolo}/sucesso', [ChamadoPublicoController::class, 'sucesso'])->name('sucesso');
    Route::get('/{protocolo}/finalizado', [ChamadoPublicoController::class, 'finalizado'])->name('finalizado');
    Route::get('/{protocolo}/avaliar/{token}', [AvaliacaoPublicaController::class, 'exibir'])->name('avaliar');
    Route::post('/{protocolo}/avaliar/{token}', [AvaliacaoPublicaController::class, 'salvar'])->name('avaliar.salvar');
});
