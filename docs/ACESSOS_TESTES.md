# Acessos e Testes — Sistema de Chamados

Credenciais, URLs e fluxos para validar o sistema após instalação.

---

## URLs por ambiente

| Recurso | Local (`artisan serve`) | Docker |
| --- | --- | --- |
| Home / Abrir chamado | http://127.0.0.1:8000/chamados/novo | http://localhost:8080/chamados/novo |
| Consultar chamado | http://127.0.0.1:8000/chamados/consultar | http://localhost:8080/chamados/consultar |
| Painel admin | http://127.0.0.1:8000/admin | http://localhost:8080/admin |
| PHPMyAdmin | — | http://localhost:8085 |

---

## Logins demo (criados pelo Seeder)

### Administrador

```txt
Painel Administrativo
URL de login: /admin
E-mail: admin@admin.com
Senha: password
```

Acesso total: todos os chamados, técnicos, setores e avaliações.

---

### Técnicos por setor

Senha padrão de **todos** os técnicos: `password`

#### Gerência de TI

| Nome | E-mail |
| --- | --- |
| Carlos Almeida | carlos-almeida@chamados.local |
| Fernanda Souza | fernanda-souza@chamados.local |
| Renato Lima | renato-lima@chamados.local |

#### Desenvolvimento

| Nome | E-mail |
| --- | --- |
| Lucas Martins | lucas-martins@chamados.local |
| Ana Beatriz | ana-beatriz@chamados.local |
| Rafael Oliveira | rafael-oliveira@chamados.local |

#### Telefonia/CFTV

| Nome | E-mail |
| --- | --- |
| Jorge Santos | jorge-santos@chamados.local |
| Camila Rocha | camila-rocha@chamados.local |
| Diego Pereira | diego-pereira@chamados.local |

#### Suporte Técnico/Infra

| Nome | E-mail |
| --- | --- |
| Marcos Silva | marcos-silva@chamados.local |
| Patrícia Gomes | patricia-gomes@chamados.local |
| Bruno Henrique | bruno-henrique@chamados.local |

---

## Chamados de demonstração (ChamadoSeeder)

Após `php artisan migrate --seed`:

| Protocolo | Status | Setor | Solicitante |
| --- | --- | --- | --- |
| CHM-{ano}-000001 | Em Aberto | Desenvolvimento | Fabiana Costa |
| CHM-{ano}-000002 | Em Andamento | Suporte Técnico/Infra | Ricardo Mendes |
| CHM-{ano}-000003 | Aguardando Cliente | Desenvolvimento | Ana Paula |

Use esses protocolos para testar a **Consultar Chamado**.

---

## Fluxos de teste

### 1. Abertura pública de chamado

1. Acesse `/chamados/novo`
2. Preencha todos os campos
3. Envie o formulário
4. Verifique protocolo na tela de sucesso (ex: `CHM-2026-000004`)
5. Confirme e-mail enfileirado (worker deve estar rodando)

### 2. Consulta de chamado

1. Acesse `/chamados/consultar`
2. Informe `CHM-{ano}-000002`
3. Veja situação atual e histórico público

### 3. Painel do técnico

1. Login em `/admin` com `marcos-silva@chamados.local` / `password`
2. Veja apenas chamados do setor **Suporte Técnico/Infra**
3. Abra um chamado → **Assumir** ou **Adicionar Histórico**
4. Verifique timeline e mudança de status

### 4. Painel do administrador

1. Login com `admin@admin.com` / `password`
2. Veja todos os chamados de todos os setores
3. Acesse dashboard com cards de resumo
4. Gerencie técnicos e setores

### 5. Finalização e avaliação

1. Como técnico, finalize um chamado
2. Verifique e-mail de avaliação enfileirado
3. Acesse o link `/chamados/{protocolo}/avaliar/{token}`
4. Envie notas de 1 a 5
5. Confirme mensagem de agradecimento

### 6. Permissões

| Ação | Admin | Técnico (outro setor) |
| --- | --- | --- |
| Ver chamado de outro setor | Sim | Não |
| Gerenciar setores | Sim | Não |
| Gerenciar técnicos | Sim | Não |
| Finalizar chamado do setor | Sim | Sim |
| Excluir chamado | Sim | Não |

---

## Banco de dados (Docker)

| Campo | Valor |
| --- | --- |
| PHPMyAdmin | http://localhost:8085 |
| Host (externo) | 127.0.0.1:3308 |
| Banco | sistema_chamados |
| Usuário | root_docker |
| Senha | password |

---

## Comandos de verificação rápida

```bash
# Testes PHP (157 testes, cobertura mín. 90% no CI)
php artisan test
composer quality          # Pint + PHPStan + testes (+ cobertura se PCOV instalado)

# Testes frontend (Vitest, cobertura mín. 90% do JS público)
npm run test:frontend
npm run test:frontend:coverage

# Testes E2E no browser (2 projetos Playwright: public + admin)
# Requer servidor local já rodando (php artisan serve) com banco seedado
npm run test:e2e
npm run test:e2e:public   # área pública
npm run test:e2e:admin     # painel Filament

# Tudo JS (frontend + browser)
npm run test:js:all

# Playwright UI mode (debug)
npm run test:e2e:ui
```

### Variáveis úteis (Playwright)

| Variável | Padrão | Uso |
| --- | --- | --- |
| `PLAYWRIGHT_BASE_URL` | `http://127.0.0.1:8000` | URL do app local (Laragon, artisan serve, Docker) |
| `PLAYWRIGHT_ADMIN_EMAIL` | `admin@admin.com` | Login admin nos testes |
| `PLAYWRIGHT_ADMIN_PASSWORD` | `password` | Senha admin nos testes |

No CI, o Playwright sobe automaticamente um servidor de teste com SQLite (`scripts/e2e-server.sh`).
