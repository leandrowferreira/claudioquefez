<?php

use App\Http\Controllers\DrawController;
use App\Http\Controllers\ParticipantController;
use Illuminate\Support\Facades\Route;

// Rotas de Participantes
Route::get('/', [ParticipantController::class, 'index'])->name('participants.index');
Route::post('/', [ParticipantController::class, 'store'])->name('participants.store');
Route::get('/sucesso/{codigo}', [ParticipantController::class, 'success'])->name('participants.success');

// Rotas de Sorteio
Route::get('/sorteio', [DrawController::class, 'index'])->name('draws.index');
Route::post('/sorteio/sortear', [DrawController::class, 'draw'])->name('draws.draw');
Route::post('/sorteio/{draw}/exibir-codigo', [DrawController::class, 'showCode'])->name('draws.showCode');
