<?php $__env->startSection('title', 'Caja / Arqueo'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $shiftLabels = [
            'MORNING' => 'Mañana',
            'AFTERNOON' => 'Tarde',
            'NIGHT' => 'Noche',
        ];
    ?>

    <?php echo $__env->make('partials.breadcrumbs', ['crumbs' => [['label' => 'Caja / Arqueo']]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Caja / Arqueo</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Gestiona las sesiones de caja y el arqueo diario.</p>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <i class="fa-solid fa-lock-open text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Abiertas</p>
                    <p class="text-2xl font-bold text-neutral-800 dark:text-neutral-100"><?php echo e($stats['open_count']); ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <i class="fa-solid fa-calendar-check text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Hoy abiertas</p>
                    <p class="text-2xl font-bold text-neutral-800 dark:text-neutral-100"><?php echo e($stats['opened_today']); ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                    <i class="fa-solid fa-lock text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Cerradas hoy</p>
                    <p class="text-2xl font-bold text-neutral-800 dark:text-neutral-100"><?php echo e($stats['closed_today']); ?></p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                    <i class="fa-solid fa-dollar-sign text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Ventas hoy</p>
                    <p class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">$<?php echo e(number_format($stats['sales_today'], 2)); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 sm:max-w-xs">
            <i class="fa-solid fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400"></i>
            <input
                type="text"
                id="cash-register-search"
                placeholder="Buscar por ID, turno o empleado..."
                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 pl-10 pr-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:placeholder:text-neutral-500"
            />
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cash_registers_create')): ?>
            <a href="<?php echo e(route('cash-register.create')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700 active:bg-brand-800">
                <i class="fa-solid fa-plus text-xs"></i> Abrir Caja
            </a>
        <?php endif; ?>
    </div>

    <div class="overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" role="grid">
                <thead>
                    <tr class="border-b border-brand-100 bg-brand-50/60 dark:border-neutral-700 dark:bg-neutral-800/60">
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">ID</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Turno</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 sm:table-cell dark:text-brand-200">Abierta por</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 md:table-cell dark:text-brand-200">Encargado</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 md:table-cell dark:text-brand-200">Apertura</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 lg:table-cell dark:text-brand-200">Creada</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 lg:table-cell dark:text-brand-200">Ventas</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 xl:table-cell dark:text-brand-200">Total Ventas</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Estado</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Acciones</th>
                    </tr>
                </thead>
                <tbody id="cash-register-tbody" class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    <?php $__empty_1 = true; $__currentLoopData = $registers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $register): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="transition-colors hover:bg-brand-50/40 dark:hover:bg-neutral-800/40" data-search="<?php echo e(strtolower($register->id . ' ' . ($shiftLabels[$register->shift] ?? $register->shift ?? '') . ' ' . $register->user->full_name . ' ' . ($register->responsible?->full_name ?? ''))); ?>">
                            <td class="px-4 py-3 font-medium text-neutral-800 dark:text-neutral-200">#<?php echo e($register->id); ?></td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400"><?php echo e($shiftLabels[$register->shift] ?? $register->shift ?? '—'); ?></td>
                            <td class="hidden px-4 py-3 sm:table-cell dark:text-neutral-300"><?php echo e($register->user->full_name); ?></td>
                            <td class="hidden px-4 py-3 md:table-cell dark:text-neutral-300"><?php echo e($register->responsible?->full_name ?? '—'); ?></td>
                            <td class="hidden px-4 py-3 md:table-cell dark:text-neutral-300">$<?php echo e(number_format($register->opening_amount, 2)); ?></td>
                            <td class="hidden px-4 py-3 whitespace-nowrap lg:table-cell dark:text-neutral-300"><?php echo e($register->created_at?->format('d/m/Y H:i') ?? '—'); ?></td>
                            <td class="hidden px-4 py-3 lg:table-cell dark:text-neutral-300"><?php echo e($register->sales_count); ?></td>
                            <td class="hidden px-4 py-3 lg:table-cell dark:text-neutral-300">$<?php echo e(number_format($register->sales_sum_total ?? 0, 2)); ?></td>
                            <td class="px-4 py-3">
                                <?php if($register->status === 'OPEN'): ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <i class="fa-solid fa-circle text-[6px]"></i> Abierta
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                                        <i class="fa-solid fa-circle text-[6px]"></i> Cerrada
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="<?php echo e(route('cash-register.show', $register)); ?>" title="Ver detalle" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-brand-700 transition-all hover:scale-110 hover:bg-brand-100 hover:shadow-sm dark:text-brand-400 dark:hover:bg-brand-900/40">
                                        <i class="fa-solid fa-eye text-sm"></i>
                                    </a>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cash_registers_edit')): ?>
                                        <a href="<?php echo e(route('cash-register.edit', $register)); ?>" title="Editar caja (corregir errores de captura)" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-blue-600 transition-all hover:scale-110 hover:bg-blue-100 hover:shadow-sm dark:text-blue-400 dark:hover:bg-blue-900/40">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-100 text-brand-400 dark:bg-brand-900/30 dark:text-brand-500">
                                        <i class="fa-solid fa-cash-register text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-neutral-700 dark:text-neutral-300">No hay sesiones de caja</p>
                                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Abre una nueva caja para comenzar.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php echo $__env->make('partials.pagination', ['paginator' => $registers], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        document.getElementById('cash-register-search')?.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            document.querySelectorAll('#cash-register-tbody tr[data-search]').forEach(function (row) {
                row.style.display = row.dataset.search.includes(query) ? '' : 'none';
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views\modules\cash-registers\index.blade.php ENDPATH**/ ?>