---
name: laravel
description: Use when working with Laravel 8 PHP framework — routes, controllers, models, migrations, Blade views, Eloquent, middleware, artisan commands, seeder, service providers. Covers PHP 7.4/8.0 compatibility.
---

# Laravel 8 Skill

## Project Context

This is a Laravel 8 Arabic store/inventory management system. PHP 7.4/8.0 only — do not use PHP 8.1+ features (enums, fibers, readonly properties, intersection types, etc.).

## Key Conventions

- **Routes**: All in `routes/web.php`. Use explicit controller method references: `[Controller::class, 'method']`. No resource controllers.
- **Controllers**: All under `App\Http\Controllers\Admin\`. Constructor uses `$this->middleware('auth')`.
- **Models**: All in `App\Models\`. Use Eloquent relationships (hasMany, belongsTo, etc.).
- **Migrations**: In `database/migrations/`. Use `Schema::create` / `Schema::table`. MySQL 8.0 dialect.
- **Seeders**: `database/seeders/PermissionSeeder.php` uses Spatie Permission's `firstOrCreate` for idempotent seeding.
- **Blade**: Views in `resources/views/admin/` organized by domain. Layout: `resources/views/layouts/main.blade.php`.
- **AJAX pattern**: Controllers check `$request->ajax()` and return JSON with rendered Blade partials via `response()->json(['table' => view()->make(...)->render()])`.
- **Auth**: `Auth::routes(['register' => true, 'reset' => false, 'verify' => false])`.
- **Permissions**: `spatie/laravel-permission`. Pattern: `add_*` for create+edit, `show_*` for view. Enforced via `@can` in Blade and `->middleware('can:permission_name')` on routes.
- **PDF**: TCPDF via `elibyy/tcpdf-laravel` (alias `PDF::`). Output to `storage/app/public/pdf/`.
- **Excel**: Maatwebsite Excel (alias `Excel::`). Exports in `app/Exports/`, imports in `app/Imports/`. Output to `storage/app/public/xlsx/`.
- **Pagination**: `config('app.page')` controls per-page count.
- **SMS**: hi5sms.com API via env vars `SMS_USERNAME`, `SMS_PASSWORD`, `SMS_SENDER`.

## Common Artisan Commands

```bash
php artisan make:model ModelName -m          # model + migration
php artisan make:controller Admin/ControllerName  # controller
php artisan migrate --seed                   # run migrations + seeders
php artisan permission:cache-reset           # clear Spatie permission cache
php artisan cache:clear                      # clear all caches
php artisan serve                            # dev server
```

## Eloquent Patterns

```php
// Relationships
class Customer extends Model {
    public function mosque() { return $this->belongsTo(Mosque::class); }
    public function sanadatSarfs() { return $this->hasMany(SanadatSarf::class); }
}

// Scopes
public function scopeActive($query) { return $query->where('status', 1); }
```

## Blade Patterns

```blade
@can('add_customers')
    <a href="...">Add Customer</a>
@endcan

@include('admin.customer.table')  {{-- partial include --}}

@foreach($items as $item)
    @include('admin.customer.table', ['customer' => $item])
@endforeach
```

## Migration Patterns

```php
Schema::create('customers', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone', 20)->nullable();
    $table->decimal('balance', 10, 4)->default(0);
    $table->foreignId('mosque_id')->nullable()->constrained()->nullOnDelete();
    $table->timestamps();
});
```
