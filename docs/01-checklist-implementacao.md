# Checklist de Implementação - PHPeste 2025

## Descrição do Projeto
Aplicação web para cadastro de participantes do PHPeste 2025 em Parnaíba, Piauí. O sistema coleta nome, e-mail e estado de origem, gerando códigos únicos para sorteio de brindes.

## Stack Tecnológica
- PHP 8.4 com Laravel 12
- Laravel Sail para ambiente de desenvolvimento
- SQLite (desenvolvimento e produção)
- SQLite em memória (testes)
- Blade Templates
- Bootstrap 5
- Validação via Form Requests
- Notificações nativas do Laravel para envio de e-mail
- Mailpit para testes de e-mail
- Pest para testes

---

## Tarefas de Implementação

### 1. Configuração Inicial
- [x] Configurar banco de dados SQLite no `.env`
- [x] Configurar Mailpit para envio de e-mail no `.env`
- [x] Remover todas as migrations padrão do Laravel
- [x] Limpar rotas padrão do Laravel (`routes/web.php`)
- [x] Remover controllers/views padrão do Laravel

### 2. Banco de Dados - Migrations
- [x] Criar migration para tabela `participants` com colunas: `id`, `name`, `email`, `state`, `codigo`, `created_at`, `updated_at`
- [x] Adicionar unique constraint para `email` e `codigo` na tabela `participants`
- [x] Criar migration para tabela `draws` com colunas: `id`, `participant_id`, `created_at`, `updated_at`
- [x] Adicionar foreign key `participant_id` referenciando `participants.id` na tabela `draws`
- [x] Adicionar unique constraint para `participant_id` na tabela `draws` (participante não pode ser sorteado mais de uma vez)
- [x] Executar migrations

### 3. Models
- [x] Criar model `Participant` com fillable fields: `name`, `email`, `state`, `codigo`
- [x] Adicionar relacionamento `hasOne(Draw::class)` no model `Participant`
- [x] Criar model `Draw` com fillable field: `participant_id`
- [x] Adicionar relacionamento `belongsTo(Participant::class)` no model `Draw`

### 4. Form Request - Validação
- [x] Criar FormRequest `StoreParticipantRequest` com validação para:
  - `name`: obrigatório
  - `email`: obrigatório, formato válido, único na tabela `participants`
  - `state`: obrigatório, deve estar na lista de estados brasileiros
- [x] Criar lista de estados brasileiros para validação (AC, AL, AP, AM, BA, CE, DF, ES, GO, MA, MT, MS, MG, PA, PB, PR, PE, PI, RJ, RN, RS, RO, RR, SC, SP, SE, TO)

### 5. Controller - ParticipantController
- [x] Criar `ParticipantController` com método `index` (exibir formulário)
- [x] Implementar método `store`:
  - Validar dados via `StoreParticipantRequest`
  - Gerar código único de 5 letras maiúsculas
  - Verificar se código já existe no banco (loop até gerar único)
  - Salvar participante
  - Enviar notificação por e-mail
  - Redirecionar para página de sucesso com código
- [x] Criar método `success` para exibir página de agradecimento com código

### 6. Controller - DrawController
- [x] Criar `DrawController` com método `index`:
  - Buscar todos os sorteios já realizados (com dados do participante)
  - Exibir lista de sorteados
  - Exibir botão "Sortear"
- [x] Implementar método `draw`:
  - Buscar participantes que ainda não foram sorteados
  - Verificar se há participantes disponíveis
  - Se não houver, retornar mensagem "não há mais participantes disponíveis"
  - Sortear participante aleatoriamente
  - Salvar sorteio na tabela `draws`
  - Retornar dados do participante sorteado (nome, e-mail, estado)
- [x] Implementar método `showCode`:
  - Receber ID do sorteio
  - Retornar código do participante sorteado em destaque

### 7. Notificação - E-mail
- [x] Criar Notification `ParticipantRegistered` usando `php artisan make:notification`
- [x] Implementar método `via` retornando `['mail']`
- [x] Implementar método `toMail` com:
  - Assunto do e-mail
  - Texto explicativo sobre o cadastro
  - Código em destaque (usar markdown ou HTML)
- [x] Integrar envio da notificação no método `store` do `ParticipantController`

### 8. Views - Layout Base
- [x] Criar layout base Blade (`resources/views/layouts/app.blade.php`) com:
  - Tag HTML5 básica
  - Bootstrap 5 CDN (CSS e JS)
  - Cabeçalho do site
  - Seção de conteúdo (`@yield('content')`)
  - Rodapé com texto: "O código-fonte e os prompts que geraram este site estão disponíveis em https://github.com/leandrowferreira/claudioquefez"

### 9. Views - Formulário de Cadastro
- [x] Criar view `resources/views/participants/index.blade.php` com:
  - Formulário com método POST para `/`
  - Campo "Nome" (input text, obrigatório)
  - Campo "E-mail" (input email, obrigatório)
  - Campo "Estado" (select dropdown com todos os estados brasileiros, obrigatório)
  - Botão "Enviar"
  - Exibição de erros de validação abaixo de cada campo (padrão Bootstrap)

### 10. Views - Página de Sucesso
- [x] Criar view `resources/views/participants/success.blade.php` com:
  - Mensagem: "Obrigado por se inscrever, [Nome]! Guarde o código abaixo, ele será necessário para receber seu brinde no evento caso você seja sorteado."
  - Código em destaque (grande, centralizado)

### 11. Views - Sistema de Sorteio
- [x] Criar view `resources/views/draws/index.blade.blade.php` com:
  - Lista de participantes já sorteados (se houver)
  - Botão grande "Sortear"
  - Se houver sorteio realizado na sessão:
    - Exibir nome, e-mail e estado do participante sorteado
    - Botão "Exibir código"
    - Botão "Sortear novamente"
  - Se não houver mais participantes disponíveis:
    - Exibir mensagem "Não há mais participantes disponíveis para sorteio"
- [x] Implementar exibição do código ao clicar em "Exibir código" (pode ser via JavaScript toggle ou requisição POST)

### 12. Rotas
- [x] Criar rota GET `/` apontando para `ParticipantController@index` (formulário)
- [x] Criar rota POST `/` apontando para `ParticipantController@store` (processar cadastro)
- [x] Criar rota GET `/sucesso` apontando para `ParticipantController@success` (página de agradecimento)
- [x] Criar rota GET `/sorteio` apontando para `DrawController@index` (página de sorteio)
- [x] Criar rota POST `/sorteio/sortear` apontando para `DrawController@draw` (executar sorteio)
- [x] Criar rota POST `/sorteio/exibir-codigo` apontando para `DrawController@showCode` (exibir código)

### 13. Configuração de Testes
- [x] Instalar Pest (se não estiver instalado): `composer require pestphp/pest --dev`
- [x] Configurar `phpunit.xml` para usar SQLite em memória nos testes
- [x] Criar arquivo `tests/Pest.php` com configurações base (se necessário)

### 14. Testes - Cadastro de Participantes
- [x] Criar teste: exibir formulário de cadastro (GET `/`)
- [x] Criar teste: cadastro com dados válidos salva no banco e redireciona para sucesso
- [x] Criar teste: gerar código único de 5 letras maiúsculas
- [x] Criar teste: não permitir e-mail duplicado
- [x] Criar teste: validar campo nome obrigatório
- [x] Criar teste: validar campo e-mail obrigatório e formato válido
- [x] Criar teste: validar campo estado obrigatório e deve ser estado brasileiro válido
- [x] Criar teste: código gerado deve ser único (mesmo se houver colisão, gerar novo)
- [x] Criar teste: enviar notificação por e-mail após cadastro

### 15. Testes - Sistema de Sorteio
- [x] Criar teste: exibir página de sorteio (GET `/sorteio`)
- [x] Criar teste: sortear participante cadastrado e salvar em `draws`
- [x] Criar teste: participante sorteado não pode ser sorteado novamente
- [x] Criar teste: exibir lista de participantes já sorteados
- [x] Criar teste: exibir mensagem quando não há mais participantes disponíveis
- [x] Criar teste: exibir código do participante sorteado
- [x] Criar teste: permitir múltiplos sorteios

### 16. Executar Testes
- [x] Executar `./vendor/bin/pest` ou `php artisan test`
- [x] Garantir que todos os testes passam com sucesso

### 17. Documentação
- [x] Criar/Atualizar README.md com:
  - Descrição do projeto (PHPeste 2025)
  - Requisitos do sistema (PHP 8.4, Composer, Docker/Sail)
  - Instruções de instalação (`composer install`, `./vendor/bin/sail up`)
  - Instruções de configuração (`.env`, migrations)
  - Instruções para executar migrations (`./vendor/bin/sail artisan migrate`)
  - Instruções de execução (acessar `http://localhost`)
  - Instruções para executar testes (`./vendor/bin/sail artisan test`)
  - Informações sobre Mailpit para testes de e-mail (acessar `http://localhost:8025`)
  - Estrutura do projeto

### 18. Validação Final

**Metodologia de Testes**: Validação realizada via `curl` e `artisan tinker` devido à execução via Laravel Sail sem acesso direto ao browser. Os testes verificaram:
- Requisições HTTP com curl para validar respostas e conteúdo HTML
- Manipulação de dados via Eloquent no tinker para testar lógica de negócio
- API do Mailpit para confirmar envio de e-mails
- Inspeção de classes Bootstrap no HTML gerado

Resultados:
- [x] Testar fluxo completo de cadastro (formulário → validação → salvamento → e-mail → sucesso)
- [x] Testar validação de e-mail duplicado (UNIQUE constraint funcionando)
- [x] Testar geração de código único (6 participantes, 6 códigos únicos)
- [x] Testar sistema de sorteio completo (sorteio → exibir dados → exibir código → sortear novamente)
- [x] Testar que participante não é sorteado duas vezes (relacionamento hasOne impede duplicação)
- [x] Testar mensagem quando não há mais participantes disponíveis
- [x] Verificar e-mails no Mailpit (2 e-mails com assunto "Cadastro realizado - PHPeste 2025")
- [x] Verificar responsividade do layout Bootstrap (Bootstrap 5.3.0 carregado, classes aplicadas)
- [x] Validar exibição de erros no padrão Bootstrap (classes is-invalid e invalid-feedback implementadas)
- [x] Executar todos os testes e garantir que passam (18 testes, 45 assertions - 100% sucesso)
