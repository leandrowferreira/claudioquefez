<?php

use App\Models\Event;
use App\Models\Participant;
use App\Notifications\ParticipantRegistered;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Event::create([
        'title' => 'PHPeste 2025',
        'description' => 'Conferência de PHP no Nordeste',
        'location' => 'Parnaíba, Piauí',
        'start_datetime' => now()->subHour(),
        'end_datetime' => now()->addHours(3),
    ]);
});

test('exibir formulário de cadastro', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('PHPeste 2025');
});

test('cadastro com dados válidos salva no banco e redireciona para sucesso', function () {
    Notification::fake();

    $response = $this->post('/', [
        'name' => 'João Silva',
        'email' => 'joao@example.com',
        'state' => 'PI',
    ]);

    $this->assertDatabaseHas('participants', [
        'name' => 'João Silva',
        'email' => 'joao@example.com',
        'state' => 'PI',
    ]);

    $participant = Participant::where('email', 'joao@example.com')->first();
    $response->assertRedirect(route('participants.success', ['codigo' => $participant->codigo]));
});

test('gerar código único de 5 letras maiúsculas', function () {
    Notification::fake();

    $this->post('/', [
        'name' => 'Maria Santos',
        'email' => 'maria@example.com',
        'state' => 'CE',
    ]);

    $participant = Participant::where('email', 'maria@example.com')->first();

    expect($participant->codigo)->toHaveLength(5);
    expect($participant->codigo)->toMatch('/^[A-Z]{5}$/');
});

test('não permitir e-mail duplicado no mesmo evento', function () {
    Notification::fake();

    $event = Event::getActiveEvent();

    Participant::create([
        'name' => 'Pedro Costa',
        'email' => 'pedro@example.com',
        'state' => 'BA',
        'codigo' => 'ABCDE',
        'event_id' => $event->id,
    ]);

    $response = $this->post('/', [
        'name' => 'Outro Pedro',
        'email' => 'pedro@example.com',
        'state' => 'SP',
    ]);

    $response->assertSessionHasErrors('email');
});

test('validar campo nome obrigatório', function () {
    $response = $this->post('/', [
        'email' => 'teste@example.com',
        'state' => 'RJ',
    ]);

    $response->assertSessionHasErrors('name');
});

test('validar campo e-mail obrigatório e formato válido', function () {
    $response = $this->post('/', [
        'name' => 'Teste',
        'state' => 'SP',
    ]);

    $response->assertSessionHasErrors('email');

    $response = $this->post('/', [
        'name' => 'Teste',
        'email' => 'email-invalido',
        'state' => 'SP',
    ]);

    $response->assertSessionHasErrors('email');
});

test('validar campo estado obrigatório e deve ser estado brasileiro válido', function () {
    $response = $this->post('/', [
        'name' => 'Teste',
        'email' => 'teste@example.com',
    ]);

    $response->assertSessionHasErrors('state');

    $response = $this->post('/', [
        'name' => 'Teste',
        'email' => 'teste@example.com',
        'state' => 'XX',
    ]);

    $response->assertSessionHasErrors('state');
});

test('código gerado deve ser único', function () {
    Notification::fake();

    // Criar 10 participantes
    for ($i = 0; $i < 10; $i++) {
        $this->post('/', [
            'name' => "Participante $i",
            'email' => "participante$i@example.com",
            'state' => 'PI',
        ]);
    }

    $codigos = Participant::pluck('codigo')->toArray();
    $codigosUnicos = array_unique($codigos);

    expect(count($codigos))->toBe(count($codigosUnicos));
});

test('enviar notificação por e-mail após cadastro', function () {
    Notification::fake();

    $this->post('/', [
        'name' => 'Ana Paula',
        'email' => 'ana@example.com',
        'state' => 'MG',
    ]);

    $participant = Participant::where('email', 'ana@example.com')->first();

    Notification::assertSentTo(
        $participant,
        ParticipantRegistered::class
    );
});
