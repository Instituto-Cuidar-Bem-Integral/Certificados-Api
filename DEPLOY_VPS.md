# Deploy da API em VPS com Nginx

Este projeto roda com:

- PHP 8.2+
- PHP-FPM
- MariaDB/MySQL
- Nginx

Os exemplos abaixo assumem uma VPS Ubuntu/Debian com acesso `sudo`.

## 1. Instalar os pacotes base

```bash
sudo apt update
sudo apt install -y mariadb-server php-fpm php-cli php-mysql php-gd php-xml php-mbstring php-curl unzip curl git
```

## 2. Instalar o Composer

```bash
cd /tmp
curl -sS https://getcomposer.org/installer -o composer-setup.php
HASH="$(curl -sS https://composer.github.io/installer.sig)"
php -r "if (hash_file('sha384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
composer --version
```

## 3. Publicar os arquivos do projeto

O repositório inteiro agora é a aplicação publicada pelo Nginx.

```bash
/var/www/Cuidado-Integral-Api
```

Faça o clone assim:

```bash
sudo mkdir -p /var/www
cd /var/www
sudo git clone https://github.com/Instituto-Cuidar-Bem-Integral/Cuidado-Integral-Api.git
cd /var/www/Cuidado-Integral-Api
```

Se você tinha a estrutura antiga com a subpasta `certificados/`, remova só depois de confirmar que tudo já está funcionando na raiz do projeto:

```bash
sudo rm -rf /var/www/Cuidado-Integral-Api/certificados
```

## 4. Instalar as dependências PHP

```bash
cd /var/www/Cuidado-Integral-Api
COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader
```

Se você usar um usuário comum de deploy em vez de `root`, pode rodar só `composer install --no-dev --optimize-autoloader`.

## 5. Criar o banco

Importe o schema pronto:

```bash
sudo mysql < /var/www/Cuidado-Integral-Api/sql/schema.sql
```

Depois crie um usuário exclusivo da aplicação:

```bash
sudo mysql
```

```sql
CREATE USER 'cert_user'@'127.0.0.1' IDENTIFIED BY 'troque-essa-senha';
GRANT ALL PRIVILEGES ON certificados.* TO 'cert_user'@'127.0.0.1';
FLUSH PRIVILEGES;
EXIT;
```

## 6. Configurar o `.env`

```bash
cd /var/www/Cuidado-Integral-Api
cp .env.example .env
nano .env
```

Exemplo:

```env
DB_HOST=127.0.0.1
DB_NAME=certificados
DB_USER=cert_user
DB_PASS=troque-essa-senha
DB_CHARSET=utf8mb4
CERT_SECRET=coloque-uma-chave-bem-grande-e-aleatoria
```

## 7. Ajustar permissões

O PHP precisa conseguir gravar os QR codes em `public/qrcodes`.

```bash
cd /var/www/Cuidado-Integral-Api
mkdir -p public/qrcodes
sudo chown -R www-data:www-data public/qrcodes
sudo chmod -R 775 public/qrcodes
sudo find /var/www/Cuidado-Integral-Api -type d -exec chmod 755 {} \;
sudo find /var/www/Cuidado-Integral-Api -type f -exec chmod 644 {} \;
sudo chmod -R 775 public/qrcodes
```

## 8. Configurar o Nginx

Copie o arquivo de exemplo deste repositório:

```bash
sudo cp /var/www/Cuidado-Integral-Api/deploy/nginx/certificados.conf /etc/nginx/sites-available/certificados
sudo nano /etc/nginx/sites-available/certificados
```

Ajuste estes dois pontos:

- `server_name` para o seu domínio ou subdomínio
- `fastcgi_pass` para a versão real do PHP-FPM instalada

Para descobrir o socket do PHP-FPM:

```bash
ls /run/php/
```

Depois habilite o site:

```bash
sudo ln -s /etc/nginx/sites-available/certificados /etc/nginx/sites-enabled/certificados
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
sudo systemctl enable nginx
sudo systemctl enable mariadb
sudo systemctl enable php8.3-fpm
```

Se a sua VPS estiver com `php8.2-fpm`, troque o nome do serviço no comando acima.

## 9. HTTPS com Certbot

Quando o domínio já estiver apontando para a VPS:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d api.seudominio.com
```

## 10. URLs para testar

- `http://SEU-DOMINIO/admin/cadastrar.php`
- `http://SEU-DOMINIO/admin/listar.php`

Se o cadastro funcionar, a API/painel já está conectada ao banco e gerando QR code.

## 11. Deploy automatico no push para `main`

O repositório já tem um workflow em:

```bash
.github/workflows/deploy-certificados.yml
```

Ele faz isso a cada `push` para `main`:

- sincroniza a raiz do repositório para a VPS
- preserva `.env`
- preserva `public/qrcodes/`
- roda `composer install --no-dev --optimize-autoloader` no servidor

### 11.1. Criar um usuario de deploy na VPS

Exemplo:

```bash
sudo adduser deploy
sudo usermod -aG www-data deploy
sudo chown -R deploy:www-data /var/www/Cuidado-Integral-Api
sudo chmod -R 775 /var/www/Cuidado-Integral-Api/public/qrcodes
```

### 11.2. Gerar chave SSH para o GitHub Actions

Na sua maquina local:

```bash
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/github_actions_deploy
```

### 11.3. Adicionar a chave publica na VPS

```bash
ssh deploy@IP_DA_VPS "mkdir -p ~/.ssh && chmod 700 ~/.ssh"
cat ~/.ssh/github_actions_deploy.pub | ssh deploy@IP_DA_VPS "cat >> ~/.ssh/authorized_keys && chmod 600 ~/.ssh/authorized_keys"
```

### 11.4. Cadastrar os secrets no GitHub

No repositório GitHub, abra:

`Settings > Secrets and variables > Actions`

Crie estes `Secrets`:

- `VPS_HOST`: IP ou dominio da VPS
- `VPS_USER`: usuario SSH, por exemplo `deploy`
- `VPS_SSH_KEY`: conteudo completo da chave privada `~/.ssh/github_actions_deploy`
- `VPS_PORT`: porta SSH, normalmente `22`

Opcional em `Variables`:

- `VPS_PATH`: caminho de deploy, por padrao `/var/www/Cuidado-Integral-Api`

### 11.5. Primeiro deploy

Depois de configurar os secrets, faça um push na `main`.

Se quiser disparar agora:

```bash
git add .
git commit -m "chore: add vps deploy workflow"
git push origin main
```

### 11.6. Observacao importante

O setup inicial da VPS continua manual:

- instalar Nginx, PHP-FPM, Composer e MariaDB
- criar o banco
- configurar o `.env`
- configurar o `nginx`

Depois disso, os proximos pushes para `main` fazem o deploy automaticamente.
