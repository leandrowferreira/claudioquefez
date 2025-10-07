<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DrawController extends Controller
{
    public function showPasswordForm(): View
    {
        return view('draws.password');
    }

    public function verifyPassword(Request $request): RedirectResponse
    {
        $password = $request->input('password');
        $correctPassword = config('app.draw_password');

        if ($password === $correctPassword) {
            session(['draw_authenticated' => true]);

            return redirect()->route('draws.index');
        }

        return redirect()->route('participants.create');
    }

    public function index(): View
    {
        $event = Event::getActiveEvent();
        $drawnParticipantId = session('drawn')?->id;

        $draws = Draw::with('participant')
            ->when($event, function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })
            ->when($drawnParticipantId, function ($query) use ($drawnParticipantId) {
                $query->where('participant_id', '!=', $drawnParticipantId);
            })
            ->latest()
            ->get();

        return view('draws.index', compact('draws', 'event'));
    }

    public function draw(): RedirectResponse
    {
        $event = Event::getActiveEvent();

        if (! $event) {
            return redirect()->route('draws.index')
                ->with('error', 'Não há eventos acontecendo no momento. Sorteios estão fechados.');
        }

        // Buscar participantes do evento ativo que ainda não foram sorteados
        $availableParticipants = Participant::where('event_id', $event->id)
            ->whereDoesntHave('draw', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })
            ->get();

        if ($availableParticipants->isEmpty()) {
            return redirect()->route('draws.index')
                ->with('error', 'Não há mais participantes disponíveis para sorteio');
        }

        // Sortear participante aleatoriamente
        $participant = $availableParticipants->random();

        // Salvar sorteio
        Draw::create([
            'participant_id' => $participant->id,
            'event_id' => $event->id,
        ]);

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
