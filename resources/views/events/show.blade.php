@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Detalhes do Evento</h4>
                @if(now()->between($event->start_datetime, $event->end_datetime))
                    <span class="badge bg-success">Ativo</span>
                @elseif(now()->lt($event->start_datetime))
                    <span class="badge bg-light text-dark">Futuro</span>
                @else
                    <span class="badge bg-secondary">Encerrado</span>
                @endif
            </div>
            <div class="card-body">
                <h3 class="mb-4">{{ $event->title }}</h3>

                @if($event->description)
                    <div class="mb-4">
                        <h5 class="text-muted">Descrição</h5>
                        <p>{{ $event->description }}</p>
                    </div>
                @endif

                @if($event->location)
                    <div class="mb-4">
                        <h5 class="text-muted">Local</h5>
                        <p>{{ $event->location }}</p>
                    </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="text-muted">Data/Hora de Início</h5>
                        <p>{{ $event->start_datetime->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-muted">Data/Hora de Término</h5>
                        <p>{{ $event->end_datetime->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h2 class="text-primary">{{ $participantsCount }}</h2>
                                <p class="mb-0 text-muted">Participantes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h2 class="text-success">{{ $drawsCount }}</h2>
                                <p class="mb-0 text-muted">Sorteados</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('eventos.index') }}" class="btn btn-secondary">Voltar</a>
                    <a href="{{ route('eventos.edit', $event) }}" class="btn btn-warning">Editar</a>
                </div>
            </div>
        </div>

        @if($draws->isNotEmpty())
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Participantes Sorteados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Estado</th>
                                    <th>Código</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($draws as $draw)
                                    <tr>
                                        <td>{{ $draw->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $draw->participant->name }}</td>
                                        <td>{{ $draw->participant->email }}</td>
                                        <td>{{ $draw->participant->state }}</td>
                                        <td><code>{{ $draw->participant->codigo }}</code></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
