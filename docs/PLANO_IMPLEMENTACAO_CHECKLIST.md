# 📋 Plano de Implementação e Checklist — Sistema de Chamados Técnicos

Este documento centraliza o acompanhamento das funcionalidades do sistema, separadas por fases de desenvolvimento.

**Legenda:**
- [x] Concluído
- [/] Em andamento / Parcial
- [ ] Pendente

**Última revisão:** 08/06/2026  
**Stack:** Laravel 13 · PHP 8.3+ · MySQL · FilamentPHP 5 · Tailwind CSS 4 · Flowbite

---

## 1. 🏗️ Estrutura Base e Ambiente

- [x] Configuração inicial do repositório (Laravel 13)
- [x] Instalação do FilamentPHP (`filament/filament` v5.6)
- [x] Instalação do Tailwind CSS 4 + Vite
- [x] Instalação do Flowbite
- [x] Variáveis de ambiente configuradas (`.env` / `.env.example`)
- [x] Configuração de locale pt_BR no `.env.example`
- [x] Configuração de fila via banco (`QUEUE_CONNECTION=database`)
- [x] Scripts Composer (`pint`, `stan`, `test`, `test:coverage`, `quality`)
- [x] Script de desenvolvimento (`composer dev` — serve + queue + logs + vite)
- [x] README.md com instruções de instalação e logins
- [x] Ambiente Docker (Nginx, PHP, MySQL, Redis, PHPMyAdmin) configurado
- [x] Configuração de CI/CD (GitHub Actions — `.github/workflows/quality.yml`)
- [x] Configuração de Redis para cache e filas em produção (Docker: `CACHE_STORE`, `SESSION_DRIVER` e `QUEUE_CONNECTION=redis`)

---

## 2. 🗄️ Banco de Dados e Modelagem

### Migrations

| Tabela | Arquivo | Status |
|--------|---------|--------|
| `setores` | `0001_01_01_000000_create_setores_table.php` | [x] |
| `usuarios` | `0001_01_01_000001_create_usuarios_table.php` | [x] |
| `cache` | `0001_01_01_000001_create_cache_table.php` | [x] |
| `jobs` | `0001_01_01_000002_create_jobs_table.php` | [x] |
| `chamados` | `2026_06_08_000002_create_chamados_table.php` | [x] |
| `historicos_chamados` | `2026_06_08_000003_create_historicos_chamados_table.php` | [x] |
| `avaliacoes_chamados` | `2026_06_08_000004_create_avaliacoes_chamados_table.php` | [x] |
| `exports` | `2026_06_08_000005_create_exports_table.php` | [x] |
| `notifications` | `2026_06_08_000006_create_notifications_table.php` | [x] |

### Models e relacionamentos

- [x] `Setor` — hasMany `usuarios`, hasMany `chamados`
- [x] `Usuario` — belongsTo `setor`, hasMany `chamadosResponsaveis`, hasMany `historicos`
- [x] `Chamado` — belongsTo `setor`, belongsTo `tecnicoResponsavel`, hasMany `historicos`, hasOne `avaliacao`
- [x] `HistoricoChamado` — belongsTo `chamado`, belongsTo `tecnico`
- [x] `AvaliacaoChamado` — belongsTo `chamado`

### Enums

- [x] `StatusChamadoEnum` (9 status: em_aberto → cancelado)
- [x] `ComplexidadeChamadoEnum` (baixa, media, alta, critica)
- [x] `TipoUsuarioEnum` (administrador, tecnico)

### Factories

- [x] `SetorFactory`
- [x] `UsuarioFactory` (com states `administrador` e `tecnico`)
- [x] `ChamadoFactory` (com state `finalizado`)
- [x] `HistoricoChamadoFactory`
- [x] `AvaliacaoChamadoFactory`
- [x] Remover `UserFactory.php` legado (model `User` foi substituído por `Usuario`)

### Seeders

- [x] `SetorSeeder` — 4 setores (Gerência de TI, Desenvolvimento, Telefonia/CFTV, Suporte Técnico/Infra)
- [x] `UsuarioSeeder` — 1 admin + 12 técnicos (3 por setor)
- [x] `DatabaseSeeder` — orquestra seeders
- [x] `ChamadoSeeder` — dados de demonstração para ambiente de homologação

---

## 3. 🔐 Autenticação e Segurança

- [x] Login no painel Filament (`/admin`)
- [x] Model `Usuario` com autenticação customizada (campo `senha`)
- [x] Guard `web` + provider `usuarios` configurado em `config/auth.php`
- [x] `FilamentUser` — controle de acesso ao painel (`canAccessPanel`)
- [x] Policies de autorização:
  - [x] `ChamadoPolicy` — admin vê tudo; técnico vê apenas seu setor
  - [x] `SetorPolicy` — apenas admin
  - [x] `UsuarioPolicy` — apenas admin
  - [x] `AvaliacaoChamadoPolicy` — visualização liberada; criação bloqueada no admin
  - [x] `HistoricoChamadoPolicy` — admin vê tudo; técnico vê seu setor
- [x] Recuperação de senha (reset por e-mail) — `->passwordReset()` no painel admin, broker `usuarios`
- [ ] Registro público de usuários (não previsto — apenas admin cria técnicos)
- [ ] Pacote de Roles/Permissions (Spatie Permission) — atualmente via Policies simples
- [ ] Proteção de rotas via API (Sanctum/Passport) — sistema é web-only
- [ ] Autenticação de dois fatores (2FA) no painel admin
- [ ] Rate limiting nas rotas públicas de abertura de chamado

---

## 4. 🧩 Módulos Principais (Regras de Negócio)

### Módulo — Área Pública de Chamados

| Item | Rota | Status |
|------|------|--------|
| Formulário de abertura de chamado | `GET /chamados/novo` | [x] |
| Salvar chamado | `POST /chamados/novo` | [x] |
| Tela de sucesso com protocolo | `GET /chamados/{protocolo}/sucesso` | [x] |
| Consulta de chamado finalizado | `GET /chamados/{protocolo}/finalizado` | [x] |
| Tela de avaliação | `GET /chamados/{protocolo}/avaliar/{token}` | [x] |
| Salvar avaliação | `POST /chamados/{protocolo}/avaliar/{token}` | [x] |
| Consulta pública por protocolo (qualquer status) | `GET/POST /chamados/consultar` | [x] |
| Validação via `CriarChamadoRequest` | — | [x] |
| Validação via `CriarAvaliacaoRequest` | — | [x] |
| Alertas Flowbite/Tailwind (sucesso, erro, validação) | — | [x] |
| Paleta principal `#00468a` aplicada | — | [x] |
| Responsividade mobile/tablet/desktop | — | [x] |

**Controllers:** `ChamadoPublicoController`, `AvaliacaoPublicaController`

---

### Módulo — Chamados (Core)

| Item | Status |
|------|--------|
| Criação de chamado com status `em_aberto` | [x] |
| Geração de protocolo único `CHM-AAAA-NNNNNN` | [x] |
| Vinculação ao setor responsável | [x] |
| Enum de complexidade (baixa → crítica) | [x] |
| Enum de status (9 status) | [x] |
| Atribuição automática de técnico responsável (primeiro histórico) | [x] |
| Alteração de status via histórico | [x] |
| Finalização com `finalizado_em` + token de avaliação | [x] |
| Impedir avaliação duplicada | [x] |
| Validação de token de avaliação com expiração (30 dias) | [x] |
| Validação de setor do técnico (exceto admin) | [x] |
| E-mail de confirmação ao criar chamado | [x] |
| E-mail com link de avaliação ao finalizar | [x] |

**Service:** `ChamadoService`  
**Jobs:** `EnviarEmailChamadoCriadoJob`, `EnviarEmailChamadoFinalizadoJob`  
**Mailables:** `ChamadoCriadoMail`, `ChamadoFinalizadoAvaliacaoMail`

---

### Módulo — Histórico de Chamados

| Item | Status |
|------|--------|
| Tabela `historicos_chamados` com todos os campos | [x] |
| Registro de técnico, status, descrição e data | [x] |
| Flag `visivel_solicitante` (público vs interno) | [x] |
| Assumir chamado automaticamente no primeiro histórico | [x] |
| Action "Assumir Chamado" no Filament | [x] |
| Action "Adicionar Histórico" no Filament | [x] |
| Timeline visual na página de detalhes do chamado | [x] |
| Resource de listagem de históricos no admin | [x] |
| Filtro de históricos por setor (técnico vê só o seu) | [x] |

**Service:** `HistoricoChamadoService`  
**Request:** `AdicionarHistoricoChamadoRequest`

---

### Módulo — Avaliações

| Item | Status |
|------|--------|
| Tabela `avaliacoes_chamados` (nota_satisfacao, nota_tempo_resolucao, comentario) | [x] |
| Avaliação apenas em chamado `finalizado` | [x] |
| Uma avaliação por chamado (unique `chamado_id`) | [x] |
| Tela pública de avaliação com notas 1–5 | [x] |
| Mensagem de agradecimento após avaliação | [x] |
| Resource de visualização de avaliações no admin | [x] |
| Exibição da avaliação na página de detalhes do chamado | [x] |

**Service:** `AvaliacaoChamadoService`

---

### Módulo — Painel Administrativo (Filament)

| Item | Status |
|------|--------|
| Login em `/admin` | [x] |
| Dashboard com cards de resumo | [x] |
| Resource de Chamados (listagem + visualização) | [x] |
| Badges coloridas para status e complexidade | [x] |
| Filtros por status, setor, complexidade e técnico | [x] |
| Busca por protocolo, nome, e-mail e título | [x] |
| Ordenação por data | [x] |
| Actions: Visualizar, Assumir, Adicionar Histórico, Finalizar | [x] |
| Resource de Técnicos (CRUD) | [x] |
| Resource de Setores (CRUD) | [x] |
| Resource de Históricos (somente leitura) | [x] |
| Resource de Avaliações (somente leitura) | [x] |
| Widget `ResumoChamadosOverview` (12 cards) | [x] |
| Notificações Filament em ações principais | [x] |
| Cor primária `#00468a` no painel | [x] |
| Menus em português (Chamados, Históricos, Avaliações, Técnicos, Setores) | [x] |
| Página de Configurações no menu | [x] |
| CRUD completo de Chamados (criar/editar/excluir pelo admin) | [x] — páginas Create/Edit, DeleteAction, modal e botão Novo Chamado |
| Exportação de chamados (CSV/Excel) | [x] — `ChamadoExporter` + `ExportAction` (fila Redis) |
| Relatórios PDF de chamados | [x] — `ChamadoRelatorioPdfService` (individual e lista) |

**Widget cards implementados:**
- [x] Total de Chamados
- [x] Chamados em Aberto
- [x] Chamados Acessados
- [x] Chamados em Andamento
- [x] Chamados Aguardando Cliente
- [x] Chamados Aguardando Terceiros
- [x] Chamados Pausados
- [x] Chamados Finalizados
- [x] Chamados Cancelados
- [x] Média de Satisfação
- [x] Média de Tempo de Resolução

---

### Módulo — Setores e Técnicos

| Item | Status |
|------|--------|
| CRUD de setores (admin) | [x] |
| CRUD de técnicos/usuários (admin) | [x] |
| 4 setores pré-cadastrados via seeder | [x] |
| 12 técnicos pré-cadastrados via seeder (3 por setor) | [x] |
| 1 administrador pré-cadastrado via seeder | [x] |
| Senha padrão `password` para todos os seeders | [x] |
| Filtros por setor e tipo de usuário | [x] |
| Notificação ao criar técnico ou setor | [x] |

---

### Módulo — Notificações

| Evento | Canal | Status |
|--------|-------|--------|
| Chamado criado | E-mail (fila) | [x] |
| Chamado finalizado + link avaliação | E-mail (fila) | [x] |
| Chamado assumido | Filament Notification | [x] |
| Histórico adicionado | Filament Notification | [x] |
| Status alterado | Filament Notification | [x] |
| Chamado finalizado | Filament Notification | [x] |
| E-mail de avaliação enviado | Filament Notification | [x] |
| Técnico criado | Filament Notification | [x] |
| Setor criado | Filament Notification | [x] |
| Avaliação registrada | Filament Notification | [x] |
| Notificação por e-mail ao técnico (novo chamado no setor) | — | [ ] |
| Notificação em tempo real (WebSockets/Pusher) | — | [ ] |

**Service:** `NotificacaoChamadoService`

---

## 5. 🎨 Frontend e Interface

### Área Pública (Blade + Tailwind + Flowbite)

- [x] Layout base (`publico/layouts/app.blade.php`) — header, footer, alertas
- [x] Tela de abertura de chamado (`criar.blade.php`)
- [x] Tela de sucesso (`sucesso.blade.php`)
- [x] Tela de chamado finalizado (`finalizado.blade.php`)
- [x] Tela de avaliação (`avaliar.blade.php`)
- [x] Responsividade para celular, tablet e desktop
- [x] Mensagens de erro de validação claras
- [x] Alertas de sucesso, erro e informação (Flowbite/Tailwind)
- [x] Paleta `#00468a` como cor primária
- [x] Build de assets via Vite (`npm run build`)
- [x] Tela pública de consulta de chamado por protocolo (qualquer status)
- [x] Página inicial institucional (landing page) — rota `/`, hero, benefícios e fluxo
- [x] Remover view `welcome.blade.php` legada do Laravel

### Painel Admin (Filament)

- [x] Interface Filament com tema personalizado
- [x] Badges coloridas para status
- [x] Timeline de histórico na visualização do chamado
- [x] Formulários modais para ações (assumir, histórico, finalizar)
- [x] Gráficos de evolução de chamados (`ChamadosEvolucaoWidget` — linha, últimos 6 meses)
- [x] Dark mode customizado com paleta do projeto (`#00468a` em `theme.css`)

### E-mails (Markdown)

- [x] Template de confirmação de chamado criado
- [x] Template de link de avaliação

---

## 6. ⚙️ Integrações e Tarefas Assíncronas (Background Jobs)

- [x] Migration de filas (`jobs` table)
- [x] `QUEUE_CONNECTION=database` configurado
- [x] Job `EnviarEmailChamadoCriadoJob` (ShouldQueue)
- [x] Job `EnviarEmailChamadoFinalizadoJob` (ShouldQueue)
- [x] Mailables implementam `ShouldQueue`
- [x] Comando `php artisan queue:work` documentado
- [x] Script `composer dev` inclui `queue:listen`
- [x] Configuração SMTP documentada — variáveis `MAIL_*` em `.env.example`
- [x] Mailpit no `docker-compose.yml` para captura de e-mails em desenvolvimento (UI em `:8025`)
- [x] Painel **Configurações** exibe driver SMTP, host/porta, remetente e link do Mailpit
- [ ] Worker de fila configurado como serviço (Supervisor/systemd)
- [ ] SMTP de produção provisionado na empresa (credenciais reais no `.env` de produção)
- [ ] Integração com serviço de SMS/WhatsApp para notificações
- [ ] Integração com Gateway de Pagamento (não aplicável)

---

## 7. 🧪 Testes e Validação

### Configuração

- [x] PHPUnit 12 configurado (`phpunit.xml`)
- [x] Banco SQLite em memória para testes
- [x] Laravel Pint configurado (`composer pint`)
- [x] Larastan/PHPStan configurado (`phpstan.neon`, level 5)
- [x] Scripts `composer quality` (pint + stan + test:coverage)
- [x] Cobertura mínima de 90% — validada no CI (PCOV); localmente requer extensão PCOV/Xdebug

### Testes implementados (166 testes PHPUnit — todos passando)

| Arquivo | Cenários | Status |
|---------|----------|--------|
| `ChamadoPublicoTest` | Criar chamado, protocolo, status, setor, validação, e-mail, sucesso | [x] |
| `HistoricoChamadoTest` | Adicionar histórico, assumir, status, setor, admin | [x] |
| `FinalizacaoChamadoTest` | Finalizar, finished_at, token, e-mail, protocolo sequencial | [x] |
| `AvaliacaoChamadoTest` | Avaliar, token inválido, duplicata, não finalizado, HTTP | [x] |
| `PermissaoTest` | Admin vs técnico, setores, exclusão | [x] |
| `ChamadoServiceTest` | Buscar por protocolo, token único | [x] |
| `MailJobTest` | Jobs de e-mail enfileirados | [x] |
| `EnumsTest` | Rótulos e opções dos enums | [x] |
| `ExampleTest` | Redirecionamento da home | [x] |
| `ConsultarChamadoTest` | Consulta por protocolo, finalizado, erros | [x] |
| `PainelControllerTest` | Redirecionamento para /admin | [x] |
| `ChamadoSeederTest` | Dados de demonstração | [x] |
| `AvaliacaoChamadoServiceTest` | Token expirado, criar, duplicata | [x] |
| `ChamadoModelTest` | estaFinalizado, podeSerAvaliado | [x] |
| `UsuarioModelTest` | ehAdministrador, ehTecnico, senha | [x] |
| `FilamentResourcesTest` | Resources, listagens, ações do painel | [x] |
| `PainelFilamentTest` | Dashboard, login, widgets | [x] |
| `RecuperacaoSenhaTest` | Solicitação de reset por e-mail | [x] |
| `ChamadoCrudFilamentTest` | Criar, editar, excluir, widget evolução | [x] |
| `ChamadoRelatorioPdfServiceTest` | PDF individual e lista | [x] |
| `ChamadoExporterTest` | Colunas de exportação | [x] |
| `NotificacaoChamadoServiceTest` | Notificações Filament | [x] |
| Policies (unitários) | Chamado, Setor, Usuario, etc. | [x] |

### Testes E2E e frontend

- [x] Playwright E2E — projetos `public` e `admin` (`.github/workflows/e2e.yml`)
- [x] Vitest — utilitários em `resources/js/publico/`

### Testes pendentes

- [x] Testes E2E para CRUD admin, exportação e PDF — `e2e/admin/chamado-crud.spec.ts`
- [x] Remover `tests/Unit/ExampleTest.php` placeholder

---

## 8. 🚀 Deploy e Produção

- [ ] Servidor de produção provisionado
- [ ] Configuração de Nginx/Apache como reverse proxy
- [ ] Configuração de SSL/HTTPS
- [ ] `APP_ENV=production` e `APP_DEBUG=false`
- [x] Configuração SMTP documentada (`MAIL_*`, Mailpit no Docker, guia em `IMPLANTACAO_EMPRESA.md`)
- [ ] SMTP de produção provisionado (Mailgun, SES, servidor corporativo, etc.)
- [ ] Worker de fila como serviço persistente (Supervisor)
- [ ] Scheduler do Laravel configurado (`cron`)
- [ ] Rotinas de backup do banco de dados agendadas
- [ ] Monitoramento de logs (Sentry, Bugsnag, etc.)
- [ ] Docker Compose para deploy padronizado
- [ ] Pipeline CI/CD com `composer quality` automatizado

---

## 9. 📊 Resumo de Progresso

| Fase | Concluído | Parcial | Pendente |
|------|-----------|---------|----------|
| 1. Estrutura Base | 10 | 0 | 3 |
| 2. Banco de Dados | 22 | 0 | 2 |
| 3. Autenticação | 6 | 0 | 5 |
| 4. Módulos Principais | 68 | 1 | 8 |
| 5. Frontend | 14 | 0 | 4 |
| 6. Jobs/Filas | 10 | 0 | 3 |
| 7. Testes | 17 | 0 | 6 |
| 8. Deploy | 1 | 0 | 10 |
| **Total** | **~148** | **1** | **~39** |

**Estimativa de conclusão do MVP:** ~87%  
**Próximas prioridades sugeridas:**

1. Provisionar SMTP de produção na empresa e monitorar worker Redis
2. Notificação por e-mail ao técnico (novo chamado no setor)
3. Provisionar servidor de produção com SSL e backups
4. Worker de fila como serviço persistente (Supervisor/systemd)

---

## 10. 📁 Mapa de Arquivos Principais

```
app/
├── Enums/
│   ├── StatusChamadoEnum.php
│   ├── ComplexidadeChamadoEnum.php
│   └── TipoUsuarioEnum.php
├── Models/
│   ├── Setor.php
│   ├── Usuario.php
│   ├── Chamado.php
│   ├── HistoricoChamado.php
│   └── AvaliacaoChamado.php
├── Services/
│   ├── ChamadoService.php
│   ├── HistoricoChamadoService.php
│   ├── AvaliacaoChamadoService.php
│   └── NotificacaoChamadoService.php
├── Http/
│   ├── Controllers/
│   │   ├── ChamadoPublicoController.php
│   │   └── AvaliacaoPublicaController.php
│   └── Requests/
│       ├── CriarChamadoRequest.php
│       ├── CriarAvaliacaoRequest.php
│       ├── AdicionarHistoricoChamadoRequest.php
│       └── AtualizarStatusChamadoRequest.php
├── Mail/
│   ├── ChamadoCriadoMail.php
│   └── ChamadoFinalizadoAvaliacaoMail.php
├── Jobs/
│   ├── EnviarEmailChamadoCriadoJob.php
│   └── EnviarEmailChamadoFinalizadoJob.php
├── Policies/
│   ├── ChamadoPolicy.php
│   ├── SetorPolicy.php
│   ├── UsuarioPolicy.php
│   ├── AvaliacaoChamadoPolicy.php
│   └── HistoricoChamadoPolicy.php
└── Filament/
    ├── Resources/
    │   ├── Chamados/ChamadoResource.php
    │   ├── Setors/SetorResource.php
    │   ├── Usuarios/UsuarioResource.php
    │   ├── HistoricoChamados/HistoricoChamadoResource.php
    │   └── AvaliacaoChamados/AvaliacaoChamadoResource.php
    └── Widgets/
        └── ResumoChamadosOverview.php

resources/views/
├── publico/
│   ├── layouts/app.blade.php
│   └── chamados/
│       ├── criar.blade.php
│       ├── sucesso.blade.php
│       ├── finalizado.blade.php
│       └── avaliar.blade.php
├── filament/chamados/visualizar.blade.php
└── emails/chamados/
    ├── criado.blade.php
    └── finalizado.blade.php

database/
├── migrations/ (7 migrations)
├── seeders/ (SetorSeeder, UsuarioSeeder, DatabaseSeeder)
└── factories/ (5 factories)

tests/
├── Feature/ (6 arquivos, 34 testes)
└── Unit/ (4 arquivos, 8 testes)
```

---

## 11. 🔑 Credenciais de Acesso (Seeder)

| Tipo | Nome | E-mail | Senha |
|------|------|--------|-------|
| Administrador | Administrador | admin@admin.com | password |
| Técnico — Gerência de TI | Carlos Almeida | carlos-almeida@chamados.local | password |
| Técnico — Desenvolvimento | Lucas Martins | lucas-martins@chamados.local | password |
| Técnico — Telefonia/CFTV | Jorge Santos | jorge-santos@chamados.local | password |
| Técnico — Suporte Técnico/Infra | Marcos Silva | marcos-silva@chamados.local | password |

**Painel admin:** `http://localhost:8000/admin`  
**Área pública:** `http://localhost:8000/chamados/novo`

---

*Documento gerado com base na análise do código-fonte em 08/06/2026.*
