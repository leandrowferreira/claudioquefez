# Sistema de Cadastro e Sorteio de Eventos

Sistema web para gerenciamento de múltiplos eventos com cadastro de participantes e sorteio de brindes.

**Desenvolvido inicialmente para o PHPeste 2025** em Parnaíba, Piauí, este projeto foi criado como demonstração de uma aplicação Laravel implementada com a ajuda do Claude Code. **Toda a documentação do processo de desenvolvimento, incluindo prompts utilizados e checklists de implementação, está disponível no diretório `/docs`**.

## 📋 Sobre o Projeto

Aplicação web que permite:
- Gerenciamento de múltiplos eventos (CRUD completo)
- Detecção automática de evento ativo baseado em data/hora
- Cadastro de participantes com nome, e-mail e estado de origem
- Geração automática de códigos únicos para cada participante
- Envio de e-mail de confirmação com o código e título do evento
- Sistema de sorteio de participantes protegido por senha
- Controle para evitar sorteio duplicado do mesmo participante por evento
- Exibição de histórico de sorteados por evento
- Interface administrativa para gestão de eventos

## 🚀 Stack Tecnológica

- **Backend**: PHP 8.4 com Laravel 12
- **Banco de Dados**: SQLite
- **Frontend**: Blade Templates + Bootstrap 5
- **Ambiente**: Laravel Sail (Docker)
- **E-mail**: Notificações nativas do Laravel + Mailpit
- **Qualidade de Código**: Laravel Pint

## 📦 Requisitos do Sistema

- Docker e Docker Compose
- Git
- Portas disponíveis: 80 (app), 1025 (SMTP), 8025 (Mailpit)

## 🔧 Instalação

### Instalação Automática (Recomendado)

Clone o repositório e execute o script de instalação:

```bash
git clone https://github.com/leandrowferreira/claudioquefez.git
cd claudioquefez
./install.sh
```

O script automaticamente:
- ✅ Instala dependências do Composer
- ✅ Inicia containers Docker com Sail
- ✅ Cria arquivo `.env`
- ✅ Gera chave da aplicação
- ✅ Cria banco de dados SQLite
- ✅ Executa migrations

### Instalação Manual

<details>
<summary>Clique para ver os passos manuais</summary>

#### 1. Clone o repositório

```bash
git clone https://github.com/leandrowferreira/claudioquefez.git
cd claudioquefez
```

#### 2. Instale as dependências do Composer (primeira vez)

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

#### 3. Inicie os containers com Sail

```bash
./vendor/bin/sail up -d
```

#### 4. Copie o arquivo de ambiente

```bash
cp .env.example .env
```

**Importante**: O arquivo `.env.example` já contém as configurações necessárias para o projeto funcionar com SQLite e Mailpit.

A senha padrão para acesso ao sistema de sorteio está definida em `DRAW_PASSWORD="123mudar :)"`.

#### 5. Gere a chave da aplicação

```bash
./vendor/bin/sail artisan key:generate
```

#### 6. Crie o banco de dados SQLite

```bash
touch database/database.sqlite
```

#### 7. Execute as migrations

```bash
./vendor/bin/sail artisan migrate
```

Este passo criará:
- Tabela de eventos
- Tabela de participantes (vinculada a eventos)
- Tabela de sorteios (vinculada a eventos e participantes)
- Evento inicial PHPeste 2025 (ID 1)

</details>

## 🎯 Uso da Aplicação

### Acessar a Aplicação

- **Site**: http://localhost
- **Mailpit** (visualizar e-mails): http://localhost:8025

### Funcionalidades

#### Cadastro de Participantes
1. Acesse http://localhost
2. Preencha o formulário com nome, e-mail e estado
3. Após o cadastro, um código único de 5 letras será gerado
4. Um e-mail de confirmação será enviado com o título do evento (veja no Mailpit)
5. O cadastro só funciona quando há um evento ativo (data/hora atual entre início e término)

#### Sistema de Sorteio
1. Acesse http://localhost/sorteio
2. Informe a senha de administrador (padrão: `123mudar :)`)
3. Clique no botão "Sortear Participante"
4. O sistema sorteará aleatoriamente um participante ainda não sorteado do evento ativo
5. Clique em "Exibir Código" para ver o código do sorteado
6. A senha fica salva na sessão durante o uso

#### Gerenciamento de Eventos
1. Acesse http://localhost/sorteio (autentique-se se necessário)
2. Clique em "Gerenciar Eventos" no menu
3. Funcionalidades disponíveis:
   - **Listar eventos**: visualize todos os eventos com status (Ativo/Futuro/Encerrado)
   - **Criar evento**: cadastre um novo evento com título, descrição, local e datas
   - **Visualizar evento**: veja detalhes, estatísticas e lista de sorteados
   - **Editar evento**:
     - Eventos futuros: edite todos os campos
     - Eventos em andamento/passados: edite apenas a data de término
   - **Deletar evento**: remove evento e todos os dados relacionados (cascade)

### Detecção de Evento Ativo

O sistema detecta automaticamente qual evento está ativo baseado na data/hora atual:
- Participantes só podem se cadastrar quando há um evento ativo
- Sorteios só ocorrem para o evento ativo
- Mesmo email pode se cadastrar em eventos diferentes
- Participantes podem ser sorteados uma vez por evento

## 📁 Estrutura do Projeto

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── EventController.php         # CRUD de eventos
│   │   │   ├── ParticipantController.php   # Cadastro de participantes
│   │   │   └── DrawController.php          # Sistema de sorteio
│   │   ├── Middleware/
│   │   │   └── CheckDrawPassword.php       # Autenticação do sorteio
│   │   └── Requests/
│   │       ├── StoreEventRequest.php       # Validação de eventos
│   │       ├── UpdateEventRequest.php      # Validação de edição
│   │       └── StoreParticipantRequest.php # Validação do formulário
│   ├── Models/
│   │   ├── Event.php                       # Model de evento
│   │   ├── Participant.php                 # Model de participante
│   │   └── Draw.php                        # Model de sorteio
│   └── Notifications/
│       └── ParticipantRegistered.php       # E-mail de confirmação
├── database/
│   └── migrations/                         # Migrations do banco
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php               # Layout base com navegação
│       ├── events/                         # Views de eventos
│       │   ├── index.blade.php             # Lista de eventos
│       │   ├── create.blade.php            # Formulário de criação
│       │   ├── edit.blade.php              # Formulário de edição
│       │   └── show.blade.php              # Detalhes e sorteados
│       ├── participants/                   # Views de participantes
│       └── draws/                          # Views de sorteio
│           ├── index.blade.php             # Tela de sorteio
│           └── password.blade.php          # Formulário de senha
├── routes/
│   └── web.php                             # Rotas da aplicação
└── docs/                                   # Documentação do desenvolvimento
```

## 🗄️ Banco de Dados

### Tabela: events
- `id`: ID auto-incremento
- `title`: Título do evento
- `description`: Descrição opcional
- `location`: Local do evento
- `start_datetime`: Data/hora de início
- `end_datetime`: Data/hora de término
- `created_at`, `updated_at`: Timestamps

### Tabela: participants
- `id`: ID auto-incremento
- `name`: Nome do participante
- `email`: E-mail (unique por evento)
- `state`: Estado de origem (sigla)
- `codigo`: Código único de 5 letras (único)
- `event_id`: FK para events (cascade delete)
- `created_at`, `updated_at`: Timestamps

### Tabela: draws
- `id`: ID auto-incremento
- `participant_id`: FK para participants
- `event_id`: FK para events (cascade delete)
- `created_at`, `updated_at`: Timestamps
- Constraint unique: (participant_id, event_id) - um sorteio por evento

## 🔐 Segurança

### Senha do Sistema de Sorteio

O acesso ao sistema de sorteio e gerenciamento de eventos é protegido por senha. Configure a variável de ambiente:

```bash
DRAW_PASSWORD="123mudar :)"
```

A senha é validada via middleware e mantida na sessão do usuário.

### Proteção de Rotas

- Rotas públicas: cadastro de participantes
- Rotas protegidas (requerem autenticação):
  - Sistema de sorteio
  - CRUD de eventos

## 📧 Configuração de E-mail

O projeto usa Mailpit para capturar e visualizar e-mails em desenvolvimento:

- **SMTP**: localhost:1025
- **Interface Web**: http://localhost:8025

Os e-mails de confirmação são personalizados com o título do evento ativo.

## 🛠️ Comandos Úteis

```bash
# Iniciar containers
./vendor/bin/sail up -d

# Parar containers
./vendor/bin/sail down

# Ver logs
./vendor/bin/sail logs -f

# Acessar bash do container
./vendor/bin/sail bash

# Executar Artisan
./vendor/bin/sail artisan [comando]

# Executar migrations
./vendor/bin/sail artisan migrate

# Limpar e recriar banco
./vendor/bin/sail artisan migrate:fresh

# Formatar código com Pint
./vendor/bin/sail pint

# Listar rotas
./vendor/bin/sail artisan route:list

# Executar testes
./vendor/bin/sail artisan test
```

## 🧪 Testes

Execute a suite de testes com Pest:

```bash
./vendor/bin/sail artisan test
```

Ou usando o Pest diretamente:

```bash
./vendor/bin/sail pest
```

### Cobertura de Testes

- ✅ 9 testes de cadastro de participantes
- ✅ 11 testes de sistema de sorteio (incluindo autenticação)
- ✅ 23 testes de CRUD e integração de eventos
- ✅ **45 testes no total com 116 assertions**

## 🎨 Características Técnicas

### Route Model Binding
Configurado mapeamento personalizado para rotas em português:
```php
Route::resource('eventos', EventController::class)->parameters([
    'eventos' => 'event',
]);
```

### Validação Contextual
- Email único por evento (permite repetição em eventos diferentes)
- Datas validadas (end_datetime > start_datetime)
- Detecção automática de evento ativo via método estático

### Edição Inteligente
- Eventos futuros: todos os campos editáveis
- Eventos iniciados: apenas data de término editável
- Interface adaptativa com feedback visual

### Relacionamentos Eloquent
- Event → hasMany(Participant, Draw)
- Participant → belongsTo(Event), hasOne(Draw)
- Draw → belongsTo(Event, Participant)
- Cascade delete configurado

## 🤖 Desenvolvimento com IA

Este projeto foi desenvolvido como demonstração das capacidades do Claude Code. Todo o código-fonte e a documentação do processo de desenvolvimento estão disponíveis neste repositório.

### Documentação do Desenvolvimento
- `/docs/00-prompt-inicial.md` - Especificação inicial do projeto
- `/docs/06-implementacao-crud-eventos.md` - Checklist completo da implementação

## 📄 Licença

Este projeto é open-source sob a licença MIT.

## 👥 Autor

Desenvolvido para demonstração no PHPeste 2025 - Parnaíba, Piauí.

---

**Código-fonte e documentação**: https://github.com/leandrowferreira/claudioquefez
