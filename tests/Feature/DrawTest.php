<?php

use App\Models\Draw;
use App\Models\Participant;

test('exibir página de sorteio', function () {
    $response = $this->get('/sorteio');

    $response->assertStatus(200);
    $response->assertSee('Sistema de Sorteio');
});

test('sortear participante cadastrado e salvar em draws', function () {
    $participant = Participant::create([
        'name' => 'Carlos Alberto',
        'email' => 'carlos@example.com',
        'state' => 'RS',
        'codigo' => 'ABCDE',
    ]);

    $response = $this->post('/sorteio/sortear');

    $this->assertDatabaseHas('draws', [
        'participant_id' => $participant->id,
    ]);

    $response->assertRedirect(route('draws.index'));
    $response->assertSessionHas('drawn');
});

test('participante sorteado não pode ser sorteado novamente', function () {
    $participant = Participant::create([
        'name' => 'Fernanda Lima',
        'email' => 'fernanda@example.com',
        'state' => 'PR',
        'codigo' => 'FGHIJ',
    ]);

    // Primeiro sorteio
    $this->post('/sorteio/sortear');

    $this->assertDatabaseHas('draws', [
        'participant_id' => $participant->id,
    ]);

    // Tentar sortear novamente (não deve haver mais participantes)
    $response = $this->post('/sorteio/sortear');

    $response->assertSessionHas('error');
    expect(Draw::count())->toBe(1);
});

test('exibir lista de participantes já sorteados', function () {
    $participant1 = Participant::create([
        'name' => 'Roberto Silva',
        'email' => 'roberto@example.com',
        'state' => 'SC',
        'codigo' => 'KLMNO',
    ]);

    $participant2 = Participant::create([
        'name' => 'Paula Costa',
        'email' => 'paula@example.com',
        'state' => 'GO',
        'codigo' => 'PQRST',
    ]);

    Draw::create(['participant_id' => $participant1->id]);
    Draw::create(['participant_id' => $participant2->id]);

    $response = $this->get('/sorteio');

    $response->assertSee('Roberto Silva');
    $response->assertSee('Paula Costa');
    $response->assertSee('KLMNO');
    $response->assertSee('PQRST');
});

test('exibir mensagem quando não há mais participantes disponíveis', function () {
    $participant = Participant::create([
        'name' => 'Última Pessoa',
        'email' => 'ultima@example.com',
        'state' => 'AM',
        'codigo' => 'UVWXY',
    ]);

    Draw::create(['participant_id' => $participant->id]);

    $response = $this->post('/sorteio/sortear');

    $response->assertSessionHas('error', 'Não há mais participantes disponíveis para sorteio');
});

test('exibir código do participante sorteado', function () {
    $participant = Participant::create([
        'name' => 'Marcelo Santos',
        'email' => 'marcelo@example.com',
        'state' => 'MT',
        'codigo' => 'ZABCD',
    ]);

    $draw = Draw::create(['participant_id' => $participant->id]);

    $response = $this->post(route('draws.showCode', $draw));

    $response->assertSessionHas('showCode', 'ZABCD');
    $response->assertRedirect(route('draws.index'));
});

test('permitir múltiplos sorteios', function () {
    $participant1 = Participant::create([
        'name' => 'Primeiro',
        'email' => 'primeiro@example.com',
        'state' => 'PA',
        'codigo' => 'AAAAA',
    ]);

    $participant2 = Participant::create([
        'name' => 'Segundo',
        'email' => 'segundo@example.com',
        'state' => 'RO',
        'codigo' => 'BBBBB',
    ]);

    $participant3 = Participant::create([
        'name' => 'Terceiro',
        'email' => 'terceiro@example.com',
        'state' => 'AP',
        'codigo' => 'CCCCC',
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
