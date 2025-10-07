<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParticipantRequest;
use App\Models\Event;
use App\Models\Participant;
use App\Notifications\ParticipantRegistered;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    public function index(): View
    {
        $event = Event::getActiveEvent();

        return view('participants.index', compact('event'));
    }

    public function store(StoreParticipantRequest $request): RedirectResponse
    {
        $event = Event::getActiveEvent();

        if (! $event) {
            return redirect()->route('participants.index')
                ->with('error', 'Não há eventos acontecendo no momento. Cadastros estão fechados.');
        }

        $validated = $request->validated();

        // Gerar código único de 5 letras maiúsculas
        do {
            $codigo = $this->generateCode();
        } while (Participant::where('codigo', $codigo)->exists());

        $validated['codigo'] = $codigo;
        $validated['event_id'] = $event->id;

        $participant = Participant::create($validated);

        // Enviar notificação por e-mail
        $participant->notify(new ParticipantRegistered($participant, $event));

        return redirect()->route('participants.success', ['codigo' => $codigo]);
    }

    public function success(string $codigo): View
    {
        $participant = Participant::where('codigo', $codigo)->firstOrFail();
        $event = $participant->event;

        return view('participants.success', compact('participant', 'event'));
    }

    private function generateCode(): string
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < 5; $i++) {
            $code .= $letters[rand(0, 25)];
        }

        return $code;
    }
}
