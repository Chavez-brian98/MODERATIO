<?php $__env->startSection('title', 'Categorías · ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Categorías'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">Categorías</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Organiza tus productos por categorías.</p>
        </div>
    </header>

    <?php
        $stats = [
            [
                'label' => 'Categorías creadas',
                'value' => $categories->count(),
                'sub' => 'Total registradas',
                'path' => 'M12 2.25 21 6.75l-9 4.5-9-4.5 9-4.5Zm-9 9 9 4.5 9-4.5M3.75 15.75 12 20.25l8.25-4.5',
            ],
            [
                'label' => 'Categorías sin utilizar',
                'value' => $categories->filter(fn ($category) => ($category->products_count ?? 0) === 0)->count(),
                'sub' => 'Sin productos asociados',
                'path' => 'M21 7.5l-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9',
            ],
            [
                'label' => 'Categorías madre',
                'value' => $categories->filter(fn ($category) => ($category->children_count ?? 0) > 0)->count(),
                'sub' => 'Con subcategorías',
                'path' => 'M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z',
            ],
        ];
    ?>

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

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:max-w-sm">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400 dark:text-neutral-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" class="stroke-current" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input
                type="search"
                id="category-search"
                placeholder="Buscar categoría..."
                autocomplete="off"
                class="w-full rounded-xl border border-neutral-300 bg-white py-2.5 pl-10 pr-4 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
            />
        </div>

        <a
            href="<?php echo e(route('categories.create')); ?>"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 focus-visible:ring-offset-2"
        >
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            Nueva categoría
        </a>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 text-sm dark:divide-neutral-800">
                <thead class="bg-brand-50/60 dark:bg-neutral-800/60">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                    <th class="px-4 py-3 sm:px-6">ID</th>
                    <th class="px-4 py-3 sm:px-6">Nombre</th>
                    <th class="hidden px-4 py-3 sm:px-6 md:table-cell">Categoría padre</th>
                    <th class="px-4 py-3 sm:px-6">Productos</th>
                    <th class="px-4 py-3 sm:px-6">Estado</th>
                    <th class="hidden px-4 py-3 sm:px-6 lg:table-cell">Creada</th>
                    <th class="px-4 py-3 text-right sm:px-6">Acciones</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr
                        data-category-row
                        data-category-search="<?php echo e(strtolower(trim($category->name . ' ' . ($category->parent?->name ?? '') . ' ' . ($category->description ?? '')))); ?>"
                        class="<?php echo e($category->is_active ? 'hover:bg-brand-50/40 dark:hover:bg-neutral-800/50' : 'bg-neutral-50 hover:bg-neutral-100/60 dark:bg-neutral-800/30 dark:hover:bg-neutral-800/60'); ?>"
                    >
                        <td class="whitespace-nowrap px-4 py-3 text-neutral-500 dark:text-neutral-400 sm:px-6">#<?php echo e($category->id); ?></td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                            <p class="<?php echo e($category->is_active ? 'font-medium text-neutral-900 dark:text-white' : 'font-medium text-neutral-400 dark:text-neutral-500'); ?>"><?php echo e($category->name); ?></p>
                            <?php if($category->description): ?>
                                <p class="mt-0.5 max-w-xs truncate text-xs text-neutral-400 dark:text-neutral-500"><?php echo e($category->description); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="hidden whitespace-nowrap px-4 py-3 text-neutral-600 dark:text-neutral-400 sm:px-6 md:table-cell">
                            <?php echo e($category->parent?->name ?? '—'); ?>

                        </td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                            <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                                <?php echo e($category->products_count ?? 0); ?>

                            </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                            <?php if($category->is_active): ?>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                    Activa
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-neutral-400"></span>
                                    Inactiva
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="hidden whitespace-nowrap px-4 py-3 text-neutral-500 dark:text-neutral-400 sm:px-6 lg:table-cell">
                            <?php echo e($category->created_at->format('d/m/Y')); ?>

                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right sm:px-6">
                            <div class="flex items-center justify-end gap-1.5">
                                <?php if(Route::has('categories.show')): ?>
                                    <button
                                        type="button"
                                        title="Ver detalle"
                                        data-view-category="<?php echo e(route('categories.show', $category)); ?>"
                                        class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-brand-700 transition-all hover:scale-110 hover:bg-brand-100 hover:shadow-sm dark:text-brand-400 dark:hover:bg-brand-900/40"
                                    >
                                        <i class="fa-solid fa-eye text-sm" aria-hidden="true"></i>
                                    </button>
                                <?php endif; ?>

                                <?php if(Route::has('categories.edit')): ?>
                                    <a
                                        href="<?php echo e(route('categories.edit', $category)); ?>"
                                        title="Editar"
                                        class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-blue-600 transition-all hover:scale-110 hover:bg-blue-100 hover:shadow-sm dark:text-blue-400 dark:hover:bg-blue-900/40"
                                    >
                                        <i class="fa-solid fa-pen-to-square text-sm" aria-hidden="true"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if($category->is_active && Route::has('categories.toggle')): ?>
                                    <form method="POST" action="<?php echo e(route('categories.toggle', $category)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button
                                            type="submit"
                                            data-disable-category
                                            title="Deshabilitar"
                                            class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-amber-600 transition-all hover:scale-110 hover:bg-amber-100 hover:shadow-sm dark:text-amber-400 dark:hover:bg-amber-900/40"
                                        >
                                            <i class="fa-solid fa-ban text-sm" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if(! $category->is_active && Route::has('categories.destroy')): ?>
                                    <form method="POST" action="<?php echo e(route('categories.destroy', $category)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button
                                            type="submit"
                                            data-delete-category
                                            title="Eliminar"
                                            class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-red-600 transition-all hover:scale-110 hover:bg-red-100 hover:shadow-sm dark:text-red-400 dark:hover:bg-red-900/40"
                                        >
                                            <i class="fa-solid fa-trash text-sm" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center sm:px-6">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" class="stroke-current" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-medium text-neutral-500 dark:text-neutral-400">No hay categorías registradas</p>
                            <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">Crea tu primera categoría para empezar.</p>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr id="no-results-row" class="hidden">
                    <td colspan="7" class="px-4 py-12 text-center sm:px-6">
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Sin resultados</p>
                        <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">No se encontraron categorías para la búsqueda.</p>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="category-modal-container" aria-hidden="true"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('category-search');
            const rows = document.querySelectorAll('[data-category-row]');
            const noResultsRow = document.getElementById('no-results-row');

            searchInput.addEventListener('input', () => {
                const term = searchInput.value.trim().toLowerCase();
                let visible = 0;

                if (rows.length === 0) {
                    noResultsRow.classList.add('hidden');
                    return;
                }

                rows.forEach((row) => {
                    const show = row.dataset.categorySearch.includes(term);
                    row.classList.toggle('hidden', !show);
                    if (show) {
                        visible++;
                    }
                });

                noResultsRow.classList.toggle('hidden', visible > 0);
            });

            document.querySelectorAll('[data-disable-category]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    const form = button.closest('form');

                    Swal.fire({
                        title: '¿Deshabilitar categoría?',
                        text: 'La categoría quedará inactiva. Puedes volver a activarla desde la edición.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, deshabilitar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#d97706',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            document.querySelectorAll('[data-delete-category]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    const form = button.closest('form');

                    Swal.fire({
                        title: '¿Eliminar categoría?',
                        text: 'Esta acción eliminará la categoría de forma permanente.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#dc2626',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            const modalContainer = document.getElementById('category-modal-container');
            const modalTrigger = { element: null };

            const closeCategoryModal = () => {
                modalContainer.innerHTML = '';
                modalContainer.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
                modalTrigger.element?.focus();
            };

            document.querySelectorAll('[data-view-category]').forEach((button) => {
                button.addEventListener('click', async () => {
                    modalTrigger.element = document.activeElement;
                    modalContainer.innerHTML = '';

                    try {
                        const response = await fetch(button.dataset.viewCategory, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html',
                            },
                        });

                        if (!response.ok) {
                            return;
                        }

                        modalContainer.innerHTML = await response.text();

                        const modal = modalContainer.querySelector('[data-category-modal]');
                        if (!modal) {
                            closeCategoryModal();
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
                                closeCategoryModal();
                            }
                        });

                        modal.addEventListener('keydown', (event) => {
                            if (event.key === 'Escape') {
                                closeCategoryModal();
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
                            element.addEventListener('click', closeCategoryModal);
                        });
                    } catch (error) {
                        closeCategoryModal();
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views/modules/categories/index.blade.php ENDPATH**/ ?>