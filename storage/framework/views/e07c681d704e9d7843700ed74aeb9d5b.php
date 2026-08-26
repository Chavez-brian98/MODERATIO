<div
    data-customer-modal
    role="dialog"
    aria-modal="true"
    aria-labelledby="customer-modal-title"
    class="fixed inset-0 z-[70] flex items-end justify-center sm:items-center sm:p-6"
>
    <div
        data-modal-backdrop
        class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm animate-[modal-backdrop-in_200ms_ease-out]"
    ></div>

    <div
        class="relative flex max-h-[88vh] w-full flex-col overflow-hidden border border-brand-200 bg-white shadow-2xl animate-[modal-panel-in_250ms_ease-out] dark:border-neutral-700 dark:bg-neutral-900 sm:max-w-lg sm:rounded-2xl rounded-t-2xl"
    >
        <header class="flex items-center justify-between gap-4 border-b border-brand-100 px-6 py-4 dark:border-neutral-800">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                    <i class="fa-solid fa-user" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <h2 id="customer-modal-title" class="truncate text-lg font-semibold text-neutral-900 dark:text-white">
                        <?php echo e($customer->first_name); ?> <?php echo e($customer->last_name); ?>

                    </h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Cliente #<?php echo e($customer->id); ?></p>
                </div>
            </div>

            <button
                type="button"
                data-modal-close
                aria-label="Cerrar detalle"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-neutral-500 transition-colors hover:bg-brand-100 hover:text-brand-800 dark:text-neutral-400 dark:hover:bg-neutral-800 dark:hover:text-white"
            >
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </header>

        <div class="overflow-y-auto px-6 py-5">
            <dl class="divide-y divide-neutral-100 dark:divide-neutral-800">
                <div class="flex items-start justify-between gap-6 py-3.5">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">NIT / DUI</dt>
                    <dd class="text-right text-sm text-neutral-900 dark:text-white"><?php echo e($customer->tax_id ?? 'Sin documento'); ?></dd>
                </div>

                <div class="flex items-start justify-between gap-6 py-3.5">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Teléfono</dt>
                    <dd class="text-right text-sm text-neutral-900 dark:text-white"><?php echo e($customer->phone ?? 'Sin teléfono'); ?></dd>
                </div>

                <div class="flex items-start justify-between gap-6 py-3.5">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Correo</dt>
                    <dd class="text-right text-sm break-all text-neutral-900 dark:text-white"><?php echo e($customer->email ?? 'Sin correo'); ?></dd>
                </div>

                <div class="flex items-start justify-between gap-6 py-3.5">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Dirección</dt>
                    <dd class="text-right text-sm text-neutral-900 dark:text-white"><?php echo e($customer->address ?: 'Sin dirección'); ?></dd>
                </div>

                <div class="flex items-center justify-between gap-6 py-3.5">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Tipo de cliente</dt>
                    <dd>
                        <?php
                            $typeLabels = ['REGULAR' => 'Regular', 'FREQUENT' => 'Frecuente', 'WHOLESALER' => 'Mayorista'];
                        ?>
                        <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                            <?php echo e($typeLabels[$customer->customer_type] ?? $customer->customer_type); ?>

                        </span>
                    </dd>
                </div>

                <div class="flex items-center justify-between gap-6 py-3.5">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Compras registradas</dt>
                    <dd>
                        <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                            <?php echo e($customer->sales_count); ?>

                        </span>
                    </dd>
                </div>

                <div class="flex items-center justify-between gap-6 py-3.5">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Estado</dt>
                    <dd>
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
                    </dd>
                </div>
            </dl>
        </div>

        <footer class="flex flex-col-reverse gap-3 border-t border-brand-100 px-6 py-4 dark:border-neutral-800 sm:flex-row sm:justify-end">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('customers_edit')): ?>
                <a
                    href="<?php echo e(route('customers.edit', $customer)); ?>"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-800"
                >
                    <i class="fa-solid fa-pen-to-square text-sm" aria-hidden="true"></i>
                    Editar cliente
                </a>
            <?php endif; ?>

            <button
                type="button"
                data-modal-close
                class="inline-flex items-center justify-center rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
            >
                Cerrar
            </button>
        </footer>
    </div>
</div>
<?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views\modules\customers\show.blade.php ENDPATH**/ ?>