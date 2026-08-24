# AGENTS.md

Laravel 13.24 app (app name: `Glenda_Store`). A POS/inventory system with 10 fully-built module pages (categories, employees, roles, audit, POS, cash registers, inventory, returns, reports, settings). The full schema from `database.md` is implemented as migrations (25 total). Models exist for all tables including `ProductReturn`, `ReturnDetail`, `AuditLog`, `Permission`, `Resource`, `Action`. Not yet a git repo.

## Docs vs. reality (trust the code)

- `README.md` brands the project "MODERATIO" with badges claiming Laravel 11, Tailwind 3.4, MySQL 8.2, JWT/Sanctum — all stale. Actual stack: Laravel 13, Tailwind v4, MySQL for local dev, and **no** JWT or Sanctum package installed (`composer.json` requires only `laravel/framework` + `laravel/tinker`).
- `database.md` is the authoritative schema reference. Tables: `roles`, `users`, `user_has_roles` (pivot), `customers`, `categories`, `products`, `cash_registers`, `sales`, `sale_details`, `returns`, `return_details`, `audit_log`, `resources`, `actions`, `permissions`, `role_has_permissions`, `user_has_permissions`. Enums are native MySQL `enum` columns.
- Users are linked to roles via `user_has_roles` pivot table (N:N). The `users` table has no `role_id` column — `EmployeeController` uses `$user->roles()->sync()`. `User::roles()` is `belongsToMany`. Views access role via `$employee->roles->first()`.
- Roles are free-text strings (varchar 100, unique), not enums. Roles have `is_active` and `is_super_admin` boolean columns.
- Do not rename/drop columns in a new migration — `renameColumn` is unsupported on the SQLite test runner. Avoid `dropColumn` on columns with composite indexes on SQLite (drop the index first).

## Current state & gotchas

- Auth IS functional: `AuthController` does real `Auth::attempt` (with `is_active => true`) + session regenerate; all routes except login/logout live inside a `Route::middleware('auth')` group in `routes/web.php`. `/` and `/login` both render the login page (`/` is named `login`, so the auth middleware redirects guests there). Login POST is throttled `throttle:5,1`. LOGOUT is audited BEFORE logout so the log keeps the user id. Users can manage their own profile at `/perfil` (`ProfileController` + `ProfileService`: password change requiring current password, photo upload/remove to `avatars/` on the public disk — `users.photo` column, `User::photoUrl()`/`initials()` helpers). The sidebar has a user block (avatar or initials fallback) with an "Mi perfil" / "Cerrar sesión" dropdown driven by `resources/js/user-menu.js`. Permissions are still not enforced by any middleware/gate — only resolved via `User::hasEffectivePermission()`. `php artisan db:seed` (via `DatabaseSeeder`) creates 3 roles and user `testuser`/`password` via pivot sync, then calls `DemoDataSeeder` (~30 days of sales, no-ops if sales already exist).
- All 10 module routes are fully built with real controllers, views, and services. No more "under construction" placeholders.
- Permissions are managed in two places, both via the shared modal partial `resources/views/modules/shared/partials/permissions-modal.blade.php` (rendered by GET `roles.permissions` / `employees.permissions`, saved via POST `.sync`, JS lives in `resources/js/permissions-modal.js` imported by `app.js`): **Roles** (`RoleController`, mode `ids` + super-admin toggle that force-checks/disables the matrix and syncs all 44 permissions) and **per-user** overrides in Empleados (`EmployeeController@syncPermissions`, mode `states`: posts `permissions[id] = allow|deny|inherit`, stored as `grant`/`deny` rows in `user_has_permissions` only when they differ from the role). Effective resolution: `User::hasEffectivePermission()` / `effectivePermissionIds()` — super_admin role → all; direct deny beats role; direct grant adds. Nothing enforces permissions yet (no auth).
- Audit logging is hybrid. **Automatic**: `app/Observers/AuditableObserver.php` logs `CREATED`/`UPDATED`/`DELETED` for every model registered in `$auditableModels` (`AppServiceProvider`) — adding a module means adding its model to that list; sensitive fields (password, remember_token, model hidden attrs) are masked as `[oculto]`, and `UPDATED` entries only contain changed keys as `{before: ..., after: ...}`. **Manual**: semantic events still call `AuditService::log()` from controllers — `TOGGLED` (toggle actions wrap their save in `Model::withoutEvents()` so no duplicate `UPDATED` row), `LOGIN`/`LOGOUT`, `OPENED`/`CLOSED` cash registers, `SALE_COMPLETED`, `PERMISSIONS_UPDATED`, returns `CREATED`, settings `UPDATED`. The bitácora page at `/bitacora` displays all logs with search/filter/modal.
- The dashboard queries real data via `app/Services/DashboardService.php` (KPIs + ApexCharts in `resources/js/dashboard.js`). `database/seeders/DemoDataSeeder.php` seeds ~30 days of sales — run `php artisan db:seed`.
- `layouts/app.blade.php` uses Blade `@yield`/`@section`, not Blade components.
- Layouts reference `storage/logobg.png` and `storage/portada.jpg` through the `public/storage` junction — keep those files present or pages show broken images. **Gotcha**: `public/storage` must be a Windows junction to `storage/app/public` (created by `php artisan storage:link`). If it's a real empty directory, images 404. `storage:link` refuses to replace an existing real directory (even with `--force`, which only deletes symlinks), so delete `public\storage` first, then rerun it.

## Dev environment

- Composer lock requires PHP >= 8.4.1. The `php` on PATH is XAMPP 8.2.12 and dies on Composer's platform check (every `php artisan`/`composer` call fails); use the 8.4 build at `C:\php\php.exe` (8.4.16, Xdebug — this is PhpStorm's "PHP8.4" interpreter).
- The 8.4 build has **no `intl`** extension, so `php artisan db:show`/`db:table` crash — inspect schema via tinker/SQL instead.
- Local dev uses MySQL (`.env`: db `glenda_store`, user `root`, pass `1234`). Sessions, cache, and queue are all `database`-driven, so run `php artisan migrate` before `composer run dev` or the app errors on missing tables.
- `composer run dev` starts `php artisan serve`, `php artisan queue:listen --tries=1`, and `npm run dev` concurrently. Individual commands: `php artisan serve`, `npm run dev` (Vite).

## Tests

- `composer test` (runs `php artisan config:clear` then `php artisan test`); plain `php artisan test` also works. Suite: 63 tests — `PageTest`, `AuthTest`, `ProfileTest`, `EmployeeTest`, `RolePermissionTest`, `UserPermissionTest`, `AuditObserverTest`, plus the two `ExampleTest`s. Protected-route tests must call `$this->signIn()` (helper in `tests/TestCase.php`).
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
- **Controllers**: thin — validate inline or with Form Requests, delegate to services; never business logic or queries in controllers.
- **Business logic** in `app/Services/` (one class per domain/feature), constructor dependency injection.
- **Audit logging**: lifecycle events (`CREATED`/`UPDATED`/`DELETED`) are automatic via `AuditableObserver` — register new models in `$auditableModels` (`AppServiceProvider`). Only call `AuditService::log($action, $table, $recordId, $details)` manually for semantic events (TOGGLED, LOGIN/LOGOUT, OPENED/CLOSED, SALE_COMPLETED, PERMISSIONS_UPDATED, ...); wrap toggle saves in `Model::withoutEvents()` to avoid duplicate rows.
- **Output**: API Resources or Eloquent casts; do not expose sensitive fields.
- **Routes**: declarative and named in `routes/web.php`; never logic in the routes file.
- **Migrations**: plural snake_case tables, snake_case columns; every schema change is a new migration, never edit a published one. Use factories and seeders for test data.
- **Middlewares**: cross-cutting logic (auth, roles, logging) as middleware, never inline in controllers.
- **Tests**: Feature for HTTP flows, Unit for pure logic; every feature or fix ships with a test.
- **Verify before finishing**: `vendor/bin/pint`, `composer test`, and `npm run build` when applicable.
