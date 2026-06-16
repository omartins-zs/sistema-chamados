# Como Executar com Docker — Sistema de Chamados

Guia para rodar o projeto com **Docker Desktop** no Windows, Linux ou macOS.

---

## Início rápido

```bash
cp .env.example .env
docker compose up -d --build
```

Na **primeira execução**, o container **`init`** instala dependências (Composer + NPM), roda migrations/seeders e faz o build do Vite. **Pode levar de 5 a 15 minutos** — acompanhe:

```bash
docker compose logs -f init
```

Quando aparecer `[bootstrap] Concluído — aplicação pronta.`, os demais containers sobem automaticamente.

Se algo falhar, veja o erro completo:

```bash
docker compose logs init
```

| Recurso | URL |
| --- | --- |
| Aplicação | http://localhost:8080 |
| Painel admin | http://localhost:8080/admin |
| PHPMyAdmin | http://localhost:8085 |
| Mailpit (e-mails) | http://localhost:8025 |

> **Um único `.env`** serve para Laragon e Docker. O `docker-compose.yml` sobrescreve `DB_HOST`, `REDIS_*`, `MAIL_*` etc. dentro dos containers — **não precisa editar o `.env` para usar Docker**.

---

## Stack e Containers

| Container | Porta (host) | Função |
| --- | --- | --- |
| `chamados-init` | — | Bootstrap único (composer, migrate, npm build) |
| `chamados-nginx` | **8080** | Servidor web |
| `chamados-app` | — | PHP 8.3-FPM |
| `chamados-mysql` | **3308** | MySQL 8 |
| `chamados-phpmyadmin` | **8085** | Interface MySQL |
| `chamados-mailpit` | **8025** / **1025** | Caixa de e-mails de teste |
| `chamados-redis` | 6379 | Cache, sessão e filas |
| `chamados-worker` | — | Fila (`queue:work`) |
| `chamados-scheduler` | — | Agendador Laravel |

---

## Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado e em execução
- Portas livres: **8080**, **3308**, **8085**, **8025**, **6379**

---

## Credenciais

### MySQL / PHPMyAdmin

| Campo | Valor |
| --- | --- |
| Host (do seu PC) | `127.0.0.1:3308` |
| Host (dentro do Docker) | `mysql:3306` |
| Banco | `sistema_chamados` |
| Usuário | `chamados` |
| Senha | `chamados` |
| Root | `root` / `root` |

### Aplicação

| Perfil | E-mail | Senha |
| --- | --- | --- |
| Administrador | admin@admin.com | `password` |
| Técnico | lucas-martins@chamados.local | `password` |

Lista completa: [ACESSOS_TESTES.md](ACESSOS_TESTES.md)

---

## Comandos úteis

```bash
# Status dos containers
docker compose ps

# Recriar banco do zero
docker compose exec app php artisan migrate:fresh --seed

# Limpar caches após mudar .env
docker compose exec app php artisan optimize:clear
docker compose exec app sh docker/scripts/rebuild-cache.sh

# Testes
docker compose exec app php artisan test

# Shell no container
docker compose exec app bash

# Parar (mantém dados)
docker compose down

# Parar e apagar volumes (apaga banco!)
docker compose down -v
```

### Reinstalar dependências manualmente

Normalmente não é necessário — o bootstrap faz isso na primeira subida. Se precisar:

```bash
docker compose exec app composer install
docker compose exec app npm ci && npm run build
docker compose exec app sh docker/scripts/bootstrap.sh
```

---

## Problemas comuns

| Problema | Solução |
| --- | --- |
| `chamados-app is unhealthy` / nginx/worker reiniciando | Veja `docker compose logs init` — aguarde o bootstrap terminar. Se falhar: `docker compose down -v`, confirme `.env` (`copy .env.example .env`) e `docker compose up -d --build` |
| Porta 8080 em uso | Altere em `docker-compose.yml`: `"8081:80"` e `APP_URL` no bloco `environment` do serviço `app` |
| Página em branco / 502 | `docker compose logs app` — aguarde o bootstrap terminar |
| Página sem CSS | `docker compose exec app npm run build` |
| MySQL não sobe | `docker compose logs mysql` — aguarde healthcheck |
| Erro de permissão | `docker compose exec app chmod -R 775 storage bootstrap/cache` |
| E-mail não chega | Verifique `chamados-worker` com `docker compose logs worker` e abra http://localhost:8025 |
| Mudou `.env` e não refletiu | `docker compose exec app php artisan optimize:clear` e `docker compose restart app` |

---

## Voltar para Laragon

```bash
docker compose down
```

O `.env` já está configurado para local (`DB_HOST=127.0.0.1`). Siga [COMO_EXECUTAR_LOCAL.md](COMO_EXECUTAR_LOCAL.md).
