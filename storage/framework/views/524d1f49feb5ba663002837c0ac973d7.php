<?php $__env->startSection('title', 'Editar Caja #' . $register->id); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $isClosed = $register->status === 'CLOSED';
    ?>

    <?php echo $__env->make('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Caja / Arqueo', 'url' => route('cash-register.index')],
        ['label' => 'Caja #' . $register->id, 'url' => route('cash-register.show', $register)],
        ['label' => 'Editar'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Editar Caja #<?php echo e($register->id); ?></h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Corrige errores de captura en los datos de la caja.</p>
        </div>
        <div class="flex items-center gap-3">
            <?php if($isClosed): ?>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                    <i class="fa-solid fa-circle text-[6px]"></i> Cerrada
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <i class="fa-solid fa-circle text-[6px]"></i> Abierta
                </span>
            <?php endif; ?>
            <a href="<?php echo e(route('cash-register.show', $register)); ?>" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
                <i class="fa-solid fa-arrow-left text-xs"></i> Volver
            </a>
        </div>
    </div>

    <div class="mx-auto max-w-2xl">
        <form method="POST" action="<?php echo e(route('cash-register.update', $register)); ?>" class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl <?php echo e($isClosed ? 'bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400' : 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400'); ?>">
                    <i class="<?php echo e($isClosed ? 'fa-solid fa-pen-to-square' : 'fa-solid fa-lock-open'); ?>"></i>
                </div>
                <span class="text-sm font-semibold text-brand-800 dark:text-brand-200"><?php echo e($isClosed ? 'Datos del cierre' : 'Datos de la caja abierta'); ?></span>
            </div>

            <?php if($isClosed): ?>
                <div class="mb-6 space-y-1.5 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm dark:border-blue-800 dark:bg-blue-950/30">
                    <p class="font-medium text-blue-800 dark:text-blue-300"><i class="fa-solid fa-circle-info mr-1.5"></i>Esta caja ya está cerrada</p>
                    <p class="text-xs leading-relaxed text-blue-700/80 dark:text-blue-400/80">
                        Al guardar, el monto teórico se recalcula con el nuevo monto de apertura y las ventas registradas (apertura + ventas en efectivo), y la diferencia se actualiza automáticamente.
                    </p>
                </div>
            <?php endif; ?>

            <div class="space-y-5">
                <div>
                    <label for="opening_amount" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Monto de Apertura <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">$</span>
                        <input
                            type="number"
                            name="opening_amount"
                            id="opening_amount"
                            value="<?php echo e(old('opening_amount', $register->opening_amount)); ?>"
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                            required
                            class="w-full rounded-xl border border-brand-200 bg-white py-2.5 pl-8 pr-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                        />
                    </div>
                    <?php $__errorArgs = ['opening_amount'];
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

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="shift" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Turno</label>
                        <select
                            name="shift"
                            id="shift"
                            class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                        >
                            <option value="">Sin turno asignado</option>
                            <option value="MORNING" <?php if(old('shift', $register->shift) === 'MORNING'): echo 'selected'; endif; ?>>Mañana</option>
                            <option value="AFTERNOON" <?php if(old('shift', $register->shift) === 'AFTERNOON'): echo 'selected'; endif; ?>>Tarde</option>
                            <option value="NIGHT" <?php if(old('shift', $register->shift) === 'NIGHT'): echo 'selected'; endif; ?>>Noche</option>
                        </select>
                        <?php $__errorArgs = ['shift'];
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

                    <div>
                        <label for="responsible_id" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Encargado de caja</label>
                        <select
                            name="responsible_id"
                            id="responsible_id"
                            class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                        >
                            <option value="">Sin encargado asignado</option>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($employee->id); ?>" <?php if((string) old('responsible_id', $register->responsible_id) === (string) $employee->id): echo 'selected'; endif; ?>>
                                    <?php echo e($employee->full_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['responsible_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">Solo se listan usuarios activos con acceso al POS.</p>
                    </div>
                </div>

                <?php if($isClosed): ?>                    <div>
                        <label for="actual_closing_amount" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Monto Real Contado al Cerrar <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">$</span>
                            <input
                                type="number"
                                name="actual_closing_amount"
                                id="actual_closing_amount"
                                value="<?php echo e(old('actual_closing_amount', $register->actual_closing_amount)); ?>"
                                placeholder="0.00"
                                step="0.01"
                                min="0"
                                required
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 pl-8 pr-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                            />
                        </div>
                        <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">Teórico actual con el monto de apertura indicado: <strong class="text-brand-600 dark:text-brand-400">$<?php echo e(number_format($theoretical, 2)); ?></strong></p>
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
                    </div>
                <?php endif; ?>

                <div>
                    <label for="closing_notes" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Descripción / Observaciones</label>
                    <textarea
                        name="closing_notes"
                        id="closing_notes"
                        rows="3"
                        maxlength="500"
                        placeholder="Describe cualquier inconveniente registrado al abrir o cerrar la caja (opcional)"
                        class="w-full rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:placeholder:text-neutral-500"
                    ><?php echo e(old('closing_notes', $register->closing_notes)); ?></textarea>
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
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t border-brand-100 pt-5 dark:border-neutral-700">
                <a href="<?php echo e(route('cash-register.show', $register)); ?>" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700 active:bg-brand-800">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views\modules\cash-registers\edit.blade.php ENDPATH**/ ?>