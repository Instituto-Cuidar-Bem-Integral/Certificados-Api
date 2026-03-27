#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${1:-/var/www/Cuidado-Integral-Api}"

echo "🚀 Iniciando deploy em: $APP_DIR"

cd "$APP_DIR"

# Pull das últimas alterações
echo "📥 Baixando atualizações..."
git pull origin main

# Instalar dependências
echo "📦 Instalando dependências..."
composer install --no-dev --optimize-autoloader

# Configurar permissões
echo "🔐 Configurando permissões..."
chmod -R 755 "$APP_DIR"
chown -R www-data:www-data "$APP_DIR"

# Criar .env se não existir
if [ ! -f .env ]; then
    echo "⚙️ Criando arquivo .env..."
    cp .env.example .env
    echo "✏️ Configure o arquivo .env com suas credenciais!"
fi

# Executar migrations se existir
if [ -f sql/schema.sql ]; then echo "🗄️ Configurando banco de dados..."
    mysql -u root -p < sql/schema.sql 2>/dev/null || echo "⚠️ Banco já existe ou erro na configuração"
fi

# Reiniciar servidor web
echo "🔄 Reiniciando servidor..."
sudo systemctl reload nginx 2>/dev/null || sudo systemctl reload apache2 2>/dev/null || echo "⚠️ Servidor web não encontrado"

echo "✅ Deploy concluído com sucesso!"
echo "🌐 Acesse: http://$(hostname -I | awk '{print $1}')"