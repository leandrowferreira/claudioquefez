@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <h2 class="mb-4">{{ $event ? $event->title : 'Sistema de Sorteio' }}</h2>

        @if(!$event)
            <div class="alert alert-warning">
                Não há eventos acontecendo no momento. Sorteios estão fechados.
            </div>
        @else

        @if(session('drawn'))
            <div class="card shadow-sm mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">🎉 Participante Sorteado!</h4>
                </div>
                <div class="card-body text-center py-5">
                    <h3 class="mb-4">{{ session('drawn')->name }}</h3>

                    <div class="row justify-content-center mb-4">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>E-mail:</strong> {{ session('drawn')->email }}</p>
                            <p class="mb-0"><strong>Estado:</strong> {{ session('drawn')->state }}</p>
                        </div>
                    </div>

                    @if(session('showCode'))
                        <div class="my-4">
                            <p class="text-muted mb-2">Código do participante:</p>
                            <div class="display-3 fw-bold text-success font-monospace">
                                {{ session('showCode') }}
                            </div>
                        </div>
                    @else
                        <form action="{{ route('draws.showCode', session('drawn')->draw->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-lg me-2">
                                🔓 Exibir Código
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('draws.index') }}" class="btn btn-secondary btn-lg">
                        ← Voltar
                    </a>
                </div>
            </div>
        @else
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center py-5">
                    <h4 class="mb-4">Clique no botão abaixo para sortear um participante</h4>
                    <form action="{{ route('draws.draw') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg px-5 py-3">
                            🎲 Sortear Participante
                        </button>
                    </form>
                </div>
            </div>
        @endif

        @if($draws->isNotEmpty())
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Participantes Já Sorteados ({{ $draws->count() }})</h5>
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
        @endif
    </div>
</div>
@endsection
