<?php $__env->startSection('title', 'Configuración'); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.breadcrumbs', ['crumbs' => [['label' => 'Configuración']]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Configuración</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Ajustes generales del negocio y del sistema.</p>
    </div>

    <form method="POST" action="<?php echo e(route('settings.update')); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Información del Negocio</span>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="business_name" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Nombre del Negocio <span class="text-red-500">*</span></label>
                            <input type="text" name="business_name" id="business_name" value="<?php echo e(old('business_name', $settings['business_name'])); ?>" required maxlength="150"
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            <?php $__errorArgs = ['business_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="business_nit" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">NIT / Registro</label>
                            <input type="text" name="business_nit" id="business_nit" value="<?php echo e(old('business_nit', $settings['business_nit'] ?? '')); ?>" maxlength="20"
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            <?php $__errorArgs = ['business_nit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="business_phone" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Teléfono</label>
                            <input type="text" name="business_phone" id="business_phone" value="<?php echo e(old('business_phone', $settings['business_phone'] ?? '')); ?>" maxlength="20"
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            <?php $__errorArgs = ['business_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="business_email" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Correo Electrónico</label>
                            <input type="email" name="business_email" id="business_email" value="<?php echo e(old('business_email', $settings['business_email'] ?? '')); ?>" maxlength="150"
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            <?php $__errorArgs = ['business_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="business_address" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Dirección</label>
                            <input type="text" name="business_address" id="business_address" value="<?php echo e(old('business_address', $settings['business_address'] ?? '')); ?>" maxlength="255"
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            <?php $__errorArgs = ['business_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                            <i class="fa-solid fa-percent"></i>
                        </div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Impuestos y Moneda</span>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="default_tax_percentage" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">IVA por Defecto (%) <span class="text-red-500">*</span></label>
                            <input type="number" name="default_tax_percentage" id="default_tax_percentage" value="<?php echo e(old('default_tax_percentage', $settings['default_tax_percentage'] ?? 13.00)); ?>" step="0.01" min="0" max="100" required
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            <?php $__errorArgs = ['default_tax_percentage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="currency_symbol" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Símbolo de Moneda <span class="text-red-500">*</span></label>
                            <input type="text" name="currency_symbol" id="currency_symbol" value="<?php echo e(old('currency_symbol', $settings['currency_symbol'] ?? '$')); ?>" maxlength="5" required
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            <?php $__errorArgs = ['currency_symbol'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Ticket / Recibo</span>
                    </div>

                    <div>
                        <label for="receipt_footer" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Mensaje del Pie de Ticket</label>
                        <textarea name="receipt_footer" id="receipt_footer" rows="2" maxlength="255"
                            class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"><?php echo e(old('receipt_footer', $settings['receipt_footer'] ?? '')); ?></textarea>
                        <?php $__errorArgs = ['receipt_footer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-4 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Acerca de</span>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-neutral-500 dark:text-neutral-400">Sistema:</span>
                            <span class="font-medium text-neutral-800 dark:text-neutral-200">Glenda Store</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-500 dark:text-neutral-400">Versión:</span>
                            <span class="font-medium text-neutral-800 dark:text-neutral-200">1.0.0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-500 dark:text-neutral-400">Framework:</span>
                            <span class="font-medium text-neutral-800 dark:text-neutral-200">Laravel</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700 active:bg-brand-800">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views\modules\settings\index.blade.php ENDPATH**/ ?>