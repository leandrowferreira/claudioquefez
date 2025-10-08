<?php

use App\Models\Draw;
use App\Models\Event;
use App\Models\Participant;

beforeEach(function () {
    $this->withSession(['draw_authenticated' => true]);
});

test('evento pode ser criado com dados válidos', function () {
    $event = Event::create([
        'title' => 'Teste Conference 2025',
        'description' => 'Evento de teste',
        'location' => 'São Paulo, SP',
        'start_datetime' => now()->addDays(5),
        'end_datetime' => now()->addDays(5)->addHours(4),
    ]);

    expect($event->id)->toBeInt();
    expect($event->title)->toBe('Teste Conference 2025');
    expect($event->location)->toBe('São Paulo, SP');
});

test('relacionamento evento para participantes funciona', function () {
    $event = Event::create([
        'title' => 'PHP Conference',
        'description' => 'Conferência de PHP',
        'location' => 'Rio de Janeiro, RJ',
        'start_datetime' => now()->subHour(),
        'end_datetime' => now()->addHours(3),
    ]);

    $participant = Participant::create([
        'name' => 'João Silva',
        'email' => 'joao@example.com',
        'state' => 'RJ',
        'codigo' => 'ABCDE',
        'event_id' => $event->id,
    ]);

    expect($event->participants)->toHaveCount(1);
    expect($event->participants->first()->id)->toBe($participant->id);
});

test('relacionamento evento para sorteios funciona', function () {
    $event = Event::create([
        'title' => 'Laravel Meetup',
        'description' => 'Encontro de desenvolvedores',
        'location' => 'Belo Horizonte, MG',
        'start_datetime' => now()->subHour(),
        'end_datetime' => now()->addHours(2),
    ]);

    $participant = Participant::create([
        'name' => 'Maria Santos',
        'email' => 'maria@example.com',
        'state' => 'MG',
        'codigo' => 'FGHIJ',
        'event_id' => $event->id,
    ]);

    $draw = Draw::create([
        'participant_id' => $participant->id,
        'event_id' => $event->id,
    ]);

    expect($event->draws)->toHaveCount(1);
    expect($event->draws->first()->id)->toBe($draw->id);
});

test('getActiveEvent retorna evento ativo baseado em data e hora', function () {
    Event::create([
        'title' => 'Evento Passado',
        'description' => 'Já acabou',
        'location' => 'Fortaleza, CE',
        'start_datetime' => now()->subDays(2),
        'end_datetime' => now()->subDays(1),
    ]);

    $activeEvent = Event::create([
        'title' => 'Evento Ativo',
        'description' => 'Acontecendo agora',
        'location' => 'Recife, PE',
        'start_datetime' => now()->subHour(),
        'end_datetime' => now()->addHours(3),
    ]);

    Event::create([
        'title' => 'Evento Futuro',
        'description' => 'Ainda não começou',
        'location' => 'Salvador, BA',
        'start_datetime' => now()->addDays(5),
        'end_datetime' => now()->addDays(5)->addHours(4),
    ]);

    $result = Event::getActiveEvent();

    expect($result)->not->toBeNull();
    expect($result->id)->toBe($activeEvent->id);
    expect($result->title)->toBe('Evento Ativo');
});

test('getActiveEvent retorna null quando não há evento ativo', function () {
    Event::create([
        'title' => 'Evento Passado',
        'description' => 'Já acabou',
        'location' => 'Natal, RN',
        'start_datetime' => now()->subDays(10),
        'end_datetime' => now()->subDays(9),
    ]);

    Event::create([
        'title' => 'Evento Futuro',
        'description' => 'Ainda não começou',
        'location' => 'João Pessoa, PB',
        'start_datetime' => now()->addDays(10),
        'end_datetime' => now()->addDays(10)->addHours(5),
    ]);

    $result = Event::getActiveEvent();

    expect($result)->toBeNull();
});

test('deletar evento deleta participantes e sorteios em cascade', function () {
    $event = Event::create([
        'title' => 'Evento para Deletar',
        'description' => 'Será removido',
        'location' => 'Curitiba, PR',
        'start_datetime' => now()->subHour(),
        'end_datetime' => now()->addHours(2),
    ]);

    $participant = Participant::create([
        'name' => 'Pedro Costa',
        'email' => 'pedro@example.com',
        'state' => 'PR',
        'codigo' => 'KLMNO',
        'event_id' => $event->id,
    ]);

    $draw = Draw::create([
        'participant_id' => $participant->id,
        'event_id' => $event->id,
    ]);

    $eventCountBefore = Event::count();
    $participantCountBefore = Participant::count();
    $drawCountBefore = Draw::count();

    $event->delete();

    expect(Event::count())->toBe($eventCountBefore - 1);
    expect(Participant::count())->toBe($participantCountBefore - 1);
    expect(Draw::count())->toBe($drawCountBefore - 1);
});

test('validar campo title obrigatório', function () {
    $response = $this->post(route('eventos.store'), [
        'description' => 'Descrição',
        'location' => 'Local',
        'start_datetime' => now()->addDays(1),
        'end_datetime' => now()->addDays(1)->addHours(3),
    ]);

    $response->assertSessionHasErrors('title');
});

test('validar campo start_datetime obrigatório e formato válido', function () {
    $response = $this->post(route('eventos.store'), [
        'title' => 'Evento Teste',
        'end_datetime' => now()->addDays(1)->addHours(3),
    ]);

    $response->assertSessionHasErrors('start_datetime');
});

test('validar campo end_datetime obrigatório e after start_datetime', function () {
    $response = $this->post(route('eventos.store'), [
        'title' => 'Evento Teste',
        'start_datetime' => now()->addDays(1),
        'end_datetime' => now()->addDays(1)->subHour(),
    ]);

    $response->assertSessionHasErrors('end_datetime');
});

test('listar todos os eventos', function () {
    Event::create([
        'title' => 'Evento 1',
        'description' => 'Descrição 1',
        'location' => 'Local 1',
        'start_datetime' => now()->addDays(1),
        'end_datetime' => now()->addDays(1)->addHours(2),
    ]);

    Event::create([
        'title' => 'Evento 2',
        'description' => 'Descrição 2',
        'location' => 'Local 2',
        'start_datetime' => now()->addDays(2),
        'end_datetime' => now()->addDays(2)->addHours(3),
    ]);

    $response = $this->get(route('eventos.index'));

    $response->assertStatus(200);
    $response->assertSee('Evento 1');
    $response->assertSee('Evento 2');
});

test('exibir formulário de criação', function () {
    $response = $this->get(route('eventos.create'));

    $response->assertStatus(200);
    $response->assertSee('Novo Evento');
});

test('criar evento com dados válidos', function () {
    $response = $this->post(route('eventos.store'), [
        'title' => 'DevFest 2025',
        'description' => 'Festival de desenvolvedores',
        'location' => 'Brasília, DF',
        'start_datetime' => now()->addDays(10)->format('Y-m-d\TH:i'),
        'end_datetime' => now()->addDays(10)->addHours(6)->format('Y-m-d\TH:i'),
    ]);

    $response->assertRedirect(route('eventos.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('events', [
        'title' => 'DevFest 2025',
        'location' => 'Brasília, DF',
    ]);
});

test('exibir detalhes de um evento', function () {
    $event = Event::create([
        'title' => 'Evento Detalhado',
        'description' => 'Descrição detalhada',
        'location' => 'Porto Alegre, RS',
        'start_datetime' => now()->addDays(3),
        'end_datetime' => now()->addDays(3)->addHours(4),
    ]);

    $response = $this->get(route('eventos.show', $event));

    $response->assertStatus(200);
    $response->assertSee('Evento Detalhado');
    $response->assertSee('Porto Alegre, RS');
});

test('exibir formulário de edição', function () {
    $event = Event::create([
        'title' => 'Evento para Editar',
        'description' => 'Será editado',
        'location' => 'Manaus, AM',
        'start_datetime' => now()->addDays(5),
        'end_datetime' => now()->addDays(5)->addHours(3),
    ]);

    $response = $this->get(route('eventos.edit', $event));

    $response->assertStatus(200);
    $response->assertSee('Evento para Editar');
    $response->assertSee('Editar Evento');
});

test('atualizar evento', function () {
    $event = Event::create([
        'title' => 'Título Antigo',
        'description' => 'Descrição antiga',
        'location' => 'Local antigo',
        'start_datetime' => now()->addDays(7),
        'end_datetime' => now()->addDays(7)->addHours(3),
    ]);

    $response = $this->put(route('eventos.update', $event), [
        'title' => 'Título Atualizado',
        'description' => 'Descrição atualizada',
        'location' => 'Local atualizado',
        'start_datetime' => now()->addDays(8)->format('Y-m-d\TH:i'),
        'end_datetime' => now()->addDays(8)->addHours(4)->format('Y-m-d\TH:i'),
    ]);

    $response->assertRedirect(route('eventos.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('events', [
        'id' => $event->id,
        'title' => 'Título Atualizado',
        'location' => 'Local atualizado',
    ]);
});

test('deletar evento', function () {
    $event = Event::create([
        'title' => 'Evento para Deletar',
        'description' => 'Será removido via controller',
        'location' => 'Vitória, ES',
        'start_datetime' => now()->addDays(4),
        'end_datetime' => now()->addDays(4)->addHours(2),
    ]);

    $response = $this->delete(route('eventos.destroy', $event));

    $response->assertRedirect(route('eventos.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseMissing('events', [
        'id' => $event->id,
    ]);
});

test('rotas de CRUD requerem autenticação via middleware', function () {
    session()->forget('draw_authenticated');

    $response = $this->get(route('eventos.index'));
    $response->assertRedirect(route('draw.password'));

    $response = $this->get(route('eventos.create'));
    $response->assertRedirect(route('draw.password'));
});

test('participante só pode se cadastrar se houver evento ativo', function () {
    Event::create([
        'title' => 'Evento Futuro',
        'description' => 'Ainda não começou',
        'location' => 'Florianópolis, SC',
        'start_datetime' => now()->addDays(10),
        'end_datetime' => now()->addDays(10)->addHours(5),
    ]);

    $response = $this->post('/', [
        'name' => 'Tentativa Falha',
        'email' => 'falha@example.com',
        'state' => 'SC',
    ]);

    $response->assertSessionHas('error');
    $this->assertDatabaseMissing('participants', [
        'email' => 'falha@example.com',
    ]);
});

test('sortear apenas participantes do evento ativo', function () {
    $this->withSession(['draw_authenticated' => true]);

    $event1 = Event::create([
        'title' => 'Evento Passado',
        'description' => 'Já acabou',
        'location' => 'Local 1',
        'start_datetime' => now()->subDays(2),
        'end_datetime' => now()->subDays(1),
    ]);

    $event2 = Event::create([
        'title' => 'Evento Ativo',
        'description' => 'Acontecendo agora',
        'location' => 'Local 2',
        'start_datetime' => now()->subHour(),
        'end_datetime' => now()->addHours(3),
    ]);

    $participant1 = Participant::create([
        'name' => 'Participante Evento 1',
        'email' => 'part1@example.com',
        'state' => 'SP',
        'codigo' => 'AAA11',
        'event_id' => $event1->id,
    ]);

    $participant2 = Participant::create([
        'name' => 'Participante Evento 2',
        'email' => 'part2@example.com',
        'state' => 'RJ',
        'codigo' => 'BBB22',
        'event_id' => $event2->id,
    ]);

    $response = $this->post('/sorteio/sortear');

    $response->assertRedirect(route('draws.index'));

    $this->assertDatabaseHas('draws', [
        'participant_id' => $participant2->id,
        'event_id' => $event2->id,
    ]);

    $this->assertDatabaseMissing('draws', [
        'participant_id' => $participant1->id,
    ]);
});

test('email único por evento permite repetição em eventos diferentes', function () {
    $event1 = Event::create([
        'title' => 'Evento 1',
        'description' => 'Primeiro evento',
        'location' => 'Local 1',
        'start_datetime' => now()->subHour(),
        'end_datetime' => now()->addHours(2),
    ]);

    $event2 = Event::create([
        'title' => 'Evento 2',
        'description' => 'Segundo evento',
        'location' => 'Local 2',
        'start_datetime' => now()->addDays(5),
        'end_datetime' => now()->addDays(5)->addHours(3),
    ]);

    Participant::create([
        'name' => 'João Silva',
        'email' => 'joao@example.com',
        'state' => 'SP',
        'codigo' => 'AAA11',
        'event_id' => $event1->id,
    ]);

    $participant2 = Participant::create([
        'name' => 'João Silva',
        'email' => 'joao@example.com',
        'state' => 'SP',
        'codigo' => 'BBB22',
        'event_id' => $event2->id,
    ]);

    expect($participant2->id)->toBeInt();

    $participantsWithEmail = Participant::where('email', 'joao@example.com')->count();
    expect($participantsWithEmail)->toBe(2);
});

test('participante pode ser sorteado em múltiplos eventos', function () {
    $this->withSession(['draw_authenticated' => true]);

    $event1 = Event::create([
        'title' => 'Evento 1',
        'description' => 'Primeiro',
        'location' => 'Local 1',
        'start_datetime' => now()->subDays(10),
        'end_datetime' => now()->subDays(9),
    ]);

    $event2 = Event::create([
        'title' => 'Evento 2',
        'description' => 'Segundo',
        'location' => 'Local 2',
        'start_datetime' => now()->subDays(5),
        'end_datetime' => now()->subDays(4),
    ]);

    $participant1 = Participant::create([
        'name' => 'Maria Santos',
        'email' => 'maria@example.com',
        'state' => 'MG',
        'codigo' => 'AAA11',
        'event_id' => $event1->id,
    ]);

    $participant2 = Participant::create([
        'name' => 'Maria Santos',
        'email' => 'maria@example.com',
        'state' => 'MG',
        'codigo' => 'BBB22',
        'event_id' => $event2->id,
    ]);

    Draw::create([
        'participant_id' => $participant1->id,
        'event_id' => $event1->id,
    ]);

    Draw::create([
        'participant_id' => $participant2->id,
        'event_id' => $event2->id,
    ]);

    $draws = Draw::whereIn('event_id', [$event1->id, $event2->id])->count();
    expect($draws)->toBe(2);
});

test('mensagem de cadastros fechados quando não há evento ativo', function () {
    Event::create([
        'title' => 'Evento Futuro',
        'description' => 'Ainda não começou',
        'location' => 'Goiânia, GO',
        'start_datetime' => now()->addDays(20),
        'end_datetime' => now()->addDays(20)->addHours(4),
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Não há eventos acontecendo no momento');
});

test('mensagem de sorteios fechados quando não há evento ativo', function () {
    $this->withSession(['draw_authenticated' => true]);

    Event::create([
        'title' => 'Evento Futuro',
        'description' => 'Ainda não começou',
        'location' => 'Aracaju, SE',
        'start_datetime' => now()->addDays(15),
        'end_datetime' => now()->addDays(15)->addHours(3),
    ]);

    $response = $this->get('/sorteio');

    $response->assertStatus(200);
    $response->assertSee('Não há eventos acontecendo no momento');
});
