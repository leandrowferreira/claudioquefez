#!/bin/bash

set -e

echo "=========================================="
echo "  Instalação do Sistema de Sorteio"
echo "=========================================="
echo ""

# Verificar se o Docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Docker não encontrado. Por favor, instale o Docker primeiro."
    exit 1
fi

# Verificar se o Docker Compose está disponível
if ! docker compose version &> /dev/null; then
    echo "❌ Docker Compose não encontrado. Por favor, instale o Docker Compose primeiro."
    exit 1
fi

echo "✓ Docker e Docker Compose encontrados"
echo ""

# Passo 1: Verificar se já está em um diretório do projeto
if [ ! -f "composer.json" ]; then
    echo "❌ Erro: Execute este script na raiz do projeto (onde está o composer.json)"
    exit 1
fi

echo "✓ Projeto encontrado"
echo ""

# Passo 2: Instalar dependências (primeiro via Docker, se necessário)
if [ ! -d "vendor" ]; then
    echo "📥 Instalando dependências do Composer (primeira vez)..."
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php84-composer:latest \
        composer install --ignore-platform-reqs
    echo "✓ Dependências instaladas"
else
    echo "✓ Dependências já instaladas"
fi

echo ""

# Passo 3: Iniciar containers com Sail
echo "📦 Iniciando containers Docker..."
./vendor/bin/sail up -d

echo ""
echo "✓ Containers iniciados"
echo ""

# Passo 4: Copiar arquivo de ambiente
if [ ! -f ".env" ]; then
    echo "📄 Copiando arquivo de ambiente..."
    cp .env.example .env
    echo "✓ Arquivo .env criado"
else
    echo "⚠️  Arquivo .env já existe, mantendo o atual"
fi

echo ""

# Passo 5: Gerar chave da aplicação
echo "🔑 Gerando chave da aplicação..."
./vendor/bin/sail artisan key:generate

echo ""
echo "✓ Chave gerada"
echo ""

# Passo 6: Criar banco de dados SQLite
if [ ! -f "database/database.sqlite" ]; then
    echo "🗄️  Criando banco de dados SQLite..."
    touch database/database.sqlite
    echo "✓ Banco de dados criado"
else
    echo "⚠️  Banco de dados já existe, mantendo o atual"
fi

echo ""

# Passo 7: Executar migrations
echo "🔄 Executando migrations..."
./vendor/bin/sail artisan migrate --force

echo ""
echo "✓ Migrations executadas"
echo ""

# Resumo final
echo "=========================================="
echo "  ✅ Instalação concluída com sucesso!"
echo "=========================================="
echo ""
echo "🌐 Acesse a aplicação:"
echo "   - Site: http://localhost"
echo "   - Mailpit: http://localhost:8025"
echo ""
echo "🔐 Senha padrão do sistema de sorteio:"
echo "   123mudar :)"
echo ""
echo "📝 Para parar os containers:"
echo "   ./vendor/bin/sail down"
echo ""
echo "🧪 Para executar os testes:"
echo "   ./vendor/bin/sail artisan test"
echo ""
