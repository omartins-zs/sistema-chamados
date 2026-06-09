# Template de Importação — Sistema de Chamados

Use este guia para **importar o projeto em outra máquina**, **novo ambiente** ou **após publicar no GitHub/GitLab**.

> **Sim, funciona.** O repositório Git está organizado e completo. O que **não** vai no Git (`.env`, `vendor/`, `node_modules/`, `public/build`) precisa ser gerado na máquina de destino — isso é normal em projetos Laravel.

---

## O que o Git já traz

| Incluído no repositório | Não incluído (gerar na importação) |
| --- | --- |
| Código-fonte (`app/`, `database/`, `resources/`, etc.) | `.env` (copiar de `.env.example`) |
| Migrations e seeders | `vendor/` (`composer install`) |
| Testes e CI (`.github/workflows/`) | `node_modules/` (`npm install`) |
| Docker (`docker-compose.yml`, `docker/`) | `public/build/` (`npm run build`) |
| Documentação (`docs/`, `README.md`) | `APP_KEY` (`php artisan key:generate`) |
| Assets Filament publicados (`public/css`, `public/js`, `public/fonts`) | Banco MySQL criado + migrations |

---

## Escolha o cenário

| Cenário | Seção |
| --- | --- |
| Clonar do GitHub/GitLab | [1) Importar via Git](#1-importar-via-git) |
| Copiar pasta / ZIP | [2) Importar via pasta ou ZIP](#2-importar-via-pasta-ou-zip) |
| Rodar no Laragon (local) | [3) Setup local — checklist](#3-setup-local--checklist-laragon) |
| Rodar com Docker | [4) Setup Docker — checklist](#4-setup-docker--checklist) |
| Validar se está OK | [5) Validação final](#5-validação-final) |

---

## 1) Importar via Git

### 1.1 Clonar

```bash
git clone https://github.com/SEU_USUARIO/sistema-chamados.git
cd sistema-chamados
```

Substitua a URL pela do seu repositório remoto.

### 1.2 Conferir histórico (opcional)

```bash
git log --oneline
```

Você deve ver commits no padrão `:emoji: tipo: descrição`, por exemplo:

```text
:tada: init: inicializando base Laravel
:card_file_box: data: criando migrations seeders
:sparkles: feat: criando camada de dominio
...
```

### 1.3 Seguir o setup

- **Laragon / local:** vá para a [seção 3](#3-setup-local--checklist-laragon)
- **Docker:** vá para a [seção 4](#4-setup-docker--checklist)

---

## 2) Importar via pasta ou ZIP

Se recebeu o projeto compactado (sem `.git`):

```bash
# Exemplo Laragon
cd c:/laragon/www
# Descompacte para: sistema-chamados
cd sistema-chamados
```

Depois siga o mesmo checklist da seção 3 ou 4.

> Sem pasta `.git`, você não terá histórico de commits — apenas o código. Para versionar depois: `git init` e commits conforme [COMMITS_ANALISE.md](COMMITS_ANALISE.md).

---

## 3) Setup local — checklist (Laragon)

Copie e execute na ordem. Marque cada item ao concluir.

```bash
# [ ] 1. Entrar na pasta do projeto
cd c:/laragon/www/sistema-chamados

# [ ] 2. Criar .env
cp .env.example .env

# [ ] 3. Criar banco MySQL (HeidiSQL ou SQL)
# CREATE DATABASE sistema_chamados CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# [ ] 4. Ajustar .env — bloco LOCAL (exemplo Laragon)
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=sistema_chamados
# DB_USERNAME=root
# DB_PASSWORD=
# APP_URL=http://127.0.0.1:8000
# APP_LOCALE=pt_BR

# [ ] 5. Instalar dependências PHP
composer install

# [ ] 6. Instalar dependências front-end
npm install

# [ ] 7. Chave da aplicação
php artisan key:generate

# [ ] 8. Banco + dados demo
php artisan migrate --seed

# [ ] 9. Compilar assets públicos (Tailwind/Vite)
npm run build

# [ ] 10. Subir aplicação
php artisan serve
```

### Terminal extra — fila de e-mails

```bash
# [ ] 11. Worker de fila (obrigatório para notificações)
php artisan queue:work
```

### Atalho (tudo junto em dev)

```bash
composer dev
```

### Acessos após importação

| Recurso | URL |
| --- | --- |
| Site público | http://127.0.0.1:8000 |
| Abrir chamado | http://127.0.0.1:8000/chamados/novo |
| Painel admin | http://127.0.0.1:8000/admin |

**Admin:** `admin@admin.com` / `password`

Mais credenciais em [ACESSOS_TESTES.md](ACESSOS_TESTES.md).

---

## 4) Setup Docker — checklist

```bash
# [ ] 1. Entrar na pasta
cd c:/laragon/www/sistema-chamados

# [ ] 2. Criar .env Docker
cp .env.docker.example .env

# [ ] 3. Conferir APP_URL e DB no .env
# APP_URL=http://localhost:8080
# DB_HOST=mysql
# DB_PORT=3306

# [ ] 4. Subir containers
docker compose up -d --build

# [ ] 5. Aguardar MySQL (primeira vez pode levar ~30s)
docker compose ps

# [ ] 6. Migrations + seed (se o entrypoint não rodou)
docker compose exec app php artisan migrate --seed

# [ ] 7. Build front (se necessário)
docker compose exec app npm install
docker compose exec app npm run build
```

### Acessos Docker

| Recurso | URL |
| --- | --- |
| Aplicação | http://localhost:8080 |
| phpMyAdmin | http://localhost:8085 |
| MySQL (host) | `127.0.0.1:3308` |

Detalhes em [COMO_EXECUTAR_DOCKER.md](COMO_EXECUTAR_DOCKER.md).

---

## 5) Validação final

Execute após importar. Se tudo passar, a importação funcionou.

```bash
# Testes automatizados
php artisan test

# Qualidade (Pint + PHPStan + testes)
composer quality
```

### Checklist manual

| Item | Esperado |
| --- | --- |
| Página inicial abre | Formulário / links visíveis com CSS |
| `/chamados/novo` | Formulário de abertura de chamado |
| `/admin` | Tela de login Filament |
| Login admin | Entra com `admin@admin.com` |
| Dashboard | Widgets e menu lateral carregam |
| `/admin/configuracoes` | Página de configurações abre |
| Criar chamado público | Redireciona para página de sucesso |

### Se algo falhar

| Sintoma | Correção rápida |
| --- | --- |
| Erro 500 | `composer install` + `php artisan key:generate` + `php artisan migrate` |
| Sem CSS no site público | `npm install` + `npm run build` |
| Erro de banco | MySQL ligado + banco `sistema_chamados` criado + `.env` correto |
| Login admin falha | `php artisan migrate:fresh --seed` |
| E-mail não aparece | `php artisan queue:work` (com `MAIL_MAILER=log`, ver `storage/logs/laravel.log`) |
| Permissão storage | `php artisan optimize:clear` |

---

## Template rápido — colar no terminal (local)

```bash
cd c:/laragon/www/sistema-chamados
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Em outro terminal:

```bash
cd c:/laragon/www/sistema-chamados
php artisan queue:work
```

---

## Template rápido — colar no terminal (Docker)

```bash
cd c:/laragon/www/sistema-chamados
cp .env.docker.example .env
docker compose up -d --build
docker compose exec app php artisan migrate --seed
```

Acesse: http://localhost:8080

---

## Publicar no GitHub (primeira vez)

Se o repositório ainda está só na sua máquina:

```bash
# Criar repo vazio no GitHub (sem README, sem .gitignore)
git remote add origin https://github.com/SEU_USUARIO/sistema-chamados.git
git branch -M main
git push -u origin main
```

Quem clonar depois usa a [seção 1](#1-importar-via-git) + checklist local ou Docker.

---

## Documentação relacionada

| Arquivo | Conteúdo |
| --- | --- |
| [COMO_EXECUTAR_LOCAL.md](COMO_EXECUTAR_LOCAL.md) | Guia completo Laragon |
| [COMO_EXECUTAR_DOCKER.md](COMO_EXECUTAR_DOCKER.md) | Guia completo Docker |
| [ACESSOS_TESTES.md](ACESSOS_TESTES.md) | URLs e credenciais de teste |
| [IMPLANTACAO_EMPRESA.md](IMPLANTACAO_EMPRESA.md) | Deploy em produção |
| [COMMITS_ANALISE.md](COMMITS_ANALISE.md) | Histórico e padrão de commits |

---

## Resumo

| Pergunta | Resposta |
| --- | --- |
| O Git que foi criado funciona para importar? | **Sim** |
| Basta dar `git clone` e abrir no navegador? | **Não** — precisa `composer install`, `.env`, migrations e `npm run build` |
| Os dados demo vêm no clone? | **Sim**, via `php artisan migrate --seed` |
| O `.env` vai junto? | **Não** — use `.env.example` ou `.env.docker.example` |
| Funciona no Laragon e no Docker? | **Sim** — ambos documentados |
