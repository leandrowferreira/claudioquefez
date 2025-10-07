@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciar Eventos</h2>
            <a href="{{ route('eventos.create') }}" class="btn btn-primary">Novo Evento</a>
        </div>

        @if($events->isEmpty())
            <div class="alert alert-info">
                Nenhum evento cadastrado. Clique em "Novo Evento" para criar o primeiro.
            </div>
        @else
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Local</th>
                                    <th>Início</th>
                                    <th>Término</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $event)
                                    <tr>
                                        <td>{{ $event->title }}</td>
                                        <td>{{ $event->location }}</td>
                                        <td>{{ $event->start_datetime->format('d/m/Y H:i') }}</td>
                                        <td>{{ $event->end_datetime->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if(now()->between($event->start_datetime, $event->end_datetime))
                                                <span class="badge bg-success">Ativo</span>
                                            @elseif(now()->lt($event->start_datetime))
                                                <span class="badge bg-info">Futuro</span>
                                            @else
                                                <span class="badge bg-secondary">Encerrado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('eventos.show', $event) }}" class="btn btn-sm btn-info">Ver</a>
                                            <a href="{{ route('eventos.edit', $event) }}" class="btn btn-sm btn-warning">Editar</a>
                                            <form action="{{ route('eventos.destroy', $event) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja deletar este evento? Todos os participantes e sorteios serão removidos.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Deletar</button>
                                            </form>
                                        </td>
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
