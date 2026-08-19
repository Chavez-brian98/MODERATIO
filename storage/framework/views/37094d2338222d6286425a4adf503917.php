<?php $__env->startSection('title', 'Bitácora · ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $actionLabels = [
            'CREATED' => 'Creación',
            'UPDATED' => 'Actualización',
            'DELETED' => 'Eliminación',
            'TOGGLED' => 'Cambio de estado',
            'LOGIN' => 'Inicio de sesión',
            'LOGOUT' => 'Cierre de sesión',
            'OPENED' => 'Apertura',
            'CLOSED' => 'Cierre',
            'SALE_COMPLETED' => 'Venta realizada',
        ];
        $actionBadges = [
            'CREATED' => 'bg-green-50 text-green-700 dark:bg-green-900/40 dark:text-green-400',
            'UPDATED' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
            'DELETED' => 'bg-red-50 text-red-700 dark:bg-red-900/40 dark:text-red-400',
            'TOGGLED' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
            'LOGIN' => 'bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400',
            'LOGOUT' => 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400',
            'OPENED' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400',
            'CLOSED' => 'bg-orange-50 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400',
            'SALE_COMPLETED' => 'bg-violet-50 text-violet-700 dark:bg-violet-900/40 dark:text-violet-400',
        ];
        $tableLabels = [
            'users' => 'Empleados',
            'products' => 'Productos',
            'categories' => 'Categorías',
            'roles' => 'Roles',
            'sales' => 'Ventas',
            'sale_details' => 'Detalle de ventas',
            'cash_registers' => 'Cajas',
            'customers' => 'Clientes',
            'returns' => 'Devoluciones',
            'settings' => 'Configuración',
        ];

        $todayCount = $logs->filter(fn ($log) => $log->created_at->isToday())->count();
        $mostActiveUser = $logs->where('user_id', '!=', null)->pluck('user')->filter()->groupBy('full_name')->sortByDesc(fn ($group) => $group->count())->keys()->first() ?? '—';
        $mostAffectedTable = $logs->groupBy('affected_table')->sortByDesc(fn ($group) => $group->count())->keys()->first() ?? '—';

        $stats = [
            [
                'label' => 'Total de registros',
                'value' => $logs->count(),
                'sub' => 'En la bitácora',
                'path' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
            ],
            [
                'label' => 'Registros hoy',
                'value' => $todayCount,
                'sub' => now()->isoFormat('D MMMM YYYY'),
                'path' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
            ],
            [
                'label' => 'Usuario más activo',
                'value' => $mostActiveUser,
                'sub' => 'Mayor cantidad de acciones',
                'path' => 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z',
            ],
            [
                'label' => 'Tabla más afectada',
                'value' => $tableLabels[$mostAffectedTable] ?? $mostAffectedTable,
                'sub' => 'Más registros modificados',
                'path' => 'M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z',
            ],
        ];
    ?>

    <?php echo $__env->make('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Bitácora (Auditoría)'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">Bitácora</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Registro de actividades y cambios realizados en el sistema.</p>
        </div>
    </header>

    <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="<?php echo e($stat['path']); ?>" class="stroke-current" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm text-neutral-500 dark:text-neutral-400"><?php echo e($stat['label']); ?></p>
                        <p class="text-xl font-semibold tracking-tight text-neutral-900 dark:text-white sm:text-2xl"><?php echo e($stat['value']); ?></p>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500"><?php echo e($stat['sub']); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </section>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:max-w-sm">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400 dark:text-neutral-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" class="stroke-current" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input
                type="search"
                id="audit-search"
                placeholder="Buscar en la bitácora..."
                autocomplete="off"
                class="w-full rounded-xl border border-neutral-300 bg-white py-2.5 pl-10 pr-4 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
            />
        </div>

        <div class="flex flex-wrap gap-2">
            <select
                id="audit-filter-action"
                class="rounded-xl border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300"
            >
                <option value="">Todas las acciones</option>
                <option value="CREATED">Creación</option>
                <option value="UPDATED">Actualización</option>
                <option value="DELETED">Eliminación</option>
                <option value="TOGGLED">Cambio de estado</option>
                <option value="LOGIN">Inicio de sesión</option>
                <option value="LOGOUT">Cierre de sesión</option>
            </select>

            <select
                id="audit-filter-table"
                class="rounded-xl border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300"
            >
                <option value="">Todas las tablas</option>
                <?php $__currentLoopData = $logs->pluck('affected_table')->unique()->sort(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($table); ?>"><?php echo e($tableLabels[$table] ?? $table); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 text-sm dark:divide-neutral-800">
                <thead class="bg-brand-50/60 dark:bg-neutral-800/60">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                    <th class="px-4 py-3 sm:px-6">ID</th>
                    <th class="px-4 py-3 sm:px-6">Fecha y hora</th>
                    <th class="px-4 py-3 sm:px-6">Usuario</th>
                    <th class="px-4 py-3 sm:px-6">Acción</th>
                    <th class="hidden px-4 py-3 sm:px-6 md:table-cell">Tabla</th>
                    <th class="hidden px-4 py-3 sm:px-6 md:table-cell">Registro</th>
                    <th class="hidden px-4 py-3 sm:px-6 lg:table-cell">IP</th>
                    <th class="px-4 py-3 text-right sm:px-6">Detalle</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr
                        data-audit-row
                        data-audit-search="<?php echo e(strtolower(trim(($log->user?->full_name ?? 'Sistema') . ' ' . ($actionLabels[$log->action] ?? $log->action) . ' ' . ($tableLabels[$log->affected_table] ?? $log->affected_table) . ' ' . ($log->record_id ?? '')))); ?>"
                        data-audit-action="<?php echo e($log->action); ?>"
                        data-audit-table="<?php echo e($log->affected_table); ?>"
                        class="hover:bg-brand-50/40 dark:hover:bg-neutral-800/50"
                    >
                        <td class="whitespace-nowrap px-4 py-3 text-neutral-500 dark:text-neutral-400 sm:px-6">#<?php echo e($log->id); ?></td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                            <p class="text-sm text-neutral-900 dark:text-white"><?php echo e($log->created_at->format('d/m/Y')); ?></p>
                            <p class="text-xs text-neutral-400 dark:text-neutral-500"><?php echo e($log->created_at->format('H:i:s')); ?></p>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                            <?php if($log->user): ?>
                                <p class="font-medium text-neutral-900 dark:text-white"><?php echo e($log->user->full_name); ?></p>
                                <p class="text-xs text-neutral-400 dark:text-neutral-500"><?php echo e($log->user->email); ?></p>
                            <?php else: ?>
                                <p class="text-neutral-400 dark:text-neutral-500">Sistema</p>
                            <?php endif; ?>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?php echo e($actionBadges[$log->action] ?? 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400'); ?>">
                                <?php echo e($actionLabels[$log->action] ?? $log->action); ?>

                            </span>
                        </td>
                        <td class="hidden whitespace-nowrap px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400 sm:px-6 md:table-cell">
                            <?php echo e($tableLabels[$log->affected_table] ?? $log->affected_table); ?>

                        </td>
                        <td class="hidden whitespace-nowrap px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400 sm:px-6 md:table-cell">
                            <?php echo e($log->record_id ? '#' . $log->record_id : '—'); ?>

                        </td>
                        <td class="hidden whitespace-nowrap px-4 py-3 text-sm text-neutral-400 dark:text-neutral-500 sm:px-6 lg:table-cell">
                            <?php echo e($log->source_ip ?? '—'); ?>

                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right sm:px-6">
                            <button
                                type="button"
                                title="Ver detalle"
                                data-view-audit="<?php echo e(route('audit.show', $log)); ?>"
                                class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-brand-700 transition-all hover:scale-110 hover:bg-brand-100 hover:shadow-sm ml-auto dark:text-brand-400 dark:hover:bg-brand-900/40"
                            >
                                <i class="fa-solid fa-eye text-sm" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center sm:px-6">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" class="stroke-current" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-medium text-neutral-500 dark:text-neutral-400">No hay registros en la bitácora</p>
                            <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">Las acciones realizadas en el sistema aparecerán aquí.</p>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr id="no-results-row" class="hidden">
                    <td colspan="8" class="px-4 py-12 text-center sm:px-6">
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Sin resultados</p>
                        <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">No se encontraron registros para la búsqueda.</p>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="audit-modal-container" aria-hidden="true"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('audit-search');
            const filterAction = document.getElementById('audit-filter-action');
            const filterTable = document.getElementById('audit-filter-table');
            const rows = document.querySelectorAll('[data-audit-row]');
            const noResultsRow = document.getElementById('no-results-row');

            function applyFilters() {
                const term = searchInput.value.trim().toLowerCase();
                const actionFilter = filterAction.value;
                const tableFilter = filterTable.value;
                let visible = 0;

                if (rows.length === 0) {
                    noResultsRow.classList.add('hidden');
                    return;
                }

                rows.forEach((row) => {
                    const matchesSearch = row.dataset.auditSearch.includes(term);
                    const matchesAction = !actionFilter || row.dataset.auditAction === actionFilter;
                    const matchesTable = !tableFilter || row.dataset.auditTable === tableFilter;
                    const show = matchesSearch && matchesAction && matchesTable;
                    row.classList.toggle('hidden', !show);
                    if (show) visible++;
                });

                noResultsRow.classList.toggle('hidden', visible > 0);
            }

            searchInput.addEventListener('input', applyFilters);
            filterAction.addEventListener('change', applyFilters);
            filterTable.addEventListener('change', applyFilters);

            const modalContainer = document.getElementById('audit-modal-container');
            const modalTrigger = { element: null };

            const closeAuditModal = () => {
                modalContainer.innerHTML = '';
                modalContainer.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
                modalTrigger.element?.focus();
            };

            document.querySelectorAll('[data-view-audit]').forEach((button) => {
                button.addEventListener('click', async () => {
                    modalTrigger.element = document.activeElement;
                    modalContainer.innerHTML = '';

                    try {
                        const response = await fetch(button.dataset.viewAudit, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html',
                            },
                        });

                        if (!response.ok) return;

                        modalContainer.innerHTML = await response.text();

                        const modal = modalContainer.querySelector('[data-audit-modal]');
                        if (!modal) {
                            closeAuditModal();
                            return;
                        }

                        modalContainer.setAttribute('aria-hidden', 'false');
                        document.body.classList.add('overflow-hidden');

                        const focusable = modal.querySelectorAll('button, [href], [tabindex]:not([tabindex="-1"])');
                        const firstFocusable = focusable[0];
                        const lastFocusable = focusable[focusable.length - 1];
                        firstFocusable?.focus();

                        modal.addEventListener('click', (event) => {
                            if (event.target === modal.querySelector('[data-modal-backdrop]')) {
                                closeAuditModal();
                            }
                        });

                        modal.addEventListener('keydown', (event) => {
                            if (event.key === 'Escape') {
                                closeAuditModal();
                                return;
                            }
                            if (event.key === 'Tab' && focusable.length > 0) {
                                if (event.shiftKey && document.activeElement === firstFocusable) {
                                    event.preventDefault();
                                    lastFocusable.focus();
                                } else if (!event.shiftKey && document.activeElement === lastFocusable) {
                                    event.preventDefault();
                                    firstFocusable.focus();
                                }
                            }
                        });

                        modal.querySelectorAll('[data-modal-close]').forEach((element) => {
                            element.addEventListener('click', closeAuditModal);
                        });
                    } catch (error) {
                        closeAuditModal();
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views/modules/audit/index.blade.php ENDPATH**/ ?>