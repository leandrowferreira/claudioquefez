@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle text-success" viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                    </svg>
                </div>

                <h2 class="card-title mb-4">Cadastro Realizado com Sucesso!</h2>

                <p class="lead mb-4">
                    Obrigado por se inscrever, <strong>{{ $participant->name }}</strong>!
                </p>

                <div class="alert alert-info" role="alert">
                    Guarde o código abaixo com cuidado. Ele será necessário para receber seu brinde no evento caso você seja sorteado.
                </div>

                <div class="my-5">
                    <p class="text-muted mb-2">Seu código:</p>
                    <div class="display-1 fw-bold text-primary font-monospace">
                        {{ $participant->codigo }}
                    </div>
                </div>

                <p class="text-muted">
                    Um e-mail de confirmação com o código foi enviado para <strong>{{ $participant->email }}</strong>
                </p>

                <div class="mt-4">
                    <a href="{{ route('participants.index') }}" class="btn btn-outline-primary">
                        Cadastrar outro participante
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
