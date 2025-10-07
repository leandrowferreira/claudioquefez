@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">Editar Evento</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('eventos.update', $event) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if(now()->lt($event->start_datetime))
                        {{-- Evento ainda não começou: permite editar tudo --}}
                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input
                                type="text"
                                class="form-control @error('title') is-invalid @enderror"
                                id="title"
                                name="title"
                                value="{{ old('title', $event->title) }}"
                                required
                            >
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea
                                class="form-control @error('description') is-invalid @enderror"
                                id="description"
                                name="description"
                                rows="3"
                            >{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Local</label>
                            <input
                                type="text"
                                class="form-control @error('location') is-invalid @enderror"
                                id="location"
                                name="location"
                                value="{{ old('location', $event->location) }}"
                            >
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_datetime" class="form-label">Data/Hora de Início</label>
                                <input
                                    type="datetime-local"
                                    class="form-control @error('start_datetime') is-invalid @enderror"
                                    id="start_datetime"
                                    name="start_datetime"
                                    value="{{ old('start_datetime', $event->start_datetime->format('Y-m-d\TH:i')) }}"
                                    required
                                >
                                @error('start_datetime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                    @else
                        {{-- Evento já começou: campos bloqueados, exceto data de fim --}}
                        <div class="alert alert-info">
                            <strong>Atenção:</strong> Este evento já começou. Apenas a data/hora de término pode ser alterada.
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $event->title }}"
                                disabled
                            >
                            <input type="hidden" name="title" value="{{ $event->title }}">
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" rows="3" disabled>{{ $event->description }}</textarea>
                            <input type="hidden" name="description" value="{{ $event->description }}">
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Local</label>
                            <input
                                type="text"
                                class="form-control"
                                value="{{ $event->location }}"
                                disabled
                            >
                            <input type="hidden" name="location" value="{{ $event->location }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_datetime" class="form-label">Data/Hora de Início</label>
                                <input
                                    type="datetime-local"
                                    class="form-control"
                                    value="{{ $event->start_datetime->format('Y-m-d\TH:i') }}"
                                    disabled
                                >
                                <input type="hidden" name="start_datetime" value="{{ $event->start_datetime->format('Y-m-d\TH:i') }}">
                            </div>
                    @endif

                        <div class="col-md-6 mb-3">
                            <label for="end_datetime" class="form-label">Data/Hora de Término</label>
                            <input
                                type="datetime-local"
                                class="form-control @error('end_datetime') is-invalid @enderror"
                                id="end_datetime"
                                name="end_datetime"
                                value="{{ old('end_datetime', $event->end_datetime->format('Y-m-d\TH:i')) }}"
                                required
                            >
                            @error('end_datetime')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('eventos.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
