<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParticipantRequest;
use App\Models\Participant;
use App\Notifications\ParticipantRegistered;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    public function index(): View
    {
        return view('participants.index');
    }

    public function store(StoreParticipantRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Gerar código único de 5 letras maiúsculas
        do {
            $codigo = $this->generateCode();
        } while (Participant::where('codigo', $codigo)->exists());

        $validated['codigo'] = $codigo;

        $participant = Participant::create($validated);

        // Enviar notificação por e-mail
        $participant->notify(new ParticipantRegistered($codigo));

        return redirect()->route('participants.success', ['codigo' => $codigo]);
    }

    public function success(string $codigo): View
    {
        $participant = Participant::where('codigo', $codigo)->firstOrFail();

        return view('participants.success', compact('participant'));
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
