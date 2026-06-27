<h1 align="center">Store</h1>

<p align="center">
  <strong>Inventory &amp; charity management system built with Laravel 8</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/php-7.4%2F8.0-777BB4?style=flat-square&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/laravel-8.x-FF2D20?style=flat-square&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/mysql-8.0-4479A1?style=flat-square&logo=mysql" alt="MySQL">
  <img src="https://img.shields.io/badge/docker-ready-2496ED?style=flat-square&logo=docker" alt="Docker">
  <img src="https://img.shields.io/badge/license-MIT-green?style=flat-square" alt="License">
</p>

---

## About

**Store** is an Arabic-language web application for managing store inventory, customers, providers, expenses, salaries, and charitable aid (عينيات). It includes SMS notifications, PDF/Excel export, and role-based access control.

Built for **IyadMarket**.

## Features

| Category | Details |
|---|---|
| **Products** | Add, edit, delete items; track quantity and status; import/export via Excel |
| **Customers** | Full customer profiles with identity, phone, family number, mosque affiliation |
| **Providers** | Track suppliers with balance and notes |
| **Mosques** | Manage mosque affiliations for customers |
| **Selectives (عينيات)** | Candidate selection and approval workflow for product distribution |
| **Import/Export Ainiat** | Track incoming and outgoing charitable goods with product-level detail |
| **Sanadat Sarf (سندات صرف)** | Disbursement vouchers with box and currency tracking |
| **Sanadat Qapd (سندات قبض)** | Receipt vouchers with box and currency tracking |
| **Expenses** | Log and categorize expenses |
| **Salaries** | Manage employee salaries |
| **Workers** | Employee registry |
| **Kafeels (كفيل)** | Sponsor management with kashf hesap (account statement) |
| **Orphans (يتامى)** | Orphan registry with payment tracking |
| **Wasis (وصي)** | Guardian management |
| **Boxes (صناديق)** | Multi-currency cash boxes with conversion support |
| **Currencies** | Manage exchange rates |
| **Movements** | Track fund movements between boxes |
| **SMS** | Send single or bulk SMS via hi5sms.com; template management with balance placeholders |
| **PDF Export** | Generate Arabic RTL PDFs (individual kashf hesap, bulk reports) via TCPDF |
| **Excel Export** | Export any entity to `.xlsx` via Maatwebsite Excel |
| **Permissions** | Role-based access via Spatie Laravel Permission |
| **Search** | Global search across customers, providers, products, ainiat |
| **Settings** | User profile management |

## Tech Stack

- **Backend:** Laravel 8 (PHP 7.4/8.0)
- **Database:** MySQL 8.0
- **Frontend:** Argon Dashboard (Bootstrap 4), jQuery
- **PDF:** TCPDF via `elibyy/tcpdf-laravel`
- **Excel:** Maatwebsite Excel + `rap2hpoutre/fast-excel`
- **Permissions:** `spatie/laravel-permission`
- **SMS:** hi5sms.com API
- **Containerization:** Docker + Docker Compose

## Prerequisites

- [Docker](https://docs.docker.com/get-docker/) & [Docker Compose](https://docs.docker.com/compose/install/)
- **OR** PHP 7.4/8.0 + MySQL 8.0 (for local development without Docker)

## Installation

### With Docker (recommended)

```bash
git clone https://github.com/your-username/Store.git
cd Store
cp .env.example .env
docker compose up --build
```

The app will be available at **http://localhost:8000**.

Docker handles everything: PHP extensions, Composer dependencies, database migrations, seeding, and the dev server.

### Without Docker

```bash
git clone https://github.com/your-username/Store.git
cd Store
composer install
cp .env.example .env
php artisan key:generate
```

Configure your `.env` database credentials, then:

```bash
php artisan migrate --seed
php artisan serve
```

### Frontend Assets (optional)

```bash
npm install
npm run dev      # development
npm run prod     # production
```

## Environment Variables

| Variable | Description | Default |
|---|---|---|
| `APP_NAME` | Application name | `Laravel` |
| `APP_ENV` | Environment (`local`, `production`) | `local` |
| `APP_URL` | Application URL | `http://localhost` |
| `DB_CONNECTION` | Database driver | `mysql` |
| `DB_HOST` | Database host | `127.0.0.1` |
| `DB_PORT` | Database port | `3306` |
| `DB_DATABASE` | Database name | `store` |
| `DB_USERNAME` | Database user | `root` |
| `DB_PASSWORD` | Database password | (empty) |
| `SMS_USERNAME` | hi5sms.com username | - |
| `SMS_PASSWORD` | hi5sms.com password | - |
| `SMS_SENDER` | SMS sender name | - |

## Project Structure

```
Store/
├── app/
│   ├── Exports/          # Maatwebsite Excel export classes (16)
│   ├── Http/
│   │   └── Controllers/
│   │       └── Admin/    # All controllers (21)
│   ├── Imports/          # Maatwebsite Excel import classes (10)
│   └── Models/           # Eloquent models (23)
├── database/
│   ├── migrations/       # Database migrations (26)
│   └── seeders/          # PermissionSeeder defines all roles
├── Docker/
│   └── entrypoint.sh     # Container startup (waits for MySQL, migrates, serves)
├── public/               # Compiled assets, index.php
├── resources/
│   └── views/
│       ├── admin/        # Blade views per domain (21 subdirectories)
│       ├── layouts/      # Main layout (main.blade.php)
│       └── includes/     # Shared partials (pagination, statistics, alerts)
├── routes/
│   └── web.php           # All application routes
├── docker-compose.yml    # PHP + MySQL services
├── Dockerfile            # PHP 7.4-FPM with extensions
└── webpack.mix.js        # Laravel Mix asset compilation
```

## Permissions

Permissions are seeded via `PermissionSeeder` (idempotent — safe to re-run) and follow the pattern `add_*` / `show_*`:

| Entity | Permissions |
|---|---|
| Boxes | `add_to_box` `show_boxes` |
| Currencies | `add_currencies` `show_currencies` |
| Customers | `add_customers` `show_customers` |
| Expenses | `add_expenses` `show_expenses` |
| Export Ainiat | `add_export_ainiats` `show_export_ainiats` |
| Import Ainiat | `add_import_ainiats` `show_import_ainiats` |
| Kafeels | `add_kafeels` `show_kafeels` |
| Movements | `show_movements` |
| Mosques | `add_mosques` `show_mosques` |
| Orphans | `add_orphans` `show_orphans` |
| Products | `add_products` `show_products` `edit_products` |
| Providers | `add_providers` `show_providers` |
| Salaries | `add_salaries` `show_salaries` |
| Sanadat Qapd | `add_sanadat_qapds` `show_sanadat_qapds` |
| Sanadat Sarf | `add_sanadat_sarfs` `show_sanadat_sarfs` |
| Selectives | `add_selectives` `show_selectives` |
| SMS | `add_sms` `show_sms` |
| Wasis | `add_wasis` `show_wasis` |
| Workers | `add_workers` `show_workers` |

## License

MIT License. See [LICENSE](LICENSE) for details.
