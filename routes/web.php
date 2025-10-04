<?php

use App\Http\Controllers\DrawController;
use App\Http\Controllers\ParticipantController;
use Illuminate\Support\Facades\Route;

// Rotas de Participantes
Route::get('/', [ParticipantController::class, 'index'])->name('participants.index');
Route::get('/cadastro', [ParticipantController::class, 'index'])->name('participants.create');
Route::post('/', [ParticipantController::class, 'store'])->name('participants.store');
Route::get('/sucesso/{codigo}', [ParticipantController::class, 'success'])->name('participants.success');

// Rota de autenticação do sorteio
Route::get('/sorteio/senha', [DrawController::class, 'showPasswordForm'])->name('draw.password');
Route::post('/sorteio/senha', [DrawController::class, 'verifyPassword'])->name('draw.verify');

// Rotas de Sorteio (protegidas por middleware)
Route::middleware('check.draw.password')->group(function () {
    Route::get('/sorteio', [DrawController::class, 'index'])->name('draws.index');
    Route::post('/sorteio/sortear', [DrawController::class, 'draw'])->name('draws.draw');
    Route::post('/sorteio/{draw}/exibir-codigo', [DrawController::class, 'showCode'])->name('draws.showCode');
});
