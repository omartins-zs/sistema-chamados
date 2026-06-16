# Como Executar com Docker — Sistema de Chamados

Guia para rodar o projeto com **Docker Desktop** no Windows, Linux ou macOS.

---

## Início rápido

```bash
cp .env.example .env
docker compose up -d --build
```

O **build da imagem** instala Composer + NPM (só na 1ª vez ou quando o código mudar). Na subida, o container só roda **migrate + seed** — leva segundos.

| Recurso | URL |
| --- | --- |
| Aplicação | http://localhost:8080 |
| Painel admin | http://localhost:8080/admin |
| PHPMyAdmin | http://localhost:8085 |
| Mailpit (e-mails) | http://localhost:8025 |

Login admin: `admin@admin.com` / `password`

> **Um único `.env`** serve para Laragon e Docker. O `docker-compose.yml` sobrescreve `DB_HOST`, `REDIS_*`, `MAIL_*` etc. — **não precisa editar o `.env` para Docker**.

---

## Containers

| Container | Porta | Função |
| --- | --- | --- |
| `chamados-nginx` | **8080** | Servidor web |
| `chamados-app` | — | PHP 8.3-FPM |
| `chamados-mysql` | **3308** | MySQL 8 |
| `chamados-redis` | — | Cache, sessão e filas |
| `chamados-mailpit` | **8025** | E-mails de teste |
| `chamados-worker` | — | Fila de e-mails |
| `chamados-phpmyadmin` | **8085** | Interface MySQL |

---

## Credenciais MySQL

| Campo | Valor |
| --- | --- |
| Host (do PC) | `127.0.0.1:3308` |
| Banco | `sistema_chamados` |
| Usuário | `chamados` / `chamados` |
| Root | `root` / `root` |

---

## Comandos úteis

```bash
docker compose ps
docker compose logs -f app

# Recriar banco
docker compose exec app php artisan migrate:fresh --seed

# Rebuild após mudar código PHP/JS
docker compose up -d --build

# Parar
docker compose down

# Parar e apagar banco
docker compose down -v
```

---

## Problemas comuns

| Problema | Solução |
| --- | --- |
| Porta 8080 em uso | Altere `"8080:80"` no serviço `nginx` |
| Página 502 | `docker compose logs app` — aguarde MySQL |
| Sem `.env` | `copy .env.example .env` |
| Banco com credenciais antigas | `docker compose down -v` e suba de novo |
| Mudou código e não refletiu | `docker compose up -d --build` |

---

## Voltar para Laragon

```bash
docker compose down
```

Siga [COMO_EXECUTAR_LOCAL.md](COMO_EXECUTAR_LOCAL.md).
