# 06 - Implementação de CRUD de Eventos e Sistema Dinâmico

## Contexto

Transformar o sistema de sorteio específico do PHPeste 2025 em uma aplicação genérica para gerenciar múltiplos eventos. O sistema deve permitir cadastrar, editar e listar eventos, detectar automaticamente qual evento está ativo baseado em data/hora, e migrar todos os dados hardcoded para o banco de dados.

## Objetivo

Implementar um CRUD completo de eventos com as seguintes características:
- Gerenciamento de múltiplos eventos
- Detecção automática de evento ativo baseado em data/hora
- Relacionamento entre participantes, sorteios e eventos
- Proteção de rotas administrativas com autenticação
- Interface para navegação entre CRUD e sorteio
- Migração de dados do PHPeste 2025 via seeder
- Remoção de todas as referências hardcoded no código

---

## Checklist de Implementação

### 1. Banco de Dados - Migration de Eventos
- [x] Criar migration `create_events_table` com as colunas:
  - `id` (bigint, auto increment, primary key)
  - `title` (string, not null) - Título do evento
  - `description` (text, nullable) - Descrição opcional do evento
  - `location` (string, nullable) - Local do evento (ex: "Parnaíba, Piauí")
  - `start_datetime` (datetime, not null) - Data/hora de início
  - `end_datetime` (datetime, not null) - Data/hora de término
  - `created_at` (timestamp)
  - `updated_at` (timestamp)
- [x] Criar índice em `start_datetime` e `end_datetime` para otimizar consultas de eventos ativos

### 2. Banco de Dados - Migrations de Relacionamento
- [x] Criar migration para adicionar `event_id` na tabela `participants`:
  - Adicionar coluna `event_id` (bigint unsigned, not null, default 1)
  - Adicionar foreign key referenciando `events.id` com `onDelete('cascade')`
  - Adicionar índice em `event_id`
- [x] Criar migration para adicionar `event_id` na tabela `draws`:
  - Adicionar coluna `event_id` (bigint unsigned, not null, default 1)
  - Adicionar foreign key referenciando `events.id` com `onDelete('cascade')`
  - Adicionar índice em `event_id`
- [x] Atualizar constraint unique de `participant_id` em `draws` para ser composto: `unique(['participant_id', 'event_id'])`
  - Remover constraint `unique('participant_id')` antiga
  - Adicionar nova constraint: participante pode ser sorteado uma vez por evento
- [x] Executar migrations: `sail artisan migrate`

### 3. Model - Event
- [x] Criar model `Event` com `sail artisan make:model Event`
- [x] Definir fillable: `['title', 'description', 'location', 'start_datetime', 'end_datetime']`
- [x] Adicionar casts para datas:
  ```php
  protected $casts = [
      'start_datetime' => 'datetime',
      'end_datetime' => 'datetime',
  ];
  ```
- [x] Adicionar relacionamento `hasMany(Participant::class)` no model `Event`
- [x] Adicionar relacionamento `hasMany(Draw::class)` no model `Event`
- [x] Criar método estático `getActiveEvent()` que:
  - Busca evento onde `now()` está entre `start_datetime` e `end_datetime`
  - Retorna o primeiro evento encontrado ou `null`
  - Ordenar por `start_datetime DESC` (caso haja sobreposição, pegar o mais recente)

### 4. Models - Atualizar Relacionamentos
- [x] No model `Participant`:
  - Adicionar `event_id` no fillable
  - Adicionar relacionamento `belongsTo(Event::class)`
  - Manter relacionamento `hasOne(Draw::class)` existente
- [x] No model `Draw`:
  - Adicionar `event_id` no fillable
  - Adicionar relacionamento `belongsTo(Event::class)`
  - Manter relacionamento `belongsTo(Participant::class)` existente

### 5. Migration de Dados - PHPeste 2025
- [x] Criar migration `sail artisan make:migration insert_phpeste_2025_event`
- [x] Implementar no método `up()`:
  ```php
  // Forçar ID 1 para o evento PHPeste 2025
  DB::table('events')->insert([
      'id' => 1,
      'title' => 'PHPeste 2025',
      'description' => 'Conferência de PHP no Nordeste',
      'location' => 'Parnaíba, Piauí',
      'start_datetime' => '2025-10-03 17:00:00',
      'end_datetime' => '2025-10-03 20:00:00',
      'created_at' => now(),
      'updated_at' => now(),
  ]);
  ```
- [x] Executar migration: `sail artisan migrate`
- [x] **Notas importantes**:
  - Com `default 1` nas colunas `event_id`, os registros existentes de participants e draws já estarão automaticamente vinculados ao evento ID 1
  - Esta é uma migration de dados que não deve ser revertida em produção
  - O rollback das migrations de relacionamento (que removem as colunas `event_id`) já garantem a limpeza estrutural

### 6. Form Request - Validação de Eventos
- [x] Criar FormRequest `StoreEventRequest` com `sail artisan make:request StoreEventRequest`
- [x] Definir `authorize()` retornando `true`
- [x] Implementar regras de validação:
  - `title`: obrigatório, string, max 255 caracteres
  - `description`: opcional, string
  - `location`: opcional, string, max 255 caracteres
  - `start_datetime`: obrigatório, formato datetime válido
  - `end_datetime`: obrigatório, formato datetime válido, after:start_datetime
- [x] Criar FormRequest `UpdateEventRequest` (mesmo conteúdo)
- [x] Adicionar mensagens personalizadas de erro em português

### 7. Controller - EventController (CRUD)
- [x] Criar `EventController` com `sail artisan make:controller EventController --resource`
- [x] Implementar método `index`:
  - Buscar todos os eventos ordenados por `start_datetime DESC`
  - Retornar view `events.index` com lista de eventos
- [x] Implementar método `create`:
  - Retornar view `events.create` com formulário vazio
- [x] Implementar método `store`:
  - Validar com `StoreEventRequest`
  - Criar evento no banco
  - Redirecionar para `events.index` com mensagem de sucesso
- [x] Implementar método `edit($id)`:
  - Buscar evento por ID
  - Retornar view `events.edit` com dados do evento
- [x] Implementar método `update($id)`:
  - Validar com `UpdateEventRequest`
  - Atualizar evento no banco
  - Redirecionar para `events.index` com mensagem de sucesso
- [x] Implementar método `destroy($id)`:
  - Buscar evento por ID
  - Deletar evento (cascade deletará participantes e sorteios)
  - Redirecionar para `events.index` com mensagem de sucesso
- [x] Implementar método `show($id)`:
  - Buscar evento com contagem de participantes e sorteios
  - Retornar view `events.show` com detalhes do evento

### 8. Atualizar Controllers Existentes

#### ParticipantController
- [x] Atualizar método `index`:
  - Verificar se existe evento ativo usando `Event::getActiveEvent()`
  - Se não houver evento ativo, exibir mensagem: "Não há eventos acontecendo no momento. Cadastros estão fechados."
  - Passar variável `$event` para a view
- [x] Atualizar método `store`:
  - Verificar se existe evento ativo
  - Se não houver, redirecionar com erro
  - Adicionar `event_id` ao criar participante:
    ```php
    $participant = Participant::create([
        'name' => $request->name,
        'email' => $request->email,
        'state' => $request->state,
        'codigo' => $codigo,
        'event_id' => $event->id,
    ]);
    ```
  - Passar `$event` para a notificação

#### DrawController
- [x] Atualizar método `index`:
  - Verificar se existe evento ativo
  - Se não houver, exibir mensagem: "Não há eventos acontecendo no momento. Sorteios estão fechados."
  - Buscar sorteios apenas do evento ativo: `Draw::where('event_id', $event->id)->with('participant')->get()`
  - Passar variável `$event` para a view
- [x] Atualizar método `draw`:
  - Verificar se existe evento ativo
  - Se não houver, retornar erro
  - Buscar participantes do evento ativo que não foram sorteados:
    ```php
    $participants = Participant::where('event_id', $event->id)
        ->whereDoesntHave('draw', function($q) use ($event) {
            $q->where('event_id', $event->id);
        })
        ->get();
    ```
  - Adicionar `event_id` ao criar sorteio

### 9. Atualizar Form Request - StoreParticipantRequest
- [x] Modificar validação de `email` para ser unique apenas dentro do evento:
  ```php
  'email' => [
      'required',
      'email',
      Rule::unique('participants')->where(function ($query) {
          $event = Event::getActiveEvent();
          return $query->where('event_id', $event?->id);
      }),
  ],
  ```

### 10. Atualizar Notificação - ParticipantRegistered
- [x] Modificar construtor para receber `Event` como parâmetro:
  ```php
  public function __construct(
      public Participant $participant,
      public Event $event
  ) {}
  ```
- [x] Atualizar método `toMail` para usar dados dinâmicos do evento:
  - Assunto: `"Cadastro realizado - {$this->event->title}"`
  - Texto: referenciar `$this->event->title` em vez de "PHPeste 2025"
- [x] Atualizar chamada da notificação em `ParticipantController::store`

### 11. Views - Layout Base (Menu de Navegação)
- [x] Atualizar `resources/views/layouts/app.blade.php`:
  - Adicionar menu de navegação Bootstrap com:
    - Link "Cadastro" (`/`)
    - Link "Sorteio" (`/sorteio`) - visível apenas se autenticado
    - Link "Gerenciar Eventos" (`/eventos`) - visível apenas se autenticado
  - Adicionar verificação de sessão para mostrar/ocultar links protegidos:
    ```php
    @if(session('draw_authenticated'))
        <a href="/eventos">Gerenciar Eventos</a>
        <a href="/sorteio">Sorteio</a>
    @endif
    ```

### 12. Views - CRUD de Eventos

#### Index (Lista)
- [x] Criar view `resources/views/events/index.blade.php`:
  - Tabela Bootstrap listando todos os eventos
  - Colunas: Título, Local, Data/Hora Início, Data/Hora Fim, Status (Ativo/Encerrado/Futuro), Ações
  - Botão "Novo Evento" no topo
  - Links de ação para cada evento: Visualizar, Editar, Deletar (com confirmação JavaScript)
  - Badge visual para evento ativo (verde) / encerrado (cinza) / futuro (azul)

#### Create (Formulário de Criação)
- [x] Criar view `resources/views/events/create.blade.php`:
  - Formulário POST para `/eventos`
  - Campo "Título" (input text, required)
  - Campo "Descrição" (textarea, opcional)
  - Campo "Local" (input text, opcional)
  - Campo "Data/Hora de Início" (input datetime-local, required)
  - Campo "Data/Hora de Término" (input datetime-local, required)
  - Botão "Salvar"
  - Botão "Cancelar" (volta para lista)
  - Exibir erros de validação no padrão Bootstrap

#### Edit (Formulário de Edição)
- [x] Criar view `resources/views/events/edit.blade.php`:
  - Mesmo formulário do create, mas com método PUT para `/eventos/{id}`
  - Campos preenchidos com dados do evento
  - Usar `@method('PUT')` do Blade

#### Show (Detalhes)
- [x] Criar view `resources/views/events/show.blade.php`:
  - Exibir todos os dados do evento
  - Estatísticas: total de participantes, total de sorteados
  - Botão "Editar"
  - Botão "Voltar"

### 13. Atualizar Views Existentes (Remover Hardcode)

#### resources/views/participants/index.blade.php
- [x] Substituir "PHPeste 2025" por `{{ $event?->title ?? 'Cadastro de Participantes' }}`
- [x] Se não houver evento ativo (`!$event`), exibir mensagem de cadastros fechados e ocultar formulário

#### resources/views/participants/success.blade.php
- [x] Substituir "PHPeste 2025" por `{{ $event->title }}`
- [x] Usar `{{ $event->title }}` na mensagem de agradecimento

#### resources/views/draws/index.blade.php
- [x] Substituir "PHPeste 2025" por `{{ $event?->title ?? 'Sistema de Sorteio' }}`
- [x] Se não houver evento ativo (`!$event`), exibir mensagem de sorteios fechados e ocultar botão de sortear

#### resources/views/layouts/app.blade.php
- [x] Substituir título fixo "PHPeste 2025" por dinâmico:
  ```blade
  <title>{{ $event->title ?? 'Sistema de Sorteios' }}</title>
  ```
- [x] No cabeçalho, usar evento ativo se disponível

### 14. Rotas - CRUD de Eventos
- [x] Adicionar em `routes/web.php`:
  ```php
  Route::middleware('check.draw.password')->group(function () {
      Route::resource('eventos', EventController::class);
  });
  ```
- [x] Todas as rotas de eventos protegidas por autenticação
- [x] Usar nomenclatura resource: index, create, store, show, edit, update, destroy

### 15. Testes - CRUD de Eventos

#### Testes de Model e Relacionamentos
- [x] Criar teste: evento pode ser criado com dados válidos
- [x] Criar teste: relacionamento `Event` → `Participant` funciona corretamente
- [x] Criar teste: relacionamento `Event` → `Draw` funciona corretamente
- [x] Criar teste: `getActiveEvent()` retorna evento ativo baseado em data/hora
- [x] Criar teste: `getActiveEvent()` retorna null quando não há evento ativo
- [x] Criar teste: deletar evento deleta participantes e sorteios (cascade)

#### Testes de Validação
- [x] Criar teste: validar campo `title` obrigatório
- [x] Criar teste: validar campo `start_datetime` obrigatório e formato válido
- [x] Criar teste: validar campo `end_datetime` obrigatório e after:start_datetime

#### Testes de Controller - CRUD
- [x] Criar teste: listar todos os eventos (GET `/eventos`)
- [x] Criar teste: exibir formulário de criação (GET `/eventos/create`)
- [x] Criar teste: criar evento com dados válidos (POST `/eventos`)
- [x] Criar teste: exibir detalhes de um evento (GET `/eventos/{id}`)
- [x] Criar teste: exibir formulário de edição (GET `/eventos/{id}/edit`)
- [x] Criar teste: atualizar evento (PUT `/eventos/{id}`)
- [x] Criar teste: deletar evento (DELETE `/eventos/{id}`)
- [x] Criar teste: rotas de CRUD requerem autenticação (middleware)

#### Testes de Integração - Sistema Completo
- [x] Criar teste: participante só pode se cadastrar se houver evento ativo
- [x] Criar teste: sortear apenas participantes do evento ativo
- [x] Criar teste: e-mail único por evento (pode repetir em eventos diferentes)
- [x] Criar teste: participante pode ser sorteado em múltiplos eventos
- [x] Criar teste: mensagem de cadastros fechados quando não há evento ativo
- [x] Criar teste: mensagem de sorteios fechados quando não há evento ativo

**Resultado**: Criado arquivo `tests/Feature/EventTest.php` com 23 testes

### 16. Atualizar Testes Existentes
- [x] Atualizar todos os testes de `ParticipantController`:
  - Criar um evento ativo no `beforeEach()`
  - Adicionar import de `Event`
  - Ajustar texto esperado de "Cadastro de Participantes" para "PHPeste 2025"
- [x] Atualizar todos os testes de `DrawController`:
  - Criar um evento ativo no `beforeEach()`
  - Adicionar import de `Event`
  - Adicionar `event_id` em todos os Participants e Draws criados (11 testes atualizados)
- [x] Criar migration para atualizar constraint unique de email (permitir repetição por evento)
- [x] Executar `sail artisan test` e garantir que todos passam

**Resultado Final**: ✅ **45 testes passando com 116 assertions**

### 17. Validação de Remoção de Hardcode
- [ ] Buscar no código por "PHPeste" ou "phpeste" (case-insensitive):
  ```bash
  grep -ri "phpeste" app/ resources/ --exclude-dir=vendor
  ```
- [ ] Garantir que nenhuma referência hardcoded existe (exceto em seeders/docs)
- [ ] Buscar por data "2025-10-03" no código:
  ```bash
  grep -r "2025-10-03" app/ resources/ --exclude-dir=vendor
  ```
- [ ] Garantir que datas estão apenas em seeders

### 18. Melhorias de UX/UI
- [ ] Adicionar feedback visual para operações CRUD (mensagens flash Bootstrap):
  - "Evento criado com sucesso"
  - "Evento atualizado com sucesso"
  - "Evento deletado com sucesso"
- [ ] Adicionar confirmação JavaScript antes de deletar evento:
  ```javascript
  onclick="return confirm('Tem certeza que deseja deletar este evento? Todos os participantes e sorteios serão removidos.')"
  ```
- [ ] Adicionar paginação na lista de eventos (se necessário)
- [ ] Formatar datas nas views usando Carbon:
  ```blade
  {{ $event->start_datetime->format('d/m/Y H:i') }}
  ```

### 19. Documentação
- [ ] Atualizar README.md com:
  - Seção "Gerenciamento de Eventos"
  - Instruções para acessar CRUD de eventos
  - Explicação sobre evento ativo (baseado em data/hora)
  - Instruções para executar seeder do PHPeste 2025
- [ ] Atualizar este documento (06-implementacao-crud-eventos.md) com:
  - Arquivos criados
  - Arquivos modificados
  - Resultados dos testes
  - Commits realizados

### 20. Validação Final
- [ ] Executar todos os testes: `sail artisan test`
- [ ] Testar fluxo completo de CRUD via interface:
  - Criar novo evento
  - Editar evento
  - Visualizar detalhes
  - Deletar evento
- [ ] Testar cadastro de participante:
  - Com evento ativo: deve funcionar
  - Sem evento ativo: deve mostrar mensagem
- [ ] Testar sorteio:
  - Com evento ativo: deve funcionar
  - Sem evento ativo: deve mostrar mensagem
- [ ] Testar múltiplos eventos:
  - Criar evento 1 (ativo)
  - Cadastrar participantes
  - Criar evento 2 (futuro)
  - Verificar que participantes são do evento 1
- [ ] Testar proteção de rotas:
  - Acessar `/eventos` sem autenticação
  - Deve redirecionar para senha
- [ ] Verificar e-mails no Mailpit com título dinâmico do evento
- [ ] Executar `sail artisan route:list` para verificar todas as rotas

---

## Estrutura de Arquivos Criados

```
app/
├── Http/
│   ├── Controllers/
│   │   └── EventController.php
│   └── Requests/
│       ├── StoreEventRequest.php
│       └── UpdateEventRequest.php
├── Models/
│   └── Event.php
database/
├── migrations/
│   ├── XXXX_XX_XX_create_events_table.php
│   ├── XXXX_XX_XX_add_event_id_to_participants_table.php
│   └── XXXX_XX_XX_add_event_id_to_draws_table.php
└── seeders/
    └── EventSeeder.php
resources/
└── views/
    └── events/
        ├── index.blade.php
        ├── create.blade.php
        ├── edit.blade.php
        └── show.blade.php
tests/
└── Feature/
    ├── EventCrudTest.php
    ├── EventRelationshipTest.php
    └── EventIntegrationTest.php
```

## Arquivos Modificados

```
app/
├── Http/
│   └── Controllers/
│       ├── ParticipantController.php
│       └── DrawController.php
├── Models/
│   ├── Participant.php
│   └── Draw.php
└── Notifications/
    └── ParticipantRegistered.php
resources/
└── views/
    ├── layouts/
    │   └── app.blade.php
    ├── participants/
    │   ├── index.blade.php
    │   └── success.blade.php
    └── draws/
        └── index.blade.php
routes/
└── web.php
tests/
└── Feature/
    ├── ParticipantTest.php
    └── DrawTest.php
database/
└── seeders/
    └── DatabaseSeeder.php
README.md
```

## Comandos Úteis

```bash
# Criar migration
sail artisan make:migration create_events_table

# Criar model
sail artisan make:model Event

# Criar controller
sail artisan make:controller EventController --resource

# Criar request
sail artisan make:request StoreEventRequest

# Criar seeder
sail artisan make:seeder EventSeeder

# Executar migrations
sail artisan migrate

# Executar seeder específico
sail artisan db:seed --class=EventSeeder

# Executar testes
sail artisan test

# Listar rotas
sail artisan route:list

# Buscar hardcode no código
grep -ri "phpeste" app/ resources/ --exclude-dir=vendor
grep -r "2025-10-03" app/ resources/ --exclude-dir=vendor
```

## Estimativa de Complexidade

- **Migrations e Models**: Média (relacionamentos e constraints)
- **CRUD Controller**: Baixa (padrão Laravel)
- **Views**: Média (4 views com Bootstrap)
- **Atualização de Controllers**: Média (lógica de evento ativo)
- **Testes**: Alta (~20-30 novos testes)
- **Refatoração**: Média (remover hardcode, passar variáveis)

**Tempo estimado**: 4-6 horas de desenvolvimento

---

## Notas Técnicas

### Detecção de Evento Ativo

A lógica de detecção usa comparação de timestamps:

```php
public static function getActiveEvent(): ?Event
{
    return self::where('start_datetime', '<=', now())
               ->where('end_datetime', '>=', now())
               ->orderBy('start_datetime', 'desc')
               ->first();
}
```

### Constraint Unique Composta

O participante pode ser sorteado uma vez **por evento**:

```php
$table->unique(['participant_id', 'event_id']);
```

### Cascade Delete

Ao deletar um evento, participantes e sorteios relacionados são removidos automaticamente:

```php
$table->foreign('event_id')
      ->references('id')
      ->on('events')
      ->onDelete('cascade');
```

### Validação Contextual de E-mail

E-mail deve ser único apenas dentro do mesmo evento:

```php
Rule::unique('participants')->where(function ($query) {
    $event = Event::getActiveEvent();
    return $query->where('event_id', $event?->id);
})
```

---

## Resultados Esperados

Ao final da implementação:

1. ✅ Sistema genérico para múltiplos eventos
2. ✅ Dados do PHPeste 2025 migrados para banco via seeder
3. ✅ CRUD completo de eventos protegido por autenticação
4. ✅ Detecção automática de evento ativo
5. ✅ Zero referências hardcoded no código
6. ✅ Participantes e sorteios vinculados a eventos
7. ✅ Mensagens dinâmicas em todas as views
8. ✅ E-mails com título do evento dinâmico
9. ✅ Menu de navegação entre funcionalidades
10. ✅ Testes completos (estimado: 40-50 testes totais)

---

## Melhorias Implementadas Durante a Validação

### Route Model Binding
- Configurado mapeamento de parâmetros nas rotas: `eventos` → `event`
- Resolve problema de Missing required parameter nas rotas resource

### View de Detalhes do Evento (show)
- Adicionada tabela com lista de sorteados do evento
- Exibe: data/hora, nome, email, estado e código
- Facilita visualização rápida dos ganhadores

### View de Edição do Evento (edit)
- **Evento futuro**: permite editar todos os campos
- **Evento em andamento ou passado**:
  - Bloqueia edição de título, descrição, local e data de início
  - Permite apenas alterar data/hora de término
  - Exibe alerta informativo ao usuário
  - Envia campos bloqueados via hidden inputs

### Código Formatado
- Executado Laravel Pint
- Corrigidos 6 problemas de estilo
- Adicionado `00_extras/` ao .gitignore

---

## Melhorias Futuras Possíveis

- [ ] Dashboard administrativo com estatísticas
- [ ] Exportação de dados (CSV/Excel) por evento
- [ ] Múltiplos sorteios por evento (categorias)
- [ ] API REST para integração externa
- [ ] Sistema de tags/categorias para eventos
- [ ] Notificações de início/fim de evento
- [ ] Interface de preview do evento (visão do participante)
- [ ] Relatórios em PDF
