# AGENTS.md

## Project Overview

Arabic-language Laravel 8 store/inventory management system. Handles products, customers, providers, expenses, salaries, SMS notifications, PDF/Excel export. Uses MySQL 8, Spatie permissions, TCPDF for PDFs, Maatwebsite Excel for imports/exports.

## Commands

```bash
# Dev server (Docker recommended)
docker-compose up --build        # PHP on :8000 + MySQL on :3306

# Without Docker
php artisan serve                # needs PHP 7.4/8.0 + MySQL running

# Frontend assets (Laravel Mix)
npm install && npm run dev       # development build
npm run prod                     # production build

# Database
php artisan migrate --seed       # runs PermissionSeeder (roles/permissions)

# PDF generation uses TCPDF via `elibyy/tcpdf-laravel` (alias: `PDF::`)
# Excel uses `maatwebsite/excel` (alias: `Excel::`)
```

## Key Conventions

- **Language**: Arabic UI throughout. Controllers, views, PDF output, and database seeders are in Arabic. Do not translate.
- **Auth**: All controllers use `$this->middleware('auth')` in constructor. Auth routes: `Auth::routes(['register' => true, 'reset' => false, 'verify' => false])`.
- **Routes**: All routes defined in `routes/web.php` (no resource controllers). Routes use explicit controller method references like `[Controller::class, 'method']`.
- **Controllers**: All under `App\Http\Controllers\Admin\`. No API routes used for the main app.
- **AJAX pattern**: Most list pages use AJAX for pagination/search. Controllers check `$request->ajax()` and return JSON with rendered Blade partials.
- **Permissions**: Uses `spatie/laravel-permission`. Permissions seeded in `PermissionSeeder.php` (e.g., `add_to_box`, `show_customers`, `edit_products`).
- **PDF output**: Files saved to `storage/app/public/pdf/` with Arabic directory names and filenames. Symlink created on demand.
- **Excel output**: Files saved to `storage/app/public/xlsx/` via Maatwebsite Excel.
- **Pagination**: `config('app.page')` controls per-page count (check `config/app.php`).
- **SMS**: Uses hi5sms.com API via `env('SMS_USERNAME')`, `env('SMS_PASSWORD')`, `env('SMS_SENDER')`.

## Structure

- `app/Http/Controllers/Admin/` - All 21 controllers
- `app/Models/` - 23 Eloquent models
- `app/Exports/` - 16 Maatwebsite export classes
- `app/Imports/` - 10 Maatwebsite import classes
- `resources/views/admin/` - Blade views per domain (21 subdirectories)
- `resources/views/layouts/main.blade.php` - Main layout
- `database/migrations/` - 26 migrations (2014-2026)
- `database/seeders/PermissionSeeder.php` - Permission definitions

## PHP Version

Requires PHP 7.4 or 8.0 (Dockerfile uses php:7.4-fpm). Do not use PHP 8.1+ features.
