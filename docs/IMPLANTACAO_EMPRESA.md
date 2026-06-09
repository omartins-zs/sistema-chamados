# Implantação em Empresa — Sistema de Chamados

Este guia explica como colocar o sistema em produção no ambiente da sua empresa.

## O sistema é funcional para empresa?

**Sim.** O sistema está pronto para uso corporativo interno com:

- Abertura pública de chamados (celular, tablet e desktop)
- Painel administrativo para técnicos e gestores
- Histórico, status, avaliação e e-mails automatizados
- Permissões por setor (admin vs técnico)
- 60 testes automatizados

## O que configurar antes de ir para produção

### 1. Servidor

Requisitos mínimos:

| Item | Recomendação |
|------|--------------|
| PHP | 8.3 ou superior |
| MySQL | 8.0+ |
| Node.js | 18+ (apenas para build dos assets) |
| Servidor web | Nginx ou Apache |
| SSL | HTTPS obrigatório em produção |

### 2. Arquivo `.env` de produção

```env
APP_NAME="Chamados - Nome da Empresa"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://chamados.suaempresa.com.br

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=sistema_chamados
DB_USERNAME=seu_usuario
DB_PASSWORD=senha_forte

QUEUE_CONNECTION=database
MAIL_MAILER=smtp
MAIL_HOST=smtp.suaempresa.com.br
MAIL_PORT=587
MAIL_USERNAME=noreply@suaempresa.com.br
MAIL_PASSWORD=senha_smtp
MAIL_FROM_ADDRESS=noreply@suaempresa.com.br
MAIL_FROM_NAME="Chamados - Sua Empresa"
```

### 3. Comandos de deploy

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
npm ci && npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Fila de e-mails (obrigatório)

Os e-mails de confirmação e avaliação usam fila. Em produção, configure um worker permanente:

```bash
php artisan queue:work --tries=3 --timeout=90
```

No Linux, use **Supervisor** para manter o worker sempre ativo.

### 5. Personalização da empresa

| O que personalizar | Onde |
|--------------------|------|
| Nome da empresa | `.env` → `APP_NAME` |
| Setores | Painel Admin → Setores |
| Técnicos | Painel Admin → Técnicos |
| Cores (opcional) | `resources/css/app.css` e Filament |
| Logo (opcional) | `AdminPanelProvider.php` → `->brandLogo()` |

### 6. Acessos após implantação

| Público | URL |
|---------|-----|
| Abrir chamado | `https://chamados.empresa.com/chamados/novo` |
| Consultar | `https://chamados.empresa.com/chamados/consultar` |
| Painel admin | `https://chamados.empresa.com/admin` |

**Primeiro acesso admin:** `admin@admin.com` / `password` — **troque a senha imediatamente.**

## Fluxo de uso na empresa

```
Colaborador                    TI / Suporte
     │                              │
     ├─ Abre chamado (celular/PC)   │
     ├─ Recebe protocolo + e-mail   │
     │                              ├─ Login no painel /admin
     │                              ├─ Vê chamados do setor
     │                              ├─ Assume e adiciona histórico
     │                              ├─ Atualiza status
     │                              └─ Finaliza chamado
     ├─ Recebe e-mail de avaliação  │
     └─ Avalia o atendimento        │
                                    └─ Gestor vê métricas no dashboard
```

## Responsividade

O sistema foi desenvolvido com **Tailwind CSS + Flowbite** e breakpoints para:

- **Celular** (< 640px): menu hambúrguer, botões largura total, formulários empilhados
- **Tablet** (640px–1024px): grid 2 colunas nos formulários
- **Desktop** (> 1024px): layout completo com navegação horizontal

O painel **Filament** já é responsivo nativamente.

## Checklist antes de liberar para a empresa

- [ ] `APP_DEBUG=false`
- [ ] HTTPS configurado
- [ ] SMTP real configurado e testado
- [ ] Worker de fila rodando (`queue:work`)
- [ ] Senha do admin alterada
- [ ] Setores e técnicos reais cadastrados
- [ ] `npm run build` executado (assets compilados)
- [ ] Backup do banco agendado
- [ ] Testar abertura de chamado pelo celular
- [ ] Testar e-mail de confirmação e avaliação

## Suporte e manutenção

```bash
# Atualizar código
git pull
composer install --no-dev
php artisan migrate --force
npm run build
php artisan optimize

# Verificar qualidade
composer quality
```

---

*Sistema desenvolvido em Laravel 13 + FilamentPHP 5 + Tailwind CSS 4.*
