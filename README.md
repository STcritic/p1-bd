# Business Diversity Website

Website institucional da Business Diversity CE, SA, reconstruído em Laravel.

## Requisitos

- PHP 8.3+
- Composer 2
- Node.js 22+
- MySQL/MariaDB em produção
- SQLite ou MySQL em ambiente local

## Ambiente local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

Para Laragon, mantenha o `.env` local com `APP_ENV=local`, `APP_DEBUG=true` e a base de dados local.

Configure `MAIL_*` e `MAIL_CONTACT_TO` no `.env` para o envio das mensagens.

O botão **Área do Colaborador** abre o Portal BD, onde são geridos anúncios, eventos e agenda. O atalho **Interno BD** continua a apontar para a intranet existente.

## Produção

Veja [PRODUCAO.md](PRODUCAO.md). O domínio deve apontar para a pasta `public/`, nunca para a raiz do projecto.

## Verificação

```bash
php artisan test
php vendor/bin/pint --test
npm run build
```
