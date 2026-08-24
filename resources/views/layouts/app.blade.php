<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name'))</title>

    @fonts

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <script>
        (() => {
            const saved = localStorage.getItem('theme');
            if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="min-h-screen bg-brand-50 font-sans text-neutral-900 antialiased dark:bg-neutral-950 dark:text-neutral-100">
<div class="group lg:grid lg:grid-cols-[17rem_1fr] transition-[grid-template-columns] duration-300 lg:has-checked:grid-cols-[4.5rem_1fr]">

    <button
        type="button"
        data-sidebar-toggle
        title="Menu"
        aria-label="Abrir o cerrar menú"
        class="fixed left-0 top-12 z-[60] flex -translate-x-1/2 cursor-pointer items-center justify-center rounded-full border border-brand-200 bg-white p-2.5 text-neutral-500 shadow-md transition-all duration-300 hover:bg-brand-100 hover:text-brand-800 group-has-checked:left-[min(85vw,18rem)] group-has-checked:translate-x-1/2 lg:hidden dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white"
    >
        <svg class="h-4 w-4 group-has-checked:hidden" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" class="stroke-current" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <svg class="hidden h-4 w-4 group-has-checked:block" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M6 18 18 6M6 6l12 12" class="stroke-current" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>

    <label for="sidebar-toggle" aria-hidden="true" class="pointer-events-none fixed inset-0 z-40 cursor-default bg-neutral-900/40 opacity-0 transition-opacity duration-300 group-has-checked:pointer-events-auto group-has-checked:opacity-100 lg:hidden"></label>

    <aside class="fixed inset-y-0 left-0 z-50 flex w-72 max-w-[85vw] -translate-x-full flex-col overflow-y-auto border-r border-brand-200 bg-white p-4 shadow-xl transition-transform duration-300 group-has-checked:translate-x-0 lg:bottom-auto lg:left-auto lg:z-auto lg:w-full lg:max-w-none lg:translate-x-0 lg:overflow-visible lg:shadow-none lg:sticky lg:top-0 lg:h-screen lg:p-5 lg:group-has-checked:min-w-0 dark:border-neutral-800 dark:bg-neutral-900">
        <input type="checkbox" id="sidebar-toggle" autocomplete="off" class="sr-only"/>

        <div class="flex justify-center px-2">
            <a href="{{ route('dashboard') }}" class="block lg:group-has-checked:hidden">
                <img src="{{ asset('storage/logobg.png') }}" alt="Glenda Store" class="mx-auto h-16 w-auto max-w-full object-contain sm:h-20 lg:h-24"/>
            </a>
        </div>

        <button
            type="button"
            data-sidebar-toggle
            title="Colapsar o expandir sidebar"
            aria-label="Colapsar o expandir sidebar"
            class="absolute right-0 top-12 z-20 hidden translate-x-1/2 cursor-pointer items-center justify-center rounded-full border border-brand-200 bg-white p-2.5 text-neutral-500 shadow-md transition-colors hover:bg-brand-100 hover:text-brand-800 lg:flex dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-white"
        >
            <svg class="h-4 w-4 transition-transform duration-300 lg:group-has-checked:rotate-180" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" class="stroke-current" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <nav class="mt-2 flex-1 space-y-1 lg:overflow-x-hidden lg:overflow-y-auto lg:group-has-checked:overflow-hidden">
            @php
                $sections = [
                    [
                        'title' => 'General',
                        'items' => [
                            ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa fa-line-chart', 'permission' => null],
                        ],
                    ],
                    [
                        'title' => 'Punto de Venta',
                        'items' => [
                            ['route' => 'pos', 'label' => 'POS', 'path' => 'M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z', 'permission' => 'sales_view'],
                            ['route' => 'cash-register.index', 'label' => 'Caja / Arqueo', 'path' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'permission' => 'cash_registers_view'],
                        ],
                    ],
                    [
                        'title' => 'Inventario',
                        'items' => [
                            ['route' => 'inventory.index', 'label' => 'Inventario', 'path' => 'M21 7.5l-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9', 'permission' => 'products_view'],
                            ['route' => 'categories.index', 'label' => 'Categorías', 'path' => 'M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3ZM6 6h.008v.008H6V6Z', 'permission' => 'categories_view'],
                        ],
                    ],
                    [
                        'title' => 'Seguridad',
                        'items' => [
                            ['route' => 'employees.index', 'label' => 'Empleados', 'path' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z', 'permission' => 'users_view'],
                            ['route' => 'roles.index', 'label' => 'Roles y Permisos', 'path' => 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z', 'permission' => 'roles_view'],
                            ['route' => 'audit.index', 'label' => 'Bitácora (Auditoría)', 'path' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2Z', 'permission' => 'audit_log_view'],
                        ],
                    ],
                    [
                        'title' => 'Post-Venta',
                        'items' => [
                            ['route' => 'returns.index', 'label' => 'Devoluciones / Notas de Crédito', 'path' => 'M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3', 'permission' => 'returns_view'],
                        ],
                    ],
                    [
                        'title' => 'Analítica',
                        'items' => [
                            ['route' => 'reports.index', 'label' => 'Reportes', 'path' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z', 'permission' => 'reports_view'],
                        ],
                    ],
                    [
                        'title' => 'Configuración',
                        'items' => [
                            ['route' => 'settings.index', 'label' => 'Configuración', 'path' => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.24-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z', 'permission' => 'settings_view'],
                        ],
                    ],
                ];
            @endphp

            @foreach ($sections as $section)
                @php
                    $visibleItems = collect($section['items'])->filter(function ($module) {
                        return empty($module['permission']) || (auth()->check() && auth()->user()->hasEffectivePermission($module['permission']));
                    });
                @endphp

                @if ($visibleItems->isNotEmpty())
                    <p class="mt-5 px-2 text-xs font-semibold uppercase tracking-wider text-brand-500 lg:group-has-checked:hidden dark:text-brand-400">{{ $section['title'] }}</p>
                    <div class="mt-1.5 space-y-1">
                        @foreach ($visibleItems as $module)
                            <a
                                href="{{ route($module['route']) }}"
                                class="{{ request()->routeIs($module['route'], $module['route'].'.*') ? 'bg-brand-100 text-brand-800 dark:bg-brand-900/40 dark:text-brand-300' : 'text-neutral-600 hover:bg-brand-100 hover:text-brand-800 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white' }} flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors lg:group-has-checked:justify-center lg:group-has-checked:px-0"
                            >
                                @if (! empty($module['icon']))
                                    <i class="{{ $module['icon'] }}" aria-hidden="true"></i>
                                @else
                                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="{{ $module['path'] }}" class="stroke-current" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @endif
                                <span class="lg:group-has-checked:hidden">{{ $module['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </nav>

{{--         Dark mode toggle--}}
        <div class="mt-4 border-t border-brand-100 pt-4 dark:border-neutral-800">
            <button
                type="button"
                id="theme-toggle"
                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-neutral-600 transition-colors hover:bg-brand-100 hover:text-brand-800 lg:group-has-checked:justify-center lg:group-has-checked:px-0 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white"
            >
                <i class="fa-solid fa-circle-half-stroke h-5 w-5 shrink-0 text-center" aria-hidden="true"></i>
                <span class="lg:group-has-checked:hidden" id="theme-label">Modo oscuro</span>
            </button>
        </div>

        @if (auth()->check())
            <div class="relative mt-2 border-t border-brand-100 pt-4 dark:border-neutral-800" data-user-menu>
                <button
                    type="button"
                    data-user-menu-toggle
                    aria-haspopup="menu"
                    aria-expanded="false"
                    title="{{ auth()->user()->full_name }}"
                    class="flex w-full items-center gap-3 rounded-xl p-2 text-left transition-colors hover:bg-brand-100 lg:group-has-checked:justify-center lg:group-has-checked:px-0 dark:hover:bg-neutral-800"
                >
                    @if (auth()->user()->photoUrl())
                        <img
                            src="{{ auth()->user()->photoUrl() }}"
                            alt="Foto de {{ auth()->user()->full_name }}"
                            class="h-9 w-9 shrink-0 rounded-full object-cover ring-2 ring-brand-200 dark:ring-neutral-700"
                        />
                    @else
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white ring-2 ring-brand-200 dark:ring-neutral-700">
                            {{ auth()->user()->initials() }}
                        </span>
                    @endif
                    <div class="min-w-0 flex-1 lg:group-has-checked:hidden">
                        <p class="truncate text-sm font-medium text-neutral-900 dark:text-white">{{ auth()->user()->full_name }}</p>
                        <p class="truncate text-xs text-neutral-400 dark:text-neutral-500">{{ auth()->user()->email }}</p>
                    </div>
                    <i class="fa-solid fa-chevron-up h-4 w-4 shrink-0 text-center text-neutral-400 lg:group-has-checked:hidden" aria-hidden="true"></i>
                </button>

                <div
                    data-user-menu-dropdown
                    class="absolute bottom-full left-0 z-50 mb-2 hidden w-full min-w-[13rem] overflow-hidden rounded-xl border border-brand-200 bg-white shadow-xl shadow-brand-500/10 lg:left-auto lg:right-0 dark:border-neutral-700 dark:bg-neutral-900"
                    role="menu"
                >
                    <a
                        href="{{ route('profile.edit') }}"
                        role="menuitem"
                        class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-neutral-700 transition-colors hover:bg-brand-50 hover:text-brand-800 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white"
                    >
                        <i class="fa-solid fa-user h-4 w-4 text-center" aria-hidden="true"></i>
                        Mi perfil
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-brand-100 dark:border-neutral-800">
                        @csrf
                        <button
                            type="submit"
                            role="menuitem"
                            class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-950/30"
                        >
                            <i class="fa-solid fa-arrow-right-from-bracket h-4 w-4 text-center" aria-hidden="true"></i>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </aside>

    <main class="p-6 sm:p-10">
        @yield('content')
    </main>
</div>

@yield('scripts')
<script>
    (() => {
        const sidebarToggle = document.getElementById('sidebar-toggle');

        const expand = () => {
            sidebarToggle.checked = false;
            requestAnimationFrame(() => {
                sidebarToggle.checked = false;
            });
        };

        document.querySelectorAll('[data-sidebar-toggle]').forEach((toggle) => {
            toggle.addEventListener('click', (event) => {
                event.preventDefault();
                sidebarToggle.checked = !sidebarToggle.checked;
            });
        });

        window.addEventListener('pageshow', expand);
        expand();
    })();
</script>
<script>
    (() => {
        const html = document.documentElement;
        const label = document.getElementById('theme-label');

        function isDark() {
            return html.classList.contains('dark');
        }

        function updateLabel() {
            if (label) {
                label.textContent = isDark() ? 'Modo claro' : 'Modo oscuro';
            }
        }

        document.getElementById('theme-toggle')?.addEventListener('click', () => {
            html.classList.toggle('dark');
            localStorage.setItem('theme', isDark() ? 'dark' : 'light');
            updateLabel();
        });

        updateLabel();
    })();
</script>
</body>
</html>
