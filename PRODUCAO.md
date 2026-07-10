# Produção — Business Diversity

Guia curto para publicar este projecto Laravel em alojamento compartilhado/cPanel.

## Estado actual do projecto

- `.env` está preparado para produção.
- `.env.dev` guarda a configuração local/desenvolvimento.
- Assets já foram compilados em `public/build`.
- Composer foi optimizado em modo produção com `--no-dev`.
- Configuração, rotas, views e eventos foram cacheados.
- As rotas já são compatíveis com `route:cache`.
- O sistema de propostas inclui QR Code de verificação digital.

## Requisitos do servidor

- PHP 8.3+
- MySQL/MariaDB
- Extensões PHP: `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `fileinfo`
- Composer 2, se for instalar dependências no servidor
- Node.js só é necessário se o build for feito no servidor. Se enviar `public/build`, não precisa.

## Regra principal de segurança

O domínio deve apontar para:

```text
/caminho/do/projecto/public
```

Não aponte o domínio para a raiz do projecto. A raiz contém `.env`, `app/`, `vendor/`, `storage/` e outros ficheiros internos.

## Ficheiro `.env`

O `.env` deste projecto já está preparado para produção. Antes de publicar, confirme apenas:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://bdiversity.co.mz
FORCE_HTTPS=true

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=mail.bdiversity.co.mz
MAIL_PORT=465
MAIL_EHLO_DOMAIN=bdiversity.co.mz
MAIL_FROM_ADDRESS=no_reply@bdiversity.co.mz
MAIL_REPLY_TO_ADDRESS=info@bdiversity.co.mz
MAIL_CONTACT_TO=info@bdiversity.co.mz
```

Não publique passwords em repositórios, screenshots ou documentação.

## Upload para cPanel sem SSH

Envie estes itens:

- `app/`
- `bootstrap/`
- `config/`
- `database/`
- `lang/`
- `public/`
- `resources/`
- `routes/`
- `storage/`
- `vendor/`
- `.env`
- `artisan`
- `composer.json`
- `composer.lock`

Não envie:

- `node_modules/`
- `.git/`
- `.agents/`
- `.codex/`
- `.env.dev`
- ficheiros antigos `.html`, se o domínio já aponta correctamente para `public/`

## Upload para cPanel com SSH/Terminal

Depois de enviar o projecto:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

Se alterar CSS/JS no servidor:

```bash
npm ci
npm run build
```

## Base de dados

No cPanel, confirme que:

- a base MySQL existe;
- o utilizador MySQL existe;
- o utilizador foi associado à base com permissões;
- os nomes no `.env` são exactamente os nomes mostrados no cPanel.

Depois execute:

```bash
php artisan migrate --force
```

## Permissões

Estas pastas precisam de escrita pelo PHP:

```text
storage/
bootstrap/cache/
```

## Verificações depois de publicar

Teste:

- `https://bdiversity.co.mz/up`
- página inicial;
- área do colaborador;
- restauro de palavra-passe;
- criação de anúncio;
- criação de evento;
- agenda e marcação de reunião;
- envio de email;
- geração de proposta;
- QR Code de verificação da proposta.

## QR Code das propostas

O QR da proposta aponta para:

```text
https://bdiversity.co.mz/propostas/verificar/{token}
```

Essa página confirma se a proposta está certificada, expirada, revogada, sem efeito ou inválida.

## Depois de alterações futuras

Sempre que alterar rotas, views, config, controllers ou assets:

```bash
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```
