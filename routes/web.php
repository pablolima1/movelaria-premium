<?php

use App\Http\Controllers\AliquotaImpostoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\ProjetoController;
use App\Http\Controllers\RecebimentoController;
use App\Http\Controllers\TipoProjetoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resource('clientes', ClienteController::class)->except(['create', 'edit', 'show']);

    Route::resource('tipos-projeto', TipoProjetoController::class)
        ->parameters(['tipos-projeto' => 'tipoProjeto'])
        ->except(['create', 'edit', 'show']);

    Route::resource('aliquotas-imposto', AliquotaImpostoController::class)
        ->parameters(['aliquotas-imposto' => 'aliquota'])
        ->except(['create', 'edit', 'show']);

    Route::resource('projetos', ProjetoController::class)->except(['create', 'edit']);

    Route::resource('projetos.recebimentos', RecebimentoController::class)
        ->only(['store', 'update', 'destroy'])
        ->scoped();

    Route::resource('projetos.despesas', DespesaController::class)
        ->only(['store', 'update', 'destroy'])
        ->scoped();

    Route::get('/cobrancas', [ParcelaController::class, 'index'])->name('cobrancas.index');
    Route::post('/parcelas/{parcela}/receber', [ParcelaController::class, 'receber'])->name('parcelas.receber');
    Route::patch('/parcelas/{parcela}', [ParcelaController::class, 'update'])->name('parcelas.update');
});
