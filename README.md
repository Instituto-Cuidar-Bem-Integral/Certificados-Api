# 🎓 Sistema de Certificados - Instituto Cuidar Bem

Sistema web completo para gerenciamento e validação de certificados.

## 📋 Funcionalidades

- ✅ **Cadastrar certificados** - Formulário completo com validação
- 📄 **Listar certificados** - Tabela com busca e filtros
- 🔍 **Validar certificados** - Verificação por hash único
- 🗑️ **Excluir certificados** - Remoção segura
- 📱 **QR Code** - Geração automática para validação
- 🎨 **Interface moderna** - Design responsivo e elegante

## 🚀 Instalação

### Pré-requisitos

- PHP 8.1+
- MySQL/MariaDB
- Composer

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
```

### Passo 4: Iniciar servidor

```bash
php -S localhost:8080
```

Acesse: http://localhost:8080

## 📁 Estrutura do Projeto

```
Cuidado-Integral-Api/
├── index.php          # Página principal
├── cadastrar.php      # Cadastro de certificados
├── listar.php         # Listagem de certificados
├── validar.php        # Validação de certificados
├── api.php            # API REST
├── sql/
│   └── schema.sql     # Estrutura do banco
├── src/
│   └── Certificate/
│       ├── CertificateDTO.php
│       ├── CertificateRepository.php
│       └── CertificateService.php
└── .env.example       # Configurações
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

## 🎨 Páginas

### 1. Página Principal (index.php)
- Menu de navegação
- Acesso rápido às funcionalidades

### 2. Cadastrar (cadastrar.php)
- Formulário completo
- Validação de campos
- Geração automática de hash

### 3. Listar (listar.php)
- Tabela de certificados
- Busca por nome/curso/hash
- Opções de visualização e exclusão

### 4. Validar (validar.php)
- Verificação por hash
- Exibição de dados completos
- QR Code para compartilhamento

## 🔒 Segurança

- Hash SHA-256 único para cada certificado
- Validação de entrada de dados
- Proteção contra SQL injection
- CORS configurado

## 📱 Responsividade

- Layout adaptativo para mobile
- Interface otimizada para tablets
- Experiência consistente em desktop

## 🛠️ Tecnologias

- **Backend**: PHP 8.3+
- **Banco**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **API**: REST JSON

## 📞 Suporte

Para suporte, entre em contato com o Instituto Cuidar Bem.

## 📄 Licença

© 2026 Instituto Cuidar Bem - Todos os direitos reservados