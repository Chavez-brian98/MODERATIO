<?php $__env->startSection('title', 'Clientes · ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Clientes'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php
        $typeLabels = [
            'REGULAR' => 'Regular',
            'FREQUENT' => 'Frecuente',
            'WHOLESALER' => 'Mayorista',
        ];

        $stats = [
            [
                'label' => 'Clientes registrados',
                'value' => $customers->total(),
                'sub' => 'Total en el sistema',
                'path' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
            ],
            [
                'label' => 'Clientes activos',
                'value' => $customers->filter(fn ($customer) => $customer->is_active)->count(),
                'sub' => 'Disponibles para vender',
                'path' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
            ],
            [
                'label' => 'Clientes mayoristas',
                'value' => $customers->filter(fn ($customer) => $customer->customer_type === 'WHOLESALER')->count(),
                'sub' => 'Con precios al por mayor',
                'path' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z',
            ],
        ];
    ?>

    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">Clientes</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Administra la cartera de clientes del negocio.</p>
        </div>
    </header>

    <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
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

    <div class="mt-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative w-full sm:max-w-sm">
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400 dark:text-neutral-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" class="stroke-current" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input
                    type="search"
                    id="customer-search"
                    placeholder="Buscar cliente..."
                    autocomplete="off"
                    class="w-full rounded-xl border border-neutral-300 bg-white py-2.5 pl-10 pr-4 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                />
            </div>

            <form method="GET" action="<?php echo e(route('customers.index')); ?>">
                <?php if(request('type')): ?>
                    <input type="hidden" name="type" value="<?php echo e(request('type')); ?>">
                <?php endif; ?>
                <select
                    name="status"
                    aria-label="Filtrar por estado"
                    onchange="this.form.submit()"
                    class="rounded-xl border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                >
                    <option value="all" <?php echo e($status === 'all' ? 'selected' : ''); ?>>Cualquier estado</option>
                    <option value="active" <?php echo e($status === 'active' ? 'selected' : ''); ?>>Solo activos</option>
                    <option value="inactive" <?php echo e($status === 'inactive' ? 'selected' : ''); ?>>Solo inactivos</option>
                </select>
            </form>

            <form method="GET" action="<?php echo e(route('customers.index')); ?>">
                <?php if(request('status')): ?>
                    <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                <?php endif; ?>
                <select
                    name="type"
                    id="customer-type-filter"
                    aria-label="Filtrar por tipo"
                    onchange="this.form.submit()"
                    class="rounded-xl border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                >
                    <option value="">Todos los tipos</option>
                    <option value="REGULAR" <?php echo e(request('type') === 'REGULAR' ? 'selected' : ''); ?>>Regular</option>
                    <option value="FREQUENT" <?php echo e(request('type') === 'FREQUENT' ? 'selected' : ''); ?>>Frecuente</option>
                    <option value="WHOLESALER" <?php echo e(request('type') === 'WHOLESALER' ? 'selected' : ''); ?>>Mayorista</option>
                </select>
            </form>
        </div>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customers_create')): ?>
            <a
                href="<?php echo e(route('customers.create')); ?>"
                class="ml-auto inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 focus-visible:ring-offset-2"
            >
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Nuevo cliente
            </a>
        <?php endif; ?>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 text-sm dark:divide-neutral-800" role="grid">
                <thead class="bg-brand-50/60 dark:bg-neutral-800/60">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                    <th class="px-4 py-3 sm:px-6">ID</th>
                    <th class="px-4 py-3 sm:px-6">Cliente</th>
                    <th class="hidden px-4 py-3 sm:px-6 md:table-cell">NIT</th>
                    <th class="hidden px-4 py-3 sm:px-6 md:table-cell">Teléfono</th>
                    <th class="hidden px-4 py-3 sm:px-6 lg:table-cell">Tipo</th>
                    <th class="hidden px-4 py-3 sm:px-6 lg:table-cell">Compras</th>
                    <th class="px-4 py-3 sm:px-6">Estado</th>
                    <th class="px-4 py-3 text-right sm:px-6">Acciones</th>
                </tr>
                </thead>
                <tbody id="customer-tbody" class="divide-y divide-neutral-100 dark:divide-neutral-800">
                <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr
                        data-customer-row
                        data-customer-search="<?php echo e(strtolower(trim($customer->first_name.' '.$customer->last_name.' '.$customer->tax_id.' '.$customer->phone.' '.$customer->email))); ?>"
                        data-customer-type="<?php echo e($customer->customer_type); ?>"
                        class="<?php echo e($customer->is_active ? 'hover:bg-brand-50/40 dark:hover:bg-neutral-800/50' : 'bg-neutral-50 hover:bg-neutral-100/60 dark:bg-neutral-800/30 dark:hover:bg-neutral-800/60'); ?>"
                    >
                        <td class="whitespace-nowrap px-4 py-3 text-neutral-500 dark:text-neutral-400 sm:px-6">#<?php echo e($customer->id); ?></td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                            <p class="<?php echo e($customer->is_active ? 'font-medium text-neutral-900 dark:text-white' : 'font-medium text-neutral-400 dark:text-neutral-500'); ?>">
                                <?php echo e($customer->first_name); ?> <?php echo e($customer->last_name); ?>

                            </p>
                            <?php if($customer->email): ?>
                                <p class="mt-0.5 max-w-xs truncate text-xs text-neutral-400 dark:text-neutral-500"><?php echo e($customer->email); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="hidden whitespace-nowrap px-4 py-3 text-neutral-600 dark:text-neutral-400 sm:px-6 md:table-cell"><?php echo e($customer->tax_id ?? '—'); ?></td>
                        <td class="hidden whitespace-nowrap px-4 py-3 text-neutral-600 dark:text-neutral-400 sm:px-6 md:table-cell"><?php echo e($customer->phone ?? '—'); ?></td>
                        <td class="hidden whitespace-nowrap px-4 py-3 sm:px-6 lg:table-cell">
                            <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                                <?php echo e($typeLabels[$customer->customer_type] ?? $customer->customer_type); ?>

                            </span>
                        </td>
                        <td class="hidden whitespace-nowrap px-4 py-3 text-neutral-600 dark:text-neutral-400 sm:px-6 lg:table-cell"><?php echo e($customer->sales_count); ?></td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                            <?php if($customer->is_active): ?>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                    Activo
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-neutral-400"></span>
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right sm:px-6">
                            <div class="flex items-center justify-end gap-1.5">
                                <button
                                    type="button"
                                    title="Ver detalle"
                                    data-view-customer="<?php echo e(route('customers.show', $customer)); ?>"
                                    class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-brand-700 transition-all hover:scale-110 hover:bg-brand-100 hover:shadow-sm dark:text-brand-400 dark:hover:bg-brand-900/40"
                                >
                                    <i class="fa-solid fa-eye text-sm" aria-hidden="true"></i>
                                </button>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customers_edit')): ?>
                                    <a
                                        href="<?php echo e(route('customers.edit', $customer)); ?>"
                                        title="Editar"
                                        class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-blue-600 transition-all hover:scale-110 hover:bg-blue-100 hover:shadow-sm dark:text-blue-400 dark:hover:bg-blue-900/40"
                                    >
                                        <i class="fa-solid fa-pen-to-square text-sm" aria-hidden="true"></i>
                                    </a>

                                    <form method="POST" action="<?php echo e(route('customers.toggle', $customer)); ?>" class="inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <?php if($customer->is_active): ?>
                                            <button type="submit" title="Deshabilitar" data-disable-customer
                                                class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-red-600 transition-all hover:scale-110 hover:bg-red-100 hover:shadow-sm dark:text-red-400 dark:hover:bg-red-900/40">
                                                <i class="fa-solid fa-ban text-sm" aria-hidden="true"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" title="Activar"
                                                class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-green-600 transition-all hover:scale-110 hover:bg-green-100 hover:shadow-sm dark:text-green-400 dark:hover:bg-green-900/40">
                                                <i class="fa-solid fa-check text-sm" aria-hidden="true"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customers_delete')): ?>
                                    <?php if (! ($customer->is_active)): ?>
                                        <form method="POST" action="<?php echo e(route('customers.destroy', $customer)); ?>" class="inline">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" title="Eliminar" data-delete-customer
                                                class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-red-600 transition-all hover:scale-110 hover:bg-red-100 hover:shadow-sm dark:text-red-400 dark:hover:bg-red-900/40">
                                                <i class="fa-solid fa-trash text-sm" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center sm:px-6">
                            <div class="mx-auto flex max-w-sm flex-col items-center gap-3">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-100 text-brand-600 dark:bg-brand-900/40 dark:text-brand-400">
                                    <i class="fa-solid fa-users text-xl" aria-hidden="true"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-900 dark:text-white">No hay clientes</p>
                                    <p class="mt-0.5 text-sm text-neutral-500 dark:text-neutral-400">
                                        <?php if($status !== 'all' || request('type')): ?>
                                            No hay clientes con este filtro.
                                        <?php else: ?>
                                            Registra tu primer cliente para comenzar.
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                    <tr id="no-results-row" class="hidden">
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-neutral-500 dark:text-neutral-400 sm:px-6">
                            No se encontraron clientes que coincidan con la búsqueda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php echo $__env->make('partials.pagination', ['paginator' => $customers], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <div id="customer-modal-container" aria-hidden="true"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('customer-search');
            const typeFilter = document.getElementById('customer-type-filter');
            const rows = document.querySelectorAll('[data-customer-row]');
            const noResultsRow = document.getElementById('no-results-row');

            const applyFilters = () => {
                const term = searchInput.value.trim().toLowerCase();
                const type = typeFilter ? typeFilter.value : '';
                let visible = 0;

                if (rows.length === 0) {
                    return;
                }

                rows.forEach((row) => {
                    const matchesSearch = row.dataset.customerSearch.includes(term);
                    const matchesType = !type || row.dataset.customerType === type;
                    const show = matchesSearch && matchesType;
                    row.classList.toggle('hidden', !show);
                    if (show) visible++;
                });

                noResultsRow?.classList.toggle('hidden', visible > 0);
            };

            searchInput.addEventListener('input', applyFilters);

            document.querySelectorAll('[data-disable-customer]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    const form = button.closest('form');

                    Swal.fire({
                        title: '¿Deshabilitar cliente?',
                        text: 'El cliente quedará inactivo y no podrá asignarse a nuevas ventas.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, deshabilitar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: window.SwalColors.brand,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            document.querySelectorAll('[data-delete-customer]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    const form = button.closest('form');

                    Swal.fire({
                        title: '¿Eliminar cliente?',
                        text: 'Esta acción eliminará el cliente de forma permanente.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: window.SwalColors.danger,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            const modalContainer = document.getElementById('customer-modal-container');
            const modalTrigger = { element: null };

            const closeCustomerModal = () => {
                modalContainer.innerHTML = '';
                modalContainer.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
                modalTrigger.element?.focus();
            };

            document.querySelectorAll('[data-view-customer]').forEach((button) => {
                button.addEventListener('click', async () => {
                    modalTrigger.element = document.activeElement;
                    modalContainer.innerHTML = '';

                    try {
                        const response = await fetch(button.dataset.viewCustomer, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html',
                            },
                        });

                        if (!response.ok) {
                            return;
                        }

                        modalContainer.innerHTML = await response.text();

                        const modal = modalContainer.querySelector('[data-customer-modal]');
                        if (!modal) {
                            closeCustomerModal();
                            return;
                        }

                        modalContainer.setAttribute('aria-hidden', 'false');
                        document.body.classList.add('overflow-hidden');

                        const focusable = modal.querySelectorAll('button, [href], [tabindex]:not([tabindex="-1"])');
                        const firstFocusable = focusable[0];
                        const lastFocusable = focusable[focusable.length - 1];
                        firstFocusable?.focus();

                        modal.querySelectorAll('[data-modal-close]').forEach((element) => {
                            element.addEventListener('click', closeCustomerModal);
                        });

                        modal.addEventListener('click', (event) => {
                            if (event.target === modal.querySelector('[data-modal-backdrop]')) {
                                closeCustomerModal();
                            }
                        });

                        modal.addEventListener('keydown', (event) => {
                            if (event.key === 'Escape') {
                                closeCustomerModal();
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
                    } catch (error) {
                        // Network error: keep silent like other modules.
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views/modules/customers/index.blade.php ENDPATH**/ ?>