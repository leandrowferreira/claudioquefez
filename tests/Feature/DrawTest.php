<?php

use App\Models\Draw;
use App\Models\Event;
use App\Models\Participant;

beforeEach(function () {
    Event::create([
        'title' => 'PHPeste 2025',
        'description' => 'Conferência de PHP no Nordeste',
        'location' => 'Parnaíba, Piauí',
        'start_datetime' => now()->subHour(),
        'end_datetime' => now()->addHours(3),
    ]);
});

test('redirecionar para tela de senha quando não autenticado', function () {
    $response = $this->get('/sorteio');

    $response->assertRedirect(route('draw.password'));
});

test('exibir formulário de senha', function () {
    $response = $this->get('/sorteio/senha');

    $response->assertStatus(200);
    $response->assertSee('Acesso ao Sistema de Sorteio');
    $response->assertSee('Senha');
});

test('autenticar com senha correta e redirecionar para sorteio', function () {
    config(['app.draw_password' => 'senha123']);

    $response = $this->post('/sorteio/senha', [
        'password' => 'senha123',
    ]);

    $response->assertRedirect(route('draws.index'));
    $response->assertSessionHas('draw_authenticated', true);
});

test('redirecionar para cadastro com senha incorreta', function () {
    config(['app.draw_password' => 'senha123']);

    $response = $this->post('/sorteio/senha', [
        'password' => 'senha_errada',
    ]);

    $response->assertRedirect(route('participants.create'));
    $response->assertSessionMissing('draw_authenticated');
});

test('exibir página de sorteio quando autenticado', function () {
    $this->withSession(['draw_authenticated' => true]);

    $response = $this->get('/sorteio');

    $response->assertStatus(200);
    $response->assertSee('Sistema de Sorteio');
});

test('sortear participante cadastrado e salvar em draws', function () {
    $this->withSession(['draw_authenticated' => true]);

    $event = Event::getActiveEvent();

    $participant = Participant::create([
        'name' => 'Carlos Alberto',
        'email' => 'carlos@example.com',
        'state' => 'RS',
        'codigo' => 'ABCDE',
        'event_id' => $event->id,
    ]);

    $response = $this->post('/sorteio/sortear');

    $this->assertDatabaseHas('draws', [
        'participant_id' => $participant->id,
        'event_id' => $event->id,
    ]);

    $response->assertRedirect(route('draws.index'));
    $response->assertSessionHas('drawn');
});

test('participante sorteado não pode ser sorteado novamente no mesmo evento', function () {
    $this->withSession(['draw_authenticated' => true]);

    $event = Event::getActiveEvent();

    $participant = Participant::create([
        'name' => 'Fernanda Lima',
        'email' => 'fernanda@example.com',
        'state' => 'PR',
        'codigo' => 'FGHIJ',
        'event_id' => $event->id,
    ]);

    // Primeiro sorteio
    $this->post('/sorteio/sortear');

    $this->assertDatabaseHas('draws', [
        'participant_id' => $participant->id,
        'event_id' => $event->id,
    ]);

    // Tentar sortear novamente (não deve haver mais participantes)
    $response = $this->post('/sorteio/sortear');

    $response->assertSessionHas('error');
    expect(Draw::count())->toBe(1);
});

test('exibir lista de participantes já sorteados', function () {
    $this->withSession(['draw_authenticated' => true]);

    $event = Event::getActiveEvent();

    $participant1 = Participant::create([
        'name' => 'Roberto Silva',
        'email' => 'roberto@example.com',
        'state' => 'SC',
        'codigo' => 'KLMNO',
        'event_id' => $event->id,
    ]);

    $participant2 = Participant::create([
        'name' => 'Paula Costa',
        'email' => 'paula@example.com',
        'state' => 'GO',
        'codigo' => 'PQRST',
        'event_id' => $event->id,
    ]);

    Draw::create([
        'participant_id' => $participant1->id,
        'event_id' => $event->id,
    ]);
    Draw::create([
        'participant_id' => $participant2->id,
        'event_id' => $event->id,
    ]);

    $response = $this->get('/sorteio');

    $response->assertSee('Roberto Silva');
    $response->assertSee('Paula Costa');
    $response->assertSee('KLMNO');
    $response->assertSee('PQRST');
});

test('exibir mensagem quando não há mais participantes disponíveis', function () {
    $this->withSession(['draw_authenticated' => true]);

    $event = Event::getActiveEvent();

    $participant = Participant::create([
        'name' => 'Última Pessoa',
        'email' => 'ultima@example.com',
        'state' => 'AM',
        'codigo' => 'UVWXY',
        'event_id' => $event->id,
    ]);

    Draw::create([
        'participant_id' => $participant->id,
        'event_id' => $event->id,
    ]);

    $response = $this->post('/sorteio/sortear');

    $response->assertSessionHas('error', 'Não há mais participantes disponíveis para sorteio');
});

test('exibir código do participante sorteado', function () {
    $this->withSession(['draw_authenticated' => true]);

    $event = Event::getActiveEvent();

    $participant = Participant::create([
        'name' => 'Marcelo Santos',
        'email' => 'marcelo@example.com',
        'state' => 'MT',
        'codigo' => 'ZABCD',
        'event_id' => $event->id,
    ]);

    $draw = Draw::create([
        'participant_id' => $participant->id,
        'event_id' => $event->id,
    ]);

    $response = $this->post(route('draws.showCode', $draw));

    $response->assertSessionHas('showCode', 'ZABCD');
    $response->assertRedirect(route('draws.index'));
});

test('permitir múltiplos sorteios', function () {
    $this->withSession(['draw_authenticated' => true]);

    $event = Event::getActiveEvent();

    $participant1 = Participant::create([
        'name' => 'Primeiro',
        'email' => 'primeiro@example.com',
        'state' => 'PA',
        'codigo' => 'AAAAA',
        'event_id' => $event->id,
    ]);

    $participant2 = Participant::create([
        'name' => 'Segundo',
        'email' => 'segundo@example.com',
        'state' => 'RO',
        'codigo' => 'BBBBB',
        'event_id' => $event->id,
    ]);

    $participant3 = Participant::create([
        'name' => 'Terceiro',
        'email' => 'terceiro@example.com',
        'state' => 'AP',
        'codigo' => 'CCCCC',
        'event_id' => $event->id,
    ]);

    // Primeiro sorteio
    $this->post('/sorteio/sortear');
    expect(Draw::count())->toBe(1);

    // Segundo sorteio
    $this->post('/sorteio/sortear');
    expect(Draw::count())->toBe(2);

    // Terceiro sorteio
    $this->post('/sorteio/sortear');
    expect(Draw::count())->toBe(3);

    // Todos os participantes foram sorteados
    $response = $this->post('/sorteio/sortear');
    $response->assertSessionHas('error');
    expect(Draw::count())->toBe(3);
});
