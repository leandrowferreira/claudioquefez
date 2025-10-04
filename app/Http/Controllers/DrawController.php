<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Models\Participant;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DrawController extends Controller
{
    public function index(): View
    {
        $draws = Draw::with('participant')->latest()->get();

        return view('draws.index', compact('draws'));
    }

    public function draw(): RedirectResponse
    {
        // Buscar participantes que ainda não foram sorteados
        $availableParticipants = Participant::whereDoesntHave('draw')->get();

        if ($availableParticipants->isEmpty()) {
            return redirect()->route('draws.index')
                ->with('error', 'Não há mais participantes disponíveis para sorteio');
        }

        // Sortear participante aleatoriamente
        $participant = $availableParticipants->random();

        // Salvar sorteio
        Draw::create(['participant_id' => $participant->id]);

        return redirect()->route('draws.index')
            ->with('drawn', $participant);
    }

    public function showCode(Draw $draw): RedirectResponse
    {
        return redirect()->route('draws.index')
            ->with('showCode', $draw->participant->codigo)
            ->with('drawn', $draw->participant);
    }
}
