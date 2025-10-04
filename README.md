# PHPeste 2025 - Sistema de Cadastro e Sorteio

Sistema web para cadastro de participantes e sorteio de brindes do evento PHPeste 2025 em Parnaíba, Piauí.

Este projeto foi desenvolvido como demonstração de uma aplicação Laravel implementada com a ajuda do Claude Code.

## 📋 Sobre o Projeto

Aplicação web que permite:
- Cadastro de participantes com nome, e-mail e estado de origem
- Geração automática de códigos únicos para cada participante
- Envio de e-mail de confirmação com o código
- Sistema de sorteio de participantes protegido por senha
- Controle para evitar sorteio duplicado do mesmo participante
- Exibição de histórico de sorteados

## 🚀 Stack Tecnológica

- **Backend**: PHP 8.4 com Laravel 12
- **Banco de Dados**: SQLite
- **Frontend**: Blade Templates + Bootstrap 5
- **Ambiente**: Laravel Sail (Docker)
- **Testes**: Pest (SQLite em memória)
- **E-mail**: Notificações nativas do Laravel + Mailpit

## 📦 Requisitos do Sistema

- Docker e Docker Compose
- Git
- Portas disponíveis: 80 (app), 1025 (SMTP), 8025 (Mailpit)

## 🔧 Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/leandrowferreira/claudioquefez.git
cd claudioquefez
```

### 2. Inicie os containers com Sail

```bash
./vendor/bin/sail up -d
```

### 3. Instale as dependências

```bash
./vendor/bin/sail composer install
```

### 4. Copie o arquivo de ambiente

```bash
cp .env.example .env
```

**Importante**: O arquivo `.env.example` já contém as configurações necessárias para o projeto funcionar com SQLite e Mailpit. Certifique-se de copiar este arquivo para `.env` antes de prosseguir.

A senha padrão para acesso ao sistema de sorteio está definida em `DRAW_PASSWORD="123mudar :)"`.

### 5. Gere a chave da aplicação

```bash
./vendor/bin/sail artisan key:generate
```

### 6. Crie o banco de dados SQLite

```bash
touch database/database.sqlite
```

### 7. Execute as migrations

**Importante**: Este passo é obrigatório para criar as tabelas no banco de dados.

```bash
./vendor/bin/sail artisan migrate
```

## 🎯 Uso da Aplicação

### Acessar a Aplicação

- **Site**: http://localhost
- **Mailpit** (visualizar e-mails): http://localhost:8025

### Funcionalidades

#### Cadastro de Participantes
1. Acesse http://localhost
2. Preencha o formulário com nome, e-mail e estado
3. Após o cadastro, um código único de 5 letras será gerado
4. Um e-mail de confirmação será enviado (veja no Mailpit)

#### Sistema de Sorteio
1. Acesse http://localhost/sorteio
2. Informe a senha de administrador (padrão: `123mudar :)`)
3. Clique no botão "Sortear Participante"
4. O sistema sorteará aleatoriamente um participante ainda não sorteado
5. Clique em "Exibir Código" para ver o código do sorteado
6. A senha fica salva na sessão durante o uso

## 🧪 Executar Testes

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
- ✅ 22 testes no total com 56 assertions

## 📁 Estrutura do Projeto

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ParticipantController.php  # Cadastro de participantes
│   │   │   └── DrawController.php          # Sistema de sorteio
│   │   ├── Middleware/
│   │   │   └── CheckDrawPassword.php       # Autenticação do sorteio
│   │   └── Requests/
│   │       └── StoreParticipantRequest.php # Validação do formulário
│   ├── Models/
│   │   ├── Participant.php                 # Model de participante
│   │   └── Draw.php                        # Model de sorteio
│   └── Notifications/
│       └── ParticipantRegistered.php       # E-mail de confirmação
├── database/
│   └── migrations/                         # Migrations do banco
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php               # Layout base
│       ├── participants/                   # Views de participantes
│       └── draws/                          # Views de sorteio
│           ├── index.blade.php             # Tela de sorteio
│           └── password.blade.php          # Formulário de senha
├── routes/
│   └── web.php                             # Rotas da aplicação
└── tests/
    └── Feature/
        ├── ParticipantTest.php             # Testes de participantes
        └── DrawTest.php                    # Testes de sorteio
```

## 🗄️ Banco de Dados

### Tabela: participants
- `id`: ID auto-incremento
- `name`: Nome do participante
- `email`: E-mail (único)
- `state`: Estado de origem (sigla)
- `codigo`: Código único de 5 letras (único)
- `created_at`, `updated_at`: Timestamps

### Tabela: draws
- `id`: ID auto-incremento
- `participant_id`: FK para participants (único)
- `created_at`, `updated_at`: Timestamps

## 🔐 Segurança

### Senha do Sistema de Sorteio

O acesso ao sistema de sorteio é protegido por senha. Configure a variável de ambiente:

```bash
DRAW_PASSWORD="123mudar :)"
```

A senha é validada via middleware e mantida na sessão do usuário.

## 📧 Configuração de E-mail

O projeto usa Mailpit para capturar e visualizar e-mails em desenvolvimento:

- **SMTP**: localhost:1025
- **Interface Web**: http://localhost:8025

Todos os e-mails enviados pela aplicação podem ser visualizados no Mailpit.

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
```

## 🤖 Desenvolvimento com IA

Este projeto foi desenvolvido como demonstração das capacidades do Claude Code. O código-fonte e os prompts utilizados estão disponíveis neste repositório.

## 📄 Licença

Este projeto é open-source sob a licença MIT.

## 👥 Autor

Desenvolvido para demonstração no PHPeste 2025 - Parnaíba, Piauí.

---

**Código-fonte e prompts**: https://github.com/leandrowferreira/claudioquefez
