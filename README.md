# 🎓 Sistema de Certificados - Instituto Cuidar Bem

Sistema web completo para gerenciamento e validação de certificados com interface SPA.

## 📋 Funcionalidades

- ✅ **Cadastrar certificados** - Formulário completo com validação
- 📄 **Listar certificados** - Tabela com busca e filtros
- 🔍 **Validar certificados** - Verificação por hash único
- 🗑️ **Excluir certificados** - Remoção segura
- 📱 **QR Code** - Geração automática para validação
- 📄 **Relatório PDF** - Geração de certificados em PDF via mPDF
- 🎨 **Interface SPA** - Single Page Application moderna
- 🔧 **Configuração Nginx** - Otimizado para produção

## 🚀 Instalação

### Pré-requisitos

- PHP 8.1+
- MySQL/MariaDB
- Composer
- Extensão GD (para mPDF)

### Passo 1: Clonar o repositório

```bash
git clone <url-do-repositorio>
cd Cuidado-Integral-Api
```

### Passo 2: Instalar dependências

```bash
composer install
```

### Passo 3: Configurar banco de dados

1. Criar o banco de dados:

```bash
mysql -u root -p < sql/schema.sql
```

2. Configurar variáveis de ambiente:

```bash
cp .env.example .env
```

3. Editar `.env` com suas credenciais:

```env
DB_HOST=localhost
DB_NAME=certificados
DB_USER=root
DB_PASS=sua_senha
CERT_SECRET=sua_chave_secreta
```

### Passo 4: Iniciar servidor

```bash
php -S localhost:8080
```

Acesse: http://localhost:8080

## 📁 Estrutura do Projeto

```
Cuidado-Integral-Api/
├── index.html          # Página principal (SPA)
├── api.php             # API REST
├── pages/
│   ├── cadastrar.php   # Formulário de cadastro
│   ├── listar.php      # Listagem de certificados
│   └── validar.php     # Validação por hash
├── public/
│   ├── certificado.php # Geração de PDF via mPDF
│   ├── validar.php     # Validação pública
│   └── assets/         # Imagens e recursos
├── config/
│   └── conexao.php     # Configuração de conexão
├── src/
│   └── Certificate/
│       ├── CertificateDTO.php
│       ├── CertificateRepository.php
│       └── CertificateService.php
├── sql/
│   └── schema.sql      # Estrutura do banco
├── deploy/
│   ├── nginx/
│   │   └── certificados.conf  # Configuração Nginx
│   └── vps/
│       └── deploy.sh   # Script de deploy
├── README.md           # Documentação
└── .env.example        # Configurações
```

## 🔌 API Endpoints

### Listar certificados
```
GET /api.php?action=list
```

### Criar certificado
```
POST /api.php?action=create
Content-Type: application/json

{
  "nome_aluno": "João Silva",
  "cpf": "123.456.789-00",
  "nome_curso": "Primeiros Socorros",
  "carga_horaria": "40",
  "data_conclusao": "2026-03-27",
  "instrutor": "Maria Santos"
}
```

### Validar certificado
```
GET /api.php?action=validate&hash=<hash_do_certificado>
```

### Excluir certificado
```
DELETE /api.php?action=delete&id=<id_do_certificado>
```

### Gerar PDF do certificado
```
GET /public/certificado.php?h=<hash_do_certificado>
```

## 🎨 Páginas (SPA)

### 1. Menu Principal (index.html)
- Interface SPA com navegação dinâmica
- Cards interativos para cada funcionalidade
- Carregamento assíncrono das páginas

### 2. Cadastrar (pages/cadastrar.php)
- Formulário completo com validação
- Formatação automática de CPF
- Geração automática de hash

### 3. Listar (pages/listar.php)
- Tabela de certificados
- Busca por nome/curso/hash
- Opções de visualização e exclusão

### 4. Validar (pages/validar.php)
- Verificação por hash
- Exibição de dados completos
- QR Code para compartilhamento

## 📄 Relatório PDF

O sistema gera certificados em PDF profissional via mPDF:
- Layout A4 paisagem
- Design elegante com bordas
- Logo da instituição
- Assinaturas configuráveis
- QR Code integrado

## 🔧 Configuração Nginx

Configuração otimizada para produção:
- Roteamento SPA (fallback para index.html)
- Cache de QR Codes (7 dias)
- Proteção de arquivos sensíveis
- FastCGI para PHP

## 🔒 Segurança

- Hash SHA-256 único para cada certificado
- Validação de entrada de dados
- Proteção contra SQL injection
- CORS configurado
- Chave secreta para hash HMAC

## 📱 Responsividade

- Layout adaptativo para mobile
- Interface otimizada para tablets
- Experiência consistente em desktop
- Navegação SPA fluida

## 🛠️ Tecnologias

- **Backend**: PHP 8.3+
- **Banco**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (SPA)
- **API**: REST JSON
- **PDF**: mPDF
- **QR Code**: API pública
- **Web Server**: Nginx

## 📞 Suporte

Para suporte, entre em contato com o Instituto Cuidar Bem.

## 📄 Licença

© 2026 Instituto Cuidar Bem - Todos os direitos reservados