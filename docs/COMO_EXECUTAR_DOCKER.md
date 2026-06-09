# Como Executar com Docker — Sistema de Chamados

Guia para rodar o projeto com **Docker Desktop** no Windows, Linux ou macOS.

---

## Stack e Containers

O ambiente Docker foi preparado para navegação local estável no Windows. A stack inclui:

| Container | Imagem / Build | Porta (host) | Função |
| --- | --- | --- | --- |
| `chamados-nginx` | nginx:1.27-alpine | **8080** → 80 | Servidor web (proxy reverso) |
| `chamados-app` | `docker/php/Dockerfile` | — (interno) | PHP 8.3-FPM + Composer + Node |
| `chamados-mysql` | mysql:8.0 | **3308** → 3306 | Banco de dados MySQL |
| `chamados-phpmyadmin` | phpmyadmin:5 | **8085** → 80 | Interface web do MySQL |
| `chamados-redis` | redis:7-alpine | 6379 → 6379 | Cache/filas (opcional) |
| `chamados-worker` | `docker/php/Dockerfile` | — | Worker de fila (`queue:work`) |
| `chamados-scheduler` | `docker/php/Dockerfile` | — | Agendador Laravel (`schedule:work`) |

**Volumes:**

- Código-fonte montado em `/var/www/html` (hot-reload)
- `mysql_data` — persistência do banco MySQL

**Arquivos Docker:**

- `docker-compose.yml` — orquestração dos containers
- `docker/php/Dockerfile` — imagem PHP-FPM
- `docker/php/entrypoint.sh` — permissões de `storage/`
- `docker/nginx/default.conf` — configuração Nginx para Laravel

---

## Requisitos

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado e em execução
- Git (opcional)
- Portas livres: **8080**, **3308**, **8085**, **6379**

---

## 1) Preparar ambiente

### 1.1 Copiar variáveis de ambiente Docker

```bash
cp .env.docker.example .env
```

### 1.2 Configurar `.env` para Docker

Ative o bloco **DOCKER** e comente o bloco **LOCAL**:

```env
APP_NAME="Sistema de Chamados"
APP_ENV=local
APP_DEBUG=false
APP_URL=http://localhost:8080

APP_LOCALE=pt_BR
APP_FALLBACK_LOCALE=pt_BR
APP_FAKER_LOCALE=pt_BR

# DOCKER
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=sistema_chamados
DB_USERNAME=root_docker
DB_PASSWORD=password

# LOCAL
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=sistema_chamados
# DB_USERNAME=root
# DB_PASSWORD=

SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=database

REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PORT=6379

MAIL_MAILER=log
```

> **Importante:** Dentro dos containers, `DB_HOST=mysql` e `DB_PORT=3306` (porta interna). A porta **3308** é apenas para acessar o MySQL **do seu computador** (HeidiSQL, DBeaver, etc.).

---

## 2) Subir containers

```bash
docker compose up -d --build
```

Verificar status:

```bash
docker compose ps
```

Todos os containers devem estar com status `running` (mysql: `healthy`).

---

## 3) Inicialização e migrations

Execute na ordem:

```bash
# Dependências PHP (volume nomeado vendor_data — primeira vez pode demorar)
docker compose exec app composer install

# Chave da aplicação
docker compose exec app php artisan key:generate

# Banco de dados + seeders
docker compose exec app php artisan migrate --seed

# Dependências e build do frontend (volume nomeado node_modules_data)
docker compose exec app npm install
docker compose exec app npm run build

# Caches são gerados automaticamente pelo start-app.sh no boot do container
# Para forçar reconstrução manual:
docker compose exec app sh docker/scripts/rebuild-cache.sh
```

### Recriar banco do zero (opcional)

```bash
docker compose exec app php artisan migrate:fresh --seed
```

---

## 4) Desenvolvimento e cache

```bash
# Limpar todos os caches
docker compose exec app php artisan optimize:clear

# Reconstruir caches de performance (após mudar .env ou rotas)
docker compose exec app sh docker/scripts/rebuild-cache.sh

# Reiniciar app após alterar arquivos PHP (opcache sem revalidação)
docker compose restart app

# Rodar testes
docker compose exec app php artisan test

# Qualidade de código
docker compose exec app composer quality

# Acessar shell do container
docker compose exec app bash
```

> **Performance:** O ambiente Docker está otimizado (OPcache, Redis, PHP-FPM, Nginx dinâmico). Detalhes em [PERFORMANCE_DOCKER.md](PERFORMANCE_DOCKER.md).

### Frontend em modo dev (opcional)

```bash
docker compose exec app npm run dev
```

---

## 5) Acessos

| Recurso | URL |
| --- | --- |
| Aplicação (área pública) | http://localhost:8080 |
| Abrir chamado | http://localhost:8080/chamados/novo |
| Consultar chamado | http://localhost:8080/chamados/consultar |
| Painel administrativo | http://localhost:8080/admin |
| PHPMyAdmin | http://localhost:8085 |
| MySQL (host externo) | `127.0.0.1:3308` |

### Credenciais PHPMyAdmin / MySQL

| Campo | Valor |
| --- | --- |
| Servidor | `mysql` (dentro do Docker) ou `127.0.0.1:3308` (do host) |
| Banco | `sistema_chamados` |
| Usuário | `root_docker` |
| Senha | `password` |
| Root (admin) | `root` / `password` |

### Credenciais da aplicação

```txt
Painel Administrativo
URL de login: http://localhost:8080/admin
E-mail: admin@admin.com
Senha: password
```

```txt
Técnico — Desenvolvimento
URL de login: http://localhost:8080/admin
E-mail: lucas-martins@chamados.local
Senha: password
```

```txt
Técnico — Suporte Técnico/Infra
URL de login: http://localhost:8080/admin
E-mail: marcos-silva@chamados.local
Senha: password
```

Lista completa de logins: [ACESSOS_TESTES.md](ACESSOS_TESTES.md)

---

## 6) Logs e diagnóstico

```bash
# Logs de todos os containers
docker compose logs -f

# Logs apenas da aplicação PHP
docker compose logs -f app

# Logs do worker de fila
docker compose logs -f worker

# Logs do Nginx
docker compose logs -f nginx

# Informações do Laravel
docker compose exec app php artisan about

# Testar conexão com MySQL
docker compose exec app php artisan db:show
```

---

## 7) Parar e remover

```bash
# Parar containers (mantém dados)
docker compose down

# Parar e remover volumes (apaga banco!)
docker compose down -v
```

---

## 8) Problemas comuns

| Problema | Solução |
| --- | --- |
| Porta 8080 em uso | Altere em `docker-compose.yml`: `"8081:80"` e `APP_URL=http://localhost:8081` |
| MySQL não sobe | Aguarde o healthcheck; rode `docker compose logs mysql` |
| Erro de permissão em `storage/` | `docker compose exec app chmod -R 775 storage bootstrap/cache` |
| Página sem CSS | `docker compose exec app npm run build` |
| Worker não processa fila | Verifique `docker compose logs worker` e se o container está `running` |
| Mudanças no `.env` não aplicam | `docker compose exec app php artisan optimize:clear` |

---

## Voltar para ambiente local

```bash
docker compose down
cp .env.example .env
# Configure DB_HOST=127.0.0.1 e siga COMO_EXECUTAR_LOCAL.md
```

Consulte também: [COMO_EXECUTAR.md](COMO_EXECUTAR.md)
