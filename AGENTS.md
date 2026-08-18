# AGENTS.md

Laravel 13.24 app (app name: `Glenda_Store`). Early-stage: a custom (placeholder) auth flow, a dashboard with ApexCharts, and 10 module pages all rendering a shared "under construction" view. The full schema from `database.md` IS implemented as migrations (13 total). Models exist for the transactional core (`User`, `Role`, `Category`, `Product`, `Customer`, `CashRegister`, `Sale`, `SaleDetail`) plus `DashboardService`; the returns/audit tables have no models yet. Not yet a git repo.

## Docs vs. reality (trust the code)

- `README.md` brands the project "MODERATIO" with badges claiming Laravel 11, Tailwind 3.4, MySQL 8.2, JWT/Sanctum — all stale. Actual stack: Laravel 13, Tailwind v4, MySQL for local dev, and **no** JWT or Sanctum package installed (`composer.json` requires only `laravel/framework` + `laravel/tinker`).
- `database.md` is the authoritative schema reference — now implemented. Tables: `roles`, `users` (custom: `full_name`/`username`/`is_active`/`role_id`, login by username, **no** `email`/`remember_token`), `customers`, `categories` (self-FK `parent_category_id`), `products`, `cash_registers`, `sales`, `sale_details`, `returns`, `return_details`, `audit_log`. Enums are native MySQL `enum` columns (`ADMINISTRATOR/CASHIER/WAREHOUSE`, `OPEN/CLOSED`, `COMPLETED/CANCELLED/PARTIALLY_RETURNED`, `CASH/CARD/TRANSFER`, `REGULAR/FREQUENT/WHOLESALER`).
- The `users` table is created by editing the **default** migration `0001_01_01_000000_create_users_table.php` (its `role_id` FK is added later in the `create_roles_table` migration). Do not rename/drop columns in a new migration — `renameColumn` is unsupported on the SQLite test runner.

## Current state & gotchas

- Auth is NOT functional: `AuthController::store` just redirects to `dashboard` with no credential check and no `auth` middleware — anyone can reach `/dashboard` without logging in. `php artisan db:seed` (via `DatabaseSeeder`) creates the 3 roles and user `testuser`/`password`, then calls `DemoDataSeeder` (~30 days of sales, no-ops if sales already exist) — but credentials are never verified anyway.
- All 10 module routes (`/pos`, `/caja`, `/inventario`, `/categorias`, `/empleados`, `/roles`, `/devoluciones`, `/reportes`, `/bitacora`, `/configuracion`) are one-line `Route::view` placeholders pointing at `modules.under-construction`. `tests/Feature/PageTest.php` asserts their titles.
- The dashboard queries real data via `app/Services/DashboardService.php` (KPIs + ApexCharts in `resources/js/dashboard.js`). `PageTest` uses `RefreshDatabase` because `/dashboard` hits the DB. `database/seeders/DemoDataSeeder.php` seeds ~30 days of sales — run `php artisan db:seed` (skips if sales already exist).
- `layouts/app.blade.php` uses Blade `@yield`/`@section`, not Blade components.
- Layouts reference `storage/logobg.png` and `storage/portada.jpg` through the `public/storage` junction — keep those files present or pages show broken images. **Gotcha**: `public/storage` must be a Windows junction to `storage/app/public` (created by `php artisan storage:link`). If it's a real empty directory, images 404. `storage:link` refuses to replace an existing real directory (even with `--force`, which only deletes symlinks), so delete `public\storage` first, then rerun it.

## Dev environment

- Composer lock requires PHP >= 8.4.1. The `php` on PATH is XAMPP 8.2.12 and dies on Composer's platform check (every `php artisan`/`composer` call fails); use the 8.4 build at `C:\php\php.exe` (8.4.16, Xdebug — this is PhpStorm's "PHP8.4" interpreter).
- The 8.4 build has **no `intl`** extension, so `php artisan db:show`/`db:table` crash — inspect schema via tinker/SQL instead.
- Local dev uses MySQL (`.env`: db `glenda_store`, user `root`, pass `1234`). Sessions, cache, and queue are all `database`-driven, so run `php artisan migrate` before `composer run dev` or the app errors on missing tables.
- `composer run dev` starts `php artisan serve`, `php artisan queue:listen --tries=1`, and `npm run dev` concurrently. Individual commands: `php artisan serve`, `npm run dev` (Vite).

## Tests

- `composer test` (runs `php artisan config:clear` then `php artisan test`); plain `php artisan test` also works. Suite: 6 tests — `PageTest` (4) plus the two `ExampleTest`s.
- `phpunit.xml` overrides everything: in-memory SQLite, array cache/session, sync queue, array mail. No MySQL needed to run tests. PHPUnit 12.
- **Gotcha**: the 8.4 build ships `pdo_sqlite`/`sqlite3` but they are commented out in `C:\php\php.ini` (lines ~935/946), so the suite currently errors with `could not find driver (Connection: sqlite...)`. Uncomment `extension=pdo_sqlite` and `extension=sqlite3` there to make tests pass. (The XAMPP 8.2 build has the driver but fails the version check.)

## Frontend

- Tailwind CSS v4 via `@tailwindcss/vite` — config is CSS-first in `resources/css/app.css`; there is **no** `tailwind.config.js`. Define palette colors as `--color-*` variables under `@theme` there; never hardcode colors in views. The `brand-*` palette is used throughout existing views.
- Vite 8 + `laravel-vite-plugin`; Google-style fonts fetched from Bunny at build. Always run `npm run build` after touching `resources/css` or `resources/js` if not using `npm run dev`.
- **Responsive**: ALL views must be responsive and work well on every device (mobile, tablet, desktop). Use Tailwind breakpoints (`sm`/`md`/`lg`/`xl`) and never fixed widths; test layouts at narrow widths too. The sidebar in `layouts/app.blade.php` is a CSS-only drawer on mobile/tablet (below `lg`): hidden off-canvas, opened via a floating hamburger button (top-right) + backdrop — all driven by one checkbox (`#sidebar-toggle`) + `group-has-checked:`/`lg:has-checked:` variants, no JS. Main content always shows at full width. On `lg+` it becomes a sticky in-grid sidebar that collapses to icons (4.5rem). Keep the CSS-only approach (no JS) and keep content full-width on small screens.

## Code style

- Laravel Pint, default preset (no `pint.json`): `vendor/bin/pint`.
- `.editorconfig`: 4-space indent, LF. Write code, commit messages, and error messages in English.

## Project conventions

Standard Laravel conventions; keep structure clean and consistent:

- **Models** (`app/Models/`): singular StudlyCase; `$fillable`, casts, relations. No raw SQL or business logic. Note: `User` uses Eloquent attributes `#[Fillable]`/`#[Hidden]` (Laravel 13) instead of `$fillable`/`$hidden`.
- **Controllers**: thin — validate with Form Requests (`app/Http/Requests/`) and delegate to services; never business logic or queries in controllers.
- **Business logic** in `app/Services/` (one class per domain/feature), constructor dependency injection; repositories only if they add value.
- **Output**: API Resources or Eloquent casts; do not expose sensitive fields.
- **Routes**: declarative and named in `routes/web.php` (or `api.php`); never logic in the routes file.
- **Migrations**: plural snake_case tables, snake_case columns; every schema change is a new migration, never edit a published one. Use factories and seeders for test data.
- **Middlewares**: cross-cutting logic (auth, roles, logging) as middleware, never inline in controllers.
- **Auth API**: stateless JWT; when the package is installed, configure it as a guard in `config/auth.php`. No sessions for APIs.
- **Tests**: Feature for HTTP flows, Unit for pure logic; every feature or fix ships with a test.
- **Verify before finishing**: `vendor/bin/pint`, `composer test`, and `npm run build` when applicable.
