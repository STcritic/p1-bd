# Produção — Business Diversity

Guia curto para publicar o website Laravel em alojamento/cPanel.

## Requisitos

- PHP 8.3+
- Extensões PHP comuns do Laravel: `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `curl`, `fileinfo`
- MySQL/MariaDB
- Composer 2
- Node.js apenas se o build for feito no servidor. Se não houver Node no servidor, faça `npm run build` localmente e envie a pasta `public/build`.

## Regra principal de segurança

O domínio deve apontar para:

```text
/caminho/do/projecto/public
```

Não aponte o domínio para a raiz do projecto. A raiz contém `.env`, `app/`, `vendor/`, `storage/` e outros ficheiros internos.

## Variáveis do `.env`

No servidor:

```bash
cp .env.example .env
php artisan key:generate
```

Depois confirme no `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://bdiversity.co.mz
APP_TIMEZONE=Africa/Maputo
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
MAIL_USERNAME=no_reply@bdiversity.co.mz
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=no_reply@bdiversity.co.mz
MAIL_FROM_NAME="Business Diversity"
MAIL_CONTACT_TO=info@bdiversity.co.mz

ANNOUNCEMENT_MASTER_EMAIL=info@bdiversity.co.mz
ANNOUNCEMENT_MASTER_PASSWORD=...
ANNOUNCEMENT_PASSWORD_EXPIRES_MONTHS=6
```

Não publique passwords em repositórios, screenshots ou documentação.

## Instalação

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

Se o servidor tiver Node:

```bash
npm ci
npm run build
```

Se não tiver Node, faça localmente:

```bash
npm ci
npm run build
```

Depois envie também `public/build`.

## Permissões

As pastas abaixo precisam de escrita pelo PHP:

```text
storage/
bootstrap/cache/
```

## Verificações depois de publicar

- `https://bdiversity.co.mz/up`
- Página inicial
- Área do Colaborador
- Restauro de palavra-passe
- Criação de anúncio
- Criação de evento
- Agenda e horários disponíveis
- Envio de email

## Depois de alterações futuras

Sempre que alterar rotas, views, config ou assets:

```bash
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
```
