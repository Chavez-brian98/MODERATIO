<?php $__env->startSection('title', 'Abrir Caja'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Caja / Arqueo', 'url' => route('cash-register.index')],
        ['label' => 'Abrir Caja'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Abrir Caja</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Registra el monto de apertura y turno para iniciar sesión de caja.</p>
        </div>
        <a href="<?php echo e(route('cash-register.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
            <i class="fa-solid fa-arrow-left text-xs"></i> Volver
        </a>
    </div>

    <div class="mx-auto max-w-2xl">
        <form method="POST" action="<?php echo e(route('cash-register.store')); ?>" class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <?php echo csrf_field(); ?>

            <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <i class="fa-solid fa-lock-open"></i>
                </div>
                <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Datos de Apertura</span>
            </div>

            <div class="space-y-5">
                <div>
                    <label for="opening_amount" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Monto de Apertura <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">$</span>
                        <input
                            type="number"
                            name="opening_amount"
                            id="opening_amount"
                            value="<?php echo e(old('opening_amount')); ?>"
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

                <div>
                    <label for="shift" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Turno</label>
                    <select
                        name="shift"
                        id="shift"
                        class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                    >
                        <option value="">Sin turno asignado</option>
                        <option value="MORNING" <?php echo e(old('shift') === 'MORNING' ? 'selected' : ''); ?>>Mañana</option>
                        <option value="AFTERNOON" <?php echo e(old('shift') === 'AFTERNOON' ? 'selected' : ''); ?>>Tarde</option>
                        <option value="NIGHT" <?php echo e(old('shift') === 'NIGHT' ? 'selected' : ''); ?>>Noche</option>
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
                            <option value="<?php echo e($employee->id); ?>" <?php if(old('responsible_id') == $employee->id): echo 'selected'; endif; ?>>
                                <?php echo e($employee->full_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">Solo se listan usuarios activos con acceso al POS. No es obligatorio que seas tú.</p>
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
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t border-brand-100 pt-5 dark:border-neutral-700">
                <a href="<?php echo e(route('cash-register.index')); ?>" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700 active:bg-brand-800">
                    <i class="fa-solid fa-lock-open text-xs"></i> Abrir Caja
                </button>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views\modules\cash-registers\create.blade.php ENDPATH**/ ?>