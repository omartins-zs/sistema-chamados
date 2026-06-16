# Como Executar com Docker — Sistema de Chamados

Roda em **qualquer máquina** com [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows, macOS ou Linux).

Os containers são **Linux** — o sistema operacional do seu PC não importa. Você **não precisa** instalar PHP, Composer, Node, MySQL ou Laragon no computador.

---

## Requisito

| Ferramenta | Obrigatório? |
| --- | --- |
| **Docker Desktop** | Sim |
| PHP / Composer / Node / Laragon | **Não** |

---

## Início rápido

```bash
git clone <url-do-repositorio>
cd sistema-chamados
cp .env.example .env
docker compose up -d --build
```

O `.env` é opcional no Docker — o compose já define banco, Redis e e-mail. Se não existir `.env`, o sistema sobe mesmo assim.

Na **primeira vez**, o `docker build` instala dependências (cacheado depois). Na subida, só rodam migrate + seed (segundos).

| Recurso | URL |
| --- | --- |
| Aplicação | http://localhost:8080 |
| Painel admin | http://localhost:8080/admin |
| PHPMyAdmin | http://localhost:8085 |
| Mailpit (e-mails) | http://localhost:8025 |

Login admin: `admin@admin.com` / `password`

---

## Containers

| Container | Porta | Função |
| --- | --- | --- |
| `chamados-nginx` | **8080** | Servidor web |
| `chamados-app` | — | PHP 8.3-FPM (Linux) |
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

docker compose exec app php artisan migrate:fresh --seed
docker compose up -d --build
docker compose down
docker compose down -v
```

---

## Problemas comuns

| Problema | Solução |
| --- | --- |
| Porta 8080 em uso | Altere `"8080:80"` no serviço `nginx` |
| Página 502 | `docker compose logs app` — aguarde o MySQL |
| Banco com dados antigos | `docker compose down -v` e suba de novo |
| Mudou código | `docker compose up -d --build` |
| Erro no build (`public/storage`) | `docker builder prune -f` e build de novo — o `.dockerignore` já ignora links do host |

---

## Desenvolvimento com PHP/Node no PC

Se quiser editar código com hot-reload, Laragon, Composer e Node, use o guia **[COMO_EXECUTAR_LOCAL.md](COMO_EXECUTAR_LOCAL.md)** — lá sim há requisitos de versão (PHP 8.3+, Composer, etc.).

```bash
docker compose down
```
