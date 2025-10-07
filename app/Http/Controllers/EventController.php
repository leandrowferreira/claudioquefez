<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::orderBy('start_datetime', 'desc')->get();

        return view('events.index', compact('events'));
    }

    public function create(): View
    {
        return view('events.create');
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        Event::create($request->validated());

        return redirect()->route('eventos.index')
            ->with('success', 'Evento criado com sucesso!');
    }

    public function show(Event $event): View
    {
        $participantsCount = $event->participants()->count();
        $drawsCount = $event->draws()->count();

        return view('events.show', compact('event', 'participantsCount', 'drawsCount'));
    }

    public function edit(Event $event): View
    {
        return view('events.edit', compact('event'));
    }

    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $event->update($request->validated());

        return redirect()->route('eventos.index')
            ->with('success', 'Evento atualizado com sucesso!');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        return redirect()->route('eventos.index')
            ->with('success', 'Evento deletado com sucesso!');
    }
}
