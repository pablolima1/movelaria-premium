# Movelaria Premium

Starter project based on Laravel 12, Fortify, Tailwind CSS, Alpine.js, and the TailAdmin layout.

## Requirements

- PHP 8.2+
- Composer
- Node.js and npm

## Setup

```bash
composer install
npm install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

In another terminal, run `npm run dev` while developing the frontend.

Open the application at `http://localhost:8000`. Fortify provides the login at `/login` and registration at `/register`. After signing in, `/home` displays the starter example inside the TailAdmin application layout.

## Project structure

- `app/Actions/Fortify`: Fortify authentication actions.
- `app/Http/Controllers`: application controllers.
- `app/Models`: Eloquent models.
- `resources/views/layouts`: TailAdmin application and full-screen layouts.
- `resources/views/components`: reusable TailAdmin Blade components.
- `resources/views/pages`: authentication and application pages.
- `routes/web.php`: application web routes.
- `database/migrations`: framework and user-account schema.

To use a different database, update the `DB_*` values in `.env` and run `php artisan migrate`.
