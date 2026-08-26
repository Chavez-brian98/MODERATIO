<?php $__env->startSection('title', 'Caja #' . $register->id); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $shiftLabels = [
            'MORNING' => 'Mañana',
            'AFTERNOON' => 'Tarde',
            'NIGHT' => 'Noche',
        ];
    ?>

    <?php echo $__env->make('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Caja / Arqueo', 'url' => route('cash-register.index')],
        ['label' => 'Caja #' . $register->id],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Caja #<?php echo e($register->id); ?></h1>
            <p class="mt-1 truncate text-sm text-neutral-500 dark:text-neutral-400">
                <?php echo e($register->shift ? 'Turno: ' . ($shiftLabels[$register->shift] ?? $register->shift) . ' — ' : ''); ?><?php echo e($register->user->full_name); ?>

            </p>
        </div>
        <div class="flex items-center gap-3">
            <?php if($register->status === 'OPEN'): ?>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <i class="fa-solid fa-circle text-[6px]"></i> Abierta
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                    <i class="fa-solid fa-circle text-[6px]"></i> Cerrada
                </span>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cash_registers_edit')): ?>
                <a href="<?php echo e(route('cash-register.edit', $register)); ?>" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800" title="Editar caja (corregir errores de captura)">
                    <i class="fa-solid fa-pen-to-square text-xs"></i> Editar
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('cash-register.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
                <i class="fa-solid fa-arrow-left text-xs"></i> Volver
            </a>
        </div>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Apertura</p>
            <p class="mt-1 text-2xl font-bold text-neutral-800 dark:text-neutral-100">$<?php echo e(number_format($register->opening_amount, 2)); ?></p>
            <p class="mt-0.5 text-xs text-neutral-400 dark:text-neutral-500"><?php echo e($register->opening_date->format('d/m/Y H:i')); ?></p>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Total Ventas</p>
            <p class="mt-1 text-2xl font-bold text-neutral-800 dark:text-neutral-100">$<?php echo e(number_format($arqueo['total_sales'], 2)); ?></p>
            <p class="mt-0.5 text-xs text-neutral-400 dark:text-neutral-500"><?php echo e($arqueo['sales_count']); ?> transacciones</p>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Efectivo</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">$<?php echo e(number_format($arqueo['cash_sales'], 2)); ?></p>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Teórico en Caja</p>
            <p class="mt-1 text-2xl font-bold text-brand-600 dark:text-brand-400">$<?php echo e(number_format($arqueo['theoretical_amount'], 2)); ?></p>
            <p class="mt-0.5 text-xs text-neutral-400 dark:text-neutral-500">Apertura + Ventas efectivo</p>
        </div>
    </div>

    <div class="mb-8 grid items-start gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900 lg:col-span-2">
            <div class="mb-4 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Información de la caja</span>
            </div>

            <dl class="divide-y divide-neutral-100 rounded-xl border border-neutral-100 dark:divide-neutral-800 dark:border-neutral-800">
                <div class="flex items-center justify-between gap-6 px-4 py-3">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Abierta por</dt>
                    <dd class="text-right text-sm font-medium text-neutral-900 dark:text-white"><?php echo e($register->user->full_name); ?></dd>
                </div>
                <div class="flex items-center justify-between gap-6 px-4 py-3">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Encargado</dt>
                    <dd class="text-right text-sm font-medium text-neutral-900 dark:text-white">
                        <?php if($register->responsible): ?>
                            <?php echo e($register->responsible->full_name); ?>

                        <?php else: ?>
                            <span class="text-neutral-400 dark:text-neutral-500">Sin encargado asignado</span>
                        <?php endif; ?>
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-6 px-4 py-3">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Fecha de creación</dt>
                    <dd class="text-right text-sm font-medium whitespace-nowrap text-neutral-900 dark:text-white"><?php echo e($register->created_at?->format('d/m/Y \a \l\a\s H:i') ?? '—'); ?></dd>
                </div>
                <div class="flex items-center justify-between gap-6 px-4 py-3">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Última actualización</dt>
                    <dd class="text-right text-sm font-medium whitespace-nowrap text-neutral-900 dark:text-white"><?php echo e($register->updated_at?->format('d/m/Y \a \l\a\s H:i') ?? '—'); ?></dd>
                </div>
                <?php if($register->closing_date): ?>
                    <div class="flex items-center justify-between gap-6 px-4 py-3">
                        <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Fecha de cierre</dt>
                        <dd class="text-right text-sm font-medium whitespace-nowrap text-neutral-900 dark:text-white"><?php echo e($register->closing_date->format('d/m/Y \a \l\a\s H:i')); ?></dd>
                    </div>
                <?php endif; ?>
            </dl>
        </div>

        <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
            <div class="rounded-2xl border border-brand-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Efectivo</p>
                        <p class="text-lg font-bold text-neutral-800 dark:text-neutral-100">$<?php echo e(number_format($arqueo['cash_sales'], 2)); ?></p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-brand-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <i class="fa-solid fa-credit-card"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Tarjeta</p>
                        <p class="text-lg font-bold text-neutral-800 dark:text-neutral-100">$<?php echo e(number_format($arqueo['card_sales'], 2)); ?></p>
                    </div>
                </div>
            </div>
            <div class="rounded-2xl border border-brand-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Transferencia</p>
                        <p class="text-lg font-bold text-neutral-800 dark:text-neutral-100">$<?php echo e(number_format($arqueo['transfer_sales'], 2)); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if($register->status === 'OPEN'): ?>
        <div class="mx-auto mb-8 max-w-2xl rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm dark:border-amber-800 dark:bg-amber-950/30">
            <div class="mb-4 flex items-center gap-3 border-b border-amber-200 pb-4 dark:border-amber-800">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-200 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <span class="text-sm font-semibold text-amber-800 dark:text-amber-200">Cerrar Caja</span>
            </div>

            <form method="POST" action="<?php echo e(route('cash-register.close', $register)); ?>" id="close-form">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>

                <div class="mb-4 space-y-1.5 rounded-xl bg-white p-4 text-sm dark:bg-neutral-900">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-neutral-500 dark:text-neutral-400">Apertura</span>
                        <span class="font-medium text-neutral-800 dark:text-neutral-200">$<?php echo e(number_format($register->opening_amount, 2)); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-neutral-500 dark:text-neutral-400">+ Ventas en efectivo</span>
                        <span class="font-medium text-neutral-800 dark:text-neutral-200">$<?php echo e(number_format($arqueo['cash_sales'], 2)); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-t border-neutral-100 pt-1.5 dark:border-neutral-700">
                        <span class="font-semibold text-neutral-700 dark:text-neutral-300">= Teórico en efectivo</span>
                        <span class="text-lg font-bold text-brand-600 dark:text-brand-400">$<?php echo e(number_format($arqueo['theoretical_amount'], 2)); ?></span>
                    </div>
                    <p class="mt-2 text-xs text-neutral-400 dark:text-neutral-500">Las ventas con tarjeta o transferencia no ingresan al cajón, por eso no cuentan para el cuadre.</p>
                </div>

                <div class="mb-5">
                    <label for="actual_closing_amount" class="mb-1.5 block text-sm font-medium text-amber-800 dark:text-amber-200">Monto Real en Efectivo <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">$</span>
                        <input
                            type="number"
                            name="actual_closing_amount"
                            id="actual_closing_amount"
                            value="<?php echo e(old('actual_closing_amount')); ?>"
                            placeholder="<?php echo e(number_format($arqueo['theoretical_amount'], 2, '.', '')); ?>"
                            step="0.01"
                            min="0"
                            required
                            class="w-full rounded-xl border border-amber-300 bg-white py-2.5 pl-8 pr-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-amber-700 dark:bg-neutral-800 dark:text-neutral-200"
                        />
                    </div>
                    <?php $__errorArgs = ['actual_closing_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <div id="difference-display" class="mt-2 text-sm font-medium"></div>
                </div>

                <div class="mb-5">
                    <label for="closing_notes" class="mb-1.5 block text-sm font-medium text-amber-800 dark:text-amber-200">Descripción / Observaciones</label>
                    <textarea
                        name="closing_notes"
                        id="closing_notes"
                        rows="3"
                        maxlength="500"
                        placeholder="Describe cualquier inconveniente: sobrante/faltante justificado, gasto de caja, retiro de efectivo, etc. (opcional)"
                        class="w-full rounded-xl border border-amber-300 bg-white px-4 py-2.5 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-amber-700 dark:bg-neutral-800 dark:text-neutral-200 dark:placeholder:text-neutral-500"
                    ><?php echo e(old('closing_notes')); ?></textarea>
                    <?php $__errorArgs = ['closing_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-amber-700 active:bg-amber-800">
                    <i class="fa-solid fa-lock text-xs"></i> Cerrar Caja
                </button>
            </form>
        </div>
    <?php elseif($register->actual_closing_amount !== null): ?>
        <?php
            $closingDifference = (float) $register->difference;
            $isBalanced = abs($closingDifference) < 0.005;
        ?>
        <div class="mx-auto mb-8 max-w-2xl rounded-2xl border p-6 shadow-sm <?php echo e($isBalanced ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-950/30' : ($closingDifference > 0 ? 'border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-950/30' : 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950/30')); ?>">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-4 border-b pb-4 <?php echo e($isBalanced ? 'border-emerald-200 dark:border-emerald-800' : ($closingDifference > 0 ? 'border-blue-200 dark:border-blue-800' : 'border-red-200 dark:border-red-800')); ?>">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl <?php echo e($isBalanced ? 'bg-emerald-200 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-400' : ($closingDifference > 0 ? 'bg-blue-200 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400' : 'bg-red-200 text-red-700 dark:bg-red-900/50 dark:text-red-400')); ?>">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </div>
                    <span class="text-sm font-semibold text-neutral-800 dark:text-neutral-100">Resumen del cierre</span>
                </div>
                <?php if($isBalanced): ?>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-600 px-3 py-1 text-xs font-semibold text-white">
                        <i class="fa-solid fa-check"></i> Caja cuadrada
                    </span>
                <?php elseif($closingDifference > 0): ?>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-600 px-3 py-1 text-xs font-semibold text-white">
                        Sobrante
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-600 px-3 py-1 text-xs font-semibold text-white">
                        Faltante
                    </span>
                <?php endif; ?>
            </div>

            <dl class="divide-y divide-neutral-200/70 rounded-xl bg-white p-4 text-sm dark:divide-neutral-700 dark:bg-neutral-900">
                <div class="flex items-center justify-between gap-4 px-1 py-1.5">
                    <dt class="text-neutral-500 dark:text-neutral-400">Teórico en efectivo</dt>
                    <dd class="font-medium text-neutral-800 dark:text-neutral-200">$<?php echo e(number_format($register->theoretical_closing_amount, 2)); ?></dd>
                </div>
                <div class="flex items-center justify-between gap-4 px-1 py-1.5">
                    <dt class="text-neutral-500 dark:text-neutral-400">Contado al cerrar</dt>
                    <dd class="font-medium text-neutral-800 dark:text-neutral-200">$<?php echo e(number_format($register->actual_closing_amount, 2)); ?></dd>
                </div>
                <div class="flex items-center justify-between gap-4 px-1 pt-2">
                    <dt class="font-semibold text-neutral-700 dark:text-neutral-300">Diferencia</dt>
                    <dd class="<?php echo e($isBalanced ? 'text-emerald-600 dark:text-emerald-400' : ($closingDifference > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400')); ?> text-base font-bold">
                        <?php echo e($isBalanced ? '$0.00' : (($closingDifference > 0 ? '+' : '-') . '$' . number_format(abs($closingDifference), 2))); ?>

                    </dd>
                </div>
            </dl>

            <?php if(filled($register->closing_notes)): ?>
                <div class="mt-3 rounded-xl border border-neutral-200/80 bg-white p-3 dark:border-neutral-700 dark:bg-neutral-900">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                        <i class="fa-solid fa-note-sticky mr-1 text-amber-500"></i> Observaciones del cierre
                    </p>
                    <p class="mt-1 whitespace-pre-line text-sm text-neutral-700 dark:text-neutral-300"><?php echo e($register->closing_notes); ?></p>
                </div>
            <?php endif; ?>

            <?php if($register->closing_date): ?>
                <p class="mt-3 text-xs <?php echo e($isBalanced ? 'text-emerald-700/80 dark:text-emerald-400/80' : ($closingDifference > 0 ? 'text-blue-700/80 dark:text-blue-400/80' : 'text-red-700/80 dark:text-red-400/80')); ?>">
                    Cerrada el <?php echo e($register->closing_date->format('d/m/Y \a \l\a\s H:i')); ?>.
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-brand-100 px-5 py-4 dark:border-neutral-700">
            <h2 class="flex items-center gap-2 text-sm font-semibold text-brand-800 dark:text-brand-200">
                <i class="fa-solid fa-receipt text-brand-500"></i> Historial de ventas
            </h2>
            <p class="hidden text-xs text-neutral-400 dark:text-neutral-500 sm:block">Toca una venta para ver los productos vendidos.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-100 bg-brand-50/60 dark:border-neutral-700 dark:bg-neutral-800/60">
                        <th class="w-10 px-2 py-3"></th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Ticket</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 sm:table-cell dark:text-brand-200">Fecha</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 md:table-cell dark:text-brand-200">Cliente</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Método</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    <?php $__empty_1 = true; $__currentLoopData = $arqueo['sales']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="transition-colors hover:bg-brand-50/40 dark:hover:bg-neutral-800/40">
                            <td class="px-2 py-3">
                                <button
                                    type="button"
                                    data-sale-toggle="<?php echo e($sale->id); ?>"
                                    aria-expanded="false"
                                    title="Ver productos vendidos"
                                    class="flex h-7 w-7 items-center justify-center rounded-lg text-neutral-400 transition-colors hover:bg-brand-100 hover:text-brand-700 dark:hover:bg-neutral-700 dark:hover:text-brand-300"
                                >
                                    <i class="fa-solid fa-chevron-right text-xs transition-transform" data-sale-icon="<?php echo e($sale->id); ?>"></i>
                                    <span class="sr-only">Ver productos de <?php echo e($sale->ticket_number); ?></span>
                                </button>
                            </td>
                            <td class="px-4 py-3 font-medium whitespace-nowrap text-neutral-800 dark:text-neutral-200"><?php echo e($sale->ticket_number); ?></td>
                            <td class="hidden px-4 py-3 whitespace-nowrap sm:table-cell dark:text-neutral-300">
                                <?php echo e($sale->created_at->format('d/m/Y')); ?>

                                <span class="text-xs text-neutral-400 dark:text-neutral-500"><?php echo e($sale->created_at->format('H:i:s')); ?></span>
                            </td>
                            <td class="hidden px-4 py-3 md:table-cell dark:text-neutral-300"><?php echo e($sale->customer?->full_name ?? '—'); ?></td>
                            <td class="px-4 py-3">
                                <?php if($sale->payment_method === 'CASH'): ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <i class="fa-solid fa-money-bill-wave text-[8px]"></i> Efectivo
                                    </span>
                                <?php elseif($sale->payment_method === 'CARD'): ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        <i class="fa-solid fa-credit-card text-[8px]"></i> Tarjeta
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-violet-100 px-2 py-0.5 text-xs font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-400">
                                        <i class="fa-solid fa-building-columns text-[8px]"></i> Transferencia
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 font-semibold whitespace-nowrap text-neutral-800 dark:text-neutral-100">$<?php echo e(number_format($sale->total, 2)); ?></td>
                        </tr>
                        <tr data-sale-details="<?php echo e($sale->id); ?>" class="hidden">
                            <td colspan="6" class="border-l-2 border-brand-300 bg-brand-50/50 px-4 py-4 dark:border-brand-700 dark:bg-neutral-800/40 sm:px-8">
                                <?php if($sale->details->isEmpty()): ?>
                                    <p class="text-xs text-neutral-400 dark:text-neutral-500">Sin productos registrados.</p>
                                <?php else: ?>
                                    <p class="mb-2.5 text-[11px] font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Productos vendidos</p>
                                    <ul class="grid gap-2 lg:grid-cols-2">
                                        <?php $__currentLoopData = $sale->details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="flex items-center justify-between gap-3 rounded-lg border border-neutral-200/80 bg-white px-3 py-2 text-xs shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                                                <span class="min-w-0 truncate font-medium text-neutral-800 dark:text-neutral-200"><?php echo e($detail->product?->name ?? 'Producto eliminado'); ?></span>
                                                <span class="shrink-0 text-neutral-400 dark:text-neutral-500"><?php echo e($detail->quantity); ?> × $<?php echo e(number_format($detail->unit_price, 2)); ?></span>
                                                <span class="shrink-0 font-semibold text-neutral-800 dark:text-neutral-100">$<?php echo e(number_format($detail->subtotal, 2)); ?></span>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-100 text-brand-400 dark:bg-brand-900/30 dark:text-brand-500">
                                        <i class="fa-solid fa-receipt text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-neutral-700 dark:text-neutral-300">Sin transacciones</p>
                                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Aún no se han registrado ventas en esta caja.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        document.getElementById('close-form')?.addEventListener('submit', function (event) {
            if (this.dataset.swalConfirmed === 'true') {
                return;
            }

            event.preventDefault();

            const amount = parseFloat(document.getElementById('actual_closing_amount').value) || 0;
            const theoretical = <?php echo e($arqueo['theoretical_amount']); ?>;
            const diff = amount - theoretical;
            const msg = diff === 0
                ? 'El monto coincide con el teórico.'
                : diff > 0
                    ? `Sobrante de $${diff.toFixed(2)} respecto al teórico.`
                    : `Faltante de $${Math.abs(diff).toFixed(2)} respecto al teórico.`;

            Swal.fire({
                title: '¿Cerrar caja?',
                text: msg,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar caja',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: window.SwalColors.brand,
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                this.dataset.swalConfirmed = 'true';
                this.submit();
            });
        });

        document.getElementById('actual_closing_amount')?.addEventListener('input', function () {
            const el = document.getElementById('difference-display');
            if (this.value === '') {
                el.textContent = '';
                el.className = 'mt-2 text-sm font-medium';
                return;
            }
            const amount = parseFloat(this.value) || 0;
            const theoretical = <?php echo e($arqueo['theoretical_amount']); ?>;
            const diff = amount - theoretical;
            if (diff === 0) {
                el.textContent = 'Coincide con el monto teórico';
                el.className = 'mt-2 text-sm font-medium text-emerald-600 dark:text-emerald-400';
            } else if (diff > 0) {
                el.textContent = `Sobrante: $${diff.toFixed(2)}`;
                el.className = 'mt-2 text-sm font-medium text-emerald-600 dark:text-emerald-400';
            } else {
                el.textContent = `Faltante: $${Math.abs(diff).toFixed(2)}`;
                el.className = 'mt-2 text-sm font-medium text-red-600 dark:text-red-400';
            }
        });

        document.querySelectorAll('[data-sale-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const saleId = button.dataset.saleToggle;
                const detailsRow = document.querySelector(`[data-sale-details="${saleId}"]`);
                const icon = document.querySelector(`[data-sale-icon="${saleId}"]`);
                const isOpen = !detailsRow.classList.toggle('hidden');

                icon.classList.toggle('rotate-90', isOpen);
                button.setAttribute('aria-expanded', String(isOpen));
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views/modules/cash-registers/show.blade.php ENDPATH**/ ?>