<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $__env->yieldContent('title', config('app.name')); ?></title>

    <?php echo app('Illuminate\Foundation\Vite')->fonts(); ?>

    <?php if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))): ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php endif; ?>

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
<div class="lg:grid lg:grid-cols-[17rem_1fr] sidebar-layout">

    <!-- Sidebar -->
    <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:flex-none lg:border-r lg:border-brand-200 lg:bg-white lg:p-4 lg:sticky lg:top-0 lg:h-screen dark:lg:border-neutral-800 dark:lg:bg-neutral-900 sidebar-sidebar relative">

        <!-- Botón de Toggle Redondo en el borde derecho -->
        <button
            type="button"
            id="sidebar-toggle-btn"
            aria-label="Expandir/Colapsar barra lateral"
            class="hidden lg:flex absolute -right-4 top-8 z-50 items-center justify-center w-8 h-8 rounded-full border border-brand-200 bg-white shadow-md transition-all duration-200 hover:shadow-lg hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-800 dark:hover:bg-neutral-700 text-neutral-500"
        >
            <svg class="w-4 h-4 transition-transform duration-200 sidebar-toggle-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <!-- Logo -->
        <div class="mb-6 flex justify-center sidebar-logo-full">
            <a href="<?php echo e(route('dashboard')); ?>">
                <img src="<?php echo e(asset('storage/logobg.png')); ?>" alt="Glenda Store" class="mx-auto h-16 w-auto max-w-full object-contain sm:h-20"/>
            </a>
            <a href="<?php echo e(route('dashboard')); ?>" class="hidden sidebar-logo-collapsed justify-center">
                <img src="<?php echo e(asset('storage/logobg.png')); ?>" alt="Glenda Store" class="h-10 w-auto max-w-full object-contain"/>
            </a>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto sidebar-nav pr-2">
            <?php
                $sections = [
                    [
                        'title' => 'General',
                        'items' => [
                            ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'fa fa-line-chart', 'permission' => 'dashboard_view'],
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
            ?>

            <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $visibleItems = collect($section['items'])->filter(function ($module) {
                        return empty($module['permission']) || (auth()->check() && auth()->user()->hasEffectivePermission($module['permission']));
                    });
                ?>

                <?php if($visibleItems->isNotEmpty()): ?>
                    <p class="mt-5 px-2 text-xs font-semibold uppercase tracking-wider text-brand-500 dark:text-brand-400 sidebar-section-title"><?php echo e($section['title']); ?></p>
                    <div class="mt-1.5 space-y-1">
                        <?php $__currentLoopData = $visibleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a
                                href="<?php echo e(route($module['route'])); ?>"
                                class="<?php echo e(request()->routeIs($module['route'], $module['route'].'.*') ? 'bg-brand-100 text-brand-800 dark:bg-brand-900/40 dark:text-brand-300' : 'text-neutral-600 hover:bg-brand-100 hover:text-brand-800 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white'); ?> flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition-colors sidebar-nav-item"
                            >
                                <?php if(! empty($module['icon'])): ?>
                                    <i class="<?php echo e($module['icon']); ?>" aria-hidden="true"></i>
                                <?php else: ?>
                                    <svg class="h-5 w-5 shrink-0 sidebar-nav-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="<?php echo e($module['path']); ?>" class="stroke-current" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                <?php endif; ?>
                                <span class="sidebar-nav-label"><?php echo e($module['label']); ?></span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>

        
        <div class="mt-4 border-t border-brand-100 pt-4 dark:border-neutral-800">
            <button
                type="button"
                id="theme-toggle"
                class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-neutral-600 transition-colors hover:bg-brand-100 hover:text-brand-800 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white sidebar-theme-toggle"
            >
                <i class="fa-solid fa-circle-half-stroke h-5 w-5 shrink-0 text-center" aria-hidden="true"></i>
                <span class="sidebar-theme-label" id="theme-label">Modo oscuro</span>
            </button>
        </div>

        <?php if(auth()->check()): ?>
            <div class="relative mt-2 border-t border-brand-100 pt-4 dark:border-neutral-800" data-user-menu>
                <button
                    type="button"
                    data-user-menu-toggle
                    aria-haspopup="menu"
                    aria-expanded="false"
                    title="<?php echo e(auth()->user()->full_name); ?>"
                    class="flex w-full items-center gap-3 rounded-xl p-2 text-left transition-colors hover:bg-brand-100 dark:hover:bg-neutral-800 sidebar-user-menu"
                >
                    <?php if(auth()->user()->photoUrl()): ?>
                        <img
                            src="<?php echo e(auth()->user()->photoUrl()); ?>"
                            alt="Foto de <?php echo e(auth()->user()->full_name); ?>"
                            class="h-9 w-9 shrink-0 rounded-full object-cover ring-2 ring-brand-200 dark:ring-neutral-700"
                        />
                    <?php else: ?>
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-600 text-xs font-bold text-white ring-2 ring-brand-200 dark:ring-neutral-700">
                            <?php echo e(auth()->user()->initials()); ?>

                        </span>
                    <?php endif; ?>
                    <div class="min-w-0 flex-1 sidebar-user-info">
                        <p class="truncate text-sm font-medium text-neutral-900 dark:text-white"><?php echo e(auth()->user()->full_name); ?></p>
                        <p class="truncate text-xs text-neutral-400 dark:text-neutral-500"><?php echo e(auth()->user()->email); ?></p>
                    </div>
                    <i class="fa-solid fa-chevron-up h-4 w-4 shrink-0 text-center text-neutral-400 sidebar-user-chevron" aria-hidden="true"></i>
                </button>

                <div
                    data-user-menu-dropdown
                    class="absolute bottom-full left-0 z-50 mb-2 hidden w-full min-w-[13rem] overflow-hidden rounded-xl border border-brand-200 bg-white shadow-xl shadow-brand-500/10 lg:left-auto lg:right-0 dark:border-neutral-700 dark:bg-neutral-900"
                    role="menu"
                >
                    <a
                        href="<?php echo e(route('profile.edit')); ?>"
                        role="menuitem"
                        class="flex items-center gap-2.5 px-4 py-2.5 text-sm font-medium text-neutral-700 transition-colors hover:bg-brand-50 hover:text-brand-800 dark:text-neutral-300 dark:hover:bg-neutral-800 dark:hover:text-white"
                    >
                        <i class="fa-solid fa-user h-4 w-4 text-center" aria-hidden="true"></i>
                        Mi perfil
                    </a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="border-t border-brand-100 dark:border-neutral-800">
                        <?php echo csrf_field(); ?>
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
        <?php endif; ?>
    </aside>

    <main class="p-6 sm:p-10 main-content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>

<?php echo $__env->yieldContent('scripts'); ?>
<script>
    (() => {
        const html = document.documentElement;
        const toggleBtn = document.getElementById('sidebar-toggle-btn');
        const toggleIcon = document.querySelector('.sidebar-toggle-icon');
        const sidebar = document.querySelector('.sidebar-sidebar');
        const mainContent = document.querySelector('.main-content');

        // Initialize from localStorage
        const savedSidebar = localStorage.getItem('sidebar-collapsed');
        if (savedSidebar === 'true') {
            html.classList.add('sidebar-collapsed');
            toggleIcon.style.transform = 'rotate(180deg)';
        }

        const toggleSidebar = () => {
            const isCollapsed = html.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);

            // Rotate icon
            toggleIcon.style.transform = isCollapsed ? 'rotate(180deg)' : 'rotate(0deg)';
        };

        toggleBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            toggleSidebar();
        });
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
<?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views/layouts/app.blade.php ENDPATH**/ ?>