# Como Executar Localmente — Sistema de Chamados

Guia para rodar o projeto no **Laragon**, **XAMPP** ou com `php artisan serve`, usando **MySQL** local.

---

## Requisitos

| Ferramenta | Versão mínima |
| --- | --- |
| PHP | 8.3+ |
| Composer | 2.x |
| Node.js | 18+ |
| MySQL | 8.0+ |
| NPM | 9+ |

Extensões PHP necessárias: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`.

---

## 1) Preparar ambiente

### 1.1 Clonar / acessar o projeto

```bash
cd c:/laragon/www/sistema-chamados
```

### 1.2 Copiar variáveis de ambiente

```bash
cp .env.example .env
```

### 1.3 Configurar banco de dados (Laragon)

No Laragon, crie o banco `sistema_chamados` pelo HeidiSQL ou:

```sql
CREATE DATABASE sistema_chamados CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

No arquivo `.env`, use o bloco **LOCAL**:

```env
APP_NAME="Sistema de Chamados"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

# LOCAL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_chamados
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database

# E-mail — padrão grava em storage/logs/laravel.log
MAIL_MAILER=log
```

> No Laragon, o MySQL geralmente roda na porta **3306** com usuário `root` e senha vazia.

### E-mail (SMTP)

O sistema envia e-mail ao **abrir chamado**, ao **finalizar** (link de avaliação) e na **recuperação de senha** do admin. Os envios passam pela fila — mantenha `php artisan queue:work` ou `queue:listen` ativo.

| Modo | Quando usar | Configuração |
| --- | --- | --- |
| **Log** (padrão) | Laragon sem SMTP | `MAIL_MAILER=log` — leia `storage/logs/laravel.log` |
| **Mailpit** | Testar envio real local | Instale [Mailpit](https://github.com/axllent/mailpit), UI em `:8025` |
| **SMTP empresa** | Homologação/produção | Veja `docs/IMPLANTACAO_EMPRESA.md` |

Exemplo com Mailpit local (`.env`):

```env
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM_ADDRESS="noreply@sistema-chamados.local"
MAILPIT_UI_URL=http://127.0.0.1:8025
```

---

## 2) Instalar dependências

```bash
composer install
npm install
```

---

## 3) Inicialização e migrations

```bash
php artisan key:generate
php artisan migrate --seed
npm run build
```

O comando `--seed` cria:

- 4 setores (Gerência de TI, Desenvolvimento, Telefonia/CFTV, Suporte Técnico/Infra)
- 1 administrador + 12 técnicos
- 3 chamados de demonstração

---

## 4) Rodar aplicação

### Servidor Laravel

```bash
php artisan serve
```

Acesse: **http://127.0.0.1:8000**

### Frontend em modo desenvolvimento (opcional)

Em outro terminal, para hot-reload do Vite:

```bash
npm run dev
```

### Atalho com tudo junto (recomendado)

```bash
composer dev
```

Este comando sobe simultaneamente: servidor, fila, logs e Vite.

---

## 5) Filas / Workers

Os e-mails de confirmação e avaliação usam fila. **É obrigatório** rodar o worker:

```bash
php artisan queue:work
```

Ou, para desenvolvimento contínuo:

```bash
php artisan queue:listen
```

---

## 6) Acessos

| Recurso | URL |
| --- | --- |
| Página inicial | http://127.0.0.1:8000 |
| Abrir chamado | http://127.0.0.1:8000/chamados/novo |
| Consultar chamado | http://127.0.0.1:8000/chamados/consultar |
| Painel administrativo | http://127.0.0.1:8000/admin |
| Redirecionamento painel | http://127.0.0.1:8000/painel |

### Credenciais de teste

```txt
Painel Administrativo
URL de login: http://127.0.0.1:8000/admin
E-mail: admin@admin.com
Senha: password
```

```txt
Técnico — Desenvolvimento
URL de login: http://127.0.0.1:8000/admin
E-mail: lucas-martins@chamados.local
Senha: password
```

```txt
Técnico — Suporte Técnico/Infra
URL de login: http://127.0.0.1:8000/admin
E-mail: marcos-silva@chamados.local
Senha: password
```

---

## 7) Comandos úteis

```bash
# Limpar caches
php artisan optimize:clear

# Recriar banco do zero
php artisan migrate:fresh --seed

# Rodar testes
php artisan test

# Qualidade de código
composer quality

# Ver informações do Laravel
php artisan about
```

---

## 8) Problemas comuns

| Problema | Solução |
| --- | --- |
| Erro de conexão MySQL | Verifique se o MySQL do Laragon está iniciado e se o banco `sistema_chamados` existe |
| Página sem estilo | Execute `npm run build` |
| E-mail não enviado | Rode `php artisan queue:work`. Com `MAIL_MAILER=log`, veja `storage/logs/laravel.log`. Com SMTP/Mailpit, confira host/porta no `.env` |
| Erro 500 após clone | Execute `composer install`, `php artisan key:generate` e `php artisan migrate` |
| Porta 8000 ocupada | Use `php artisan serve --port=8001` |

---

## Próximo passo

Para ambiente containerizado, consulte [COMO_EXECUTAR_DOCKER.md](COMO_EXECUTAR_DOCKER.md).
