# Business Diversity Website

Website institucional da Business Diversity CE, SA, reconstruído em Laravel 13.

## Requisitos

- PHP 8.3+
- Composer 2
- Node.js 22+
- SQLite ou MySQL

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

Configure `MAIL_*` e `MAIL_CONTACT_TO` no `.env` para o envio das mensagens. Todas as mensagens também ficam guardadas na tabela `contact_messages`.

O botão **Área do Colaborador** aponta para `https://bdiversity.co.mz/intranet`; a autenticação continua a ser responsabilidade da intranet existente.

## Verificação

```bash
php artisan test
php vendor/bin/pint --test
npm run build
```
