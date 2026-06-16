# Como Executar — Sistema de Chamados Técnicos

Escolha **um** guia conforme seu ambiente:

| Guia | Quando usar |
| --- | --- |
| **[COMO_EXECUTAR_LOCAL.md](COMO_EXECUTAR_LOCAL.md)** | Laragon, XAMPP ou `php artisan serve` (MySQL local) |
| **[COMO_EXECUTAR_DOCKER.md](COMO_EXECUTAR_DOCKER.md)** | Docker Desktop (MySQL + Redis + PHPMyAdmin) |
| [ACESSOS_TESTES.md](ACESSOS_TESTES.md) | Logins demo, URLs e fluxos de teste |

---

## Início rápido

### Local (Laragon)

```bash
cp .env.example .env
composer install && npm install
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Em outro terminal:

```bash
php artisan queue:work
```

→ http://127.0.0.1:8000

### Docker

```bash
cp .env.example .env
docker compose up -d --build
docker compose logs -f app
```

→ http://localhost:8080 (bootstrap automático na primeira subida)

---

## Logins demo

| Perfil | E-mail | Senha |
| --- | --- | --- |
| Administrador | admin@admin.com | password |
| Técnico — Desenvolvimento | lucas-martins@chamados.local | password |
| Técnico — Suporte Técnico/Infra | marcos-silva@chamados.local | password |
| Técnico — Gerência de TI | carlos-almeida@chamados.local | password |
| Técnico — Telefonia/CFTV | jorge-santos@chamados.local | password |

> Todos os técnicos usam a senha padrão `password`. Veja a lista completa em [ACESSOS_TESTES.md](ACESSOS_TESTES.md).

---

## URLs principais

| Área | Local (porta 8000) | Docker (porta 8080) |
| --- | --- | --- |
| Abrir chamado | http://127.0.0.1:8000/chamados/novo | http://localhost:8080/chamados/novo |
| Consultar chamado | http://127.0.0.1:8000/chamados/consultar | http://localhost:8080/chamados/consultar |
| Painel admin | http://127.0.0.1:8000/admin | http://localhost:8080/admin |
| PHPMyAdmin | — | http://localhost:8085 |

---

## Outros documentos

- [PLANO_IMPLEMENTACAO_CHECKLIST.md](PLANO_IMPLEMENTACAO_CHECKLIST.md) — Checklist do projeto
- [IMPLANTACAO_EMPRESA.md](IMPLANTACAO_EMPRESA.md) — Guia para produção corporativa
- [ACESSOS_TESTES.md](ACESSOS_TESTES.md) — Credenciais e fluxos de teste
