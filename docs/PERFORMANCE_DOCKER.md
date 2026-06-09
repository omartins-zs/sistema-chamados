# Auditoria e Otimização de Performance — Docker

Documento gerado após revisão de performance do ambiente Docker local (Windows + Docker Desktop).

---

## 1. Gargalos encontrados (antes)

| # | Gargalo | Impacto |
|---|---------|---------|
| 1 | `APP_DEBUG=true` | Compila views, queries debug, respostas lentas |
| 2 | Sem OPcache configurado | PHP recompila arquivos a cada request |
| 3 | `SESSION_DRIVER=database` + `CACHE_STORE=database` | I/O MySQL em toda navegação |
| 4 | Extensão Redis ausente no Dockerfile | Redis no compose mas não utilizável |
| 5 | Nginx com `fastcgi_pass app:9000` estático | **502** após `docker compose up --force-recreate` |
| 6 | PHP-FPM padrão (`pm.max_children` baixo) | Fila de requests, lentidão entre telas |
| 7 | Sem `realpath_cache` | Excesso de `stat()` no bind mount Windows |
| 8 | Bootstrap sem espera de MySQL | Erros/500 no cold start |
| 9 | `vendor/` e `node_modules/` no bind mount | I/O muito lento no Windows |
| 10 | Sem cache de config/routes | Laravel recompila a cada request |

---

## 2. Alterações aplicadas

### Laravel / Runtime

| Alteração | Arquivo |
|-----------|---------|
| `APP_DEBUG=false` no Docker | `docker-compose.yml`, `.env.docker.example` |
| `config:cache` + `route:cache` no bootstrap inteligente | `docker/scripts/start-app.sh` |
| `view:cache` **omitido** (Filament/Livewire) | `start-app.sh` |
| Timezone `America/Sao_Paulo` | `local.ini`, `.env.docker.example` |

### Sessão e Cache

| Antes | Depois | Motivo |
|-------|--------|--------|
| `SESSION_DRIVER=database` | `redis` | Menos round-trips MySQL |
| `CACHE_STORE=database` | `redis` | Cache em memória, ~10x mais rápido |
| Sem extensão phpredis | `pecl install redis` | Suporte nativo no Dockerfile |

### PHP-FPM

| Parâmetro | Valor | Arquivo |
|-----------|-------|---------|
| `pm` | dynamic | `fpm-performance.conf` |
| `pm.max_children` | 20 | `fpm-performance.conf` |
| `pm.start_servers` | 4 | `fpm-performance.conf` |
| `pm.max_spare_servers` | 8 | `fpm-performance.conf` |
| `pm.max_requests` | 500 | `fpm-performance.conf` |
| `request_terminate_timeout` | 120s | `fpm-performance.conf` |

### PHP ini / OPcache

| Parâmetro | Valor | Arquivo |
|-----------|-------|---------|
| `realpath_cache_size` | 4096K | `local.ini` |
| `realpath_cache_ttl` | 600 | `local.ini` |
| `opcache.memory_consumption` | 256 | `local.ini` |
| `opcache.max_accelerated_files` | 20000 | `local.ini` |
| `opcache.validate_timestamps` | **0** | `local.ini` |

> Com `validate_timestamps=0`, reinicie o container `app` após alterar arquivos PHP.

### Nginx

| Alteração | Motivo |
|-----------|--------|
| `resolver 127.0.0.11` + `fastcgi_pass $php_backend` | Evita 502 após recreate do app |
| Buffers fastcgi aumentados | Menos timeouts em respostas Filament |
| Cache de assets estáticos (7d) | CSS/JS/fonts mais rápidos |
| `try_files $uri =404` em PHP | Segurança + menos overhead |

### Bootstrap (`start-app.sh`)

1. Aguarda MySQL (`php artisan db:show`)
2. Aguarda Redis (se configurado)
3. Gera `config:cache` **somente se ausente**
4. Gera `route:cache` **somente se ausente**
5. Sobe `php-fpm -F`

### Docker Compose

| Alteração | Motivo |
|-----------|--------|
| Volumes `vendor_data` e `node_modules_data` | Menos I/O no bind mount Windows |
| Healthcheck no `app` | Nginx só sobe quando app está pronto |
| MySQL tunado (`innodb-buffer-pool-size=256M`) | Queries mais rápidas |
| Redis com `maxmemory 128mb` + LRU | Uso controlado de RAM |
| `env_file: .env` | Config centralizada |

---

## 3. Arquivos modificados / criados

```
docker/
├── nginx/default.conf          (atualizado)
├── php/
│   ├── Dockerfile              (atualizado)
│   ├── entrypoint.sh           (simplificado)
│   ├── local.ini               (novo)
│   └── fpm-performance.conf    (novo)
└── scripts/
    ├── start-app.sh            (novo)
    └── rebuild-cache.sh        (novo)

docker-compose.yml              (atualizado)
.env.docker.example             (atualizado)
docs/PERFORMANCE_DOCKER.md      (este arquivo)
```

---

## 4. Como aplicar / validar

### Primeira vez (ou após mudanças no Dockerfile)

```bash
cp .env.docker.example .env
docker compose down
docker compose build --no-cache app
docker compose up -d

# Instalar dependências DENTRO do container (volumes nomeados)
docker compose exec app composer install
docker compose exec app npm install && npm run build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

### Validar performance

```bash
# Status dos containers
docker compose ps

# Info Laravel
docker compose exec app php artisan about

# Verificar caches
docker compose exec app ls -la bootstrap/cache/

# OPcache ativo
docker compose exec app php -i | grep opcache.enable

# Redis conectado
docker compose exec app php artisan tinker --execute="Cache::put('teste', 'ok', 60); echo Cache::get('teste');"

# Logs
docker compose logs -f app nginx
```

### Após mudar `.env`, rotas ou config

```bash
docker compose exec app sh /var/www/html/docker/scripts/rebuild-cache.sh
# ou reinicie o container removendo caches:
docker compose exec app php artisan optimize:clear
docker compose restart app
```

### Testar rotas (PowerShell / bash)

```bash
# Cold start (primeira request após restart — esperado ser mais lenta)
curl -o /dev/null -s -w "Home: %{time_total}s\n" http://localhost:8080/chamados/novo

# Login admin
curl -o /dev/null -s -w "Admin: %{time_total}s\n" http://localhost:8080/admin/login
```

---

## 5. Limitações honestas (Docker Desktop Windows)

| Limitação | Explicação |
|-----------|------------|
| Bind mount lento | Mesmo com `:delegated`, Windows → container tem overhead. Volumes nomeados para `vendor/` mitigam isso. |
| Cold start | Primeira request após restart ainda é mais lenta (OPcache + autoload). Requests seguintes devem ser bem mais rápidas. |
| `opcache.validate_timestamps=0` | Após editar PHP, é necessário `docker compose restart app`. |
| Filament/Livewire | `view:cache` não é usado — telas admin compilam views sob demanda. |
| Queue em database | Mantida para compatibilidade; Redis só para sessão/cache. |
| Sem benchmark formal | Tempos variam com RAM/CPU do host; compare antes/depois na mesma máquina. |

### Quando `APP_DEBUG=true` de volta

Para depurar erros, altere temporariamente no `.env`:

```env
APP_DEBUG=true
```

E limpe caches:

```bash
docker compose exec app php artisan optimize:clear
```

---

## 6. Comandos rápidos

| Ação | Comando |
|------|---------|
| Rebuild completo | `docker compose up -d --build` |
| Reiniciar só app | `docker compose restart app` |
| Reconstruir caches | `docker compose exec app sh docker/scripts/rebuild-cache.sh` |
| Ver workers FPM | `docker compose exec app ps aux \| grep php-fpm` |
| Limpar tudo | `docker compose down -v` (apaga banco!) |

---

*Última revisão: junho/2026*
