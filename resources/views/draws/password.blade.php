@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">🔒 Acesso ao Sistema de Sorteio</h4>
            </div>
            <div class="card-body py-5">
                <p class="text-center mb-4">
                    Para acessar o sistema de sorteio, informe a senha de administrador:
                </p>

                <form action="{{ route('draw.verify') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="password" class="form-label">Senha</label>
                        <input
                            type="password"
                            class="form-control form-control-lg"
                            id="password"
                            name="password"
                            required
                            autofocus
                        >
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Acessar Sistema de Sorteio
                        </button>
                        <a href="{{ route('participants.create') }}" class="btn btn-outline-secondary">
                            Voltar ao Cadastro
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
