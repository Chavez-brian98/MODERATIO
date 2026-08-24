<?php $__env->startSection('title', 'Editar rol · ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Roles y Permisos', 'url' => route('roles.index')],
        ['label' => $role->name],
        ['label' => 'Editar'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <header class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400 sm:flex">
                <i class="fa-solid fa-shield-halved text-lg" aria-hidden="true"></i>
            </div>
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">Editar rol</h1>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Actualiza la información de <?php echo e($role->name); ?>.</p>
            </div>
        </div>
        <a
            href="<?php echo e(route('roles.index')); ?>"
            class="inline-flex items-center gap-2 rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
        >
            <i class="fa-solid fa-arrow-left text-sm" aria-hidden="true"></i>
            Volver
        </a>
    </header>

    <div class="mt-6 grid items-start gap-6 lg:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900 lg:col-span-2">
            <div class="flex items-center gap-2 border-b border-brand-100 bg-brand-50/40 px-6 py-4 sm:px-8 dark:border-neutral-800 dark:bg-neutral-800/40">
                <i class="fa-solid fa-circle-info text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-700 dark:text-neutral-300">Información del rol</h2>
            </div>

            <form method="POST" action="<?php echo e(route('roles.update', $role)); ?>" class="space-y-6 px-6 py-6 sm:px-8">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div>
                    <label for="name" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Nombre del rol <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="<?php echo e(old('name', $role->name)); ?>"
                        required
                        maxlength="100"
                        placeholder="Ej. Supervisor de inventario"
                        class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                    />
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="description" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Descripción
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Describe las funciones y alcance de este rol..."
                        class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                    ><?php echo e(old('description', $role->description)); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex items-center justify-between gap-4 rounded-xl border border-brand-200 bg-brand-50/40 px-4 py-3.5 dark:border-neutral-800 dark:bg-neutral-800/40">
                    <div>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">Rol activo</p>
                        <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">Los roles inactivos no pueden asignarse a empleados nuevos.</p>
                    </div>
                    <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $role->is_active)): echo 'checked'; endif; ?> class="peer sr-only" />
                        <div class="peer relative h-6 w-11 rounded-full bg-neutral-300 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:bg-brand-600 peer-checked:after:translate-x-5 dark:bg-neutral-600"></div>
                    </label>
                </div>

                <div>
                    <label for="default_route" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Ruta por defecto al iniciar sesión
                    </label>
                    <select
                        id="default_route"
                        name="default_route"
                        class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                    >
                        <option value="">Usar redirección automática</option>
                        <option value="dashboard" <?php echo e(old('default_route', $role->default_route) === 'dashboard' ? 'selected' : ''); ?>>Dashboard</option>
                        <option value="pos" <?php echo e(old('default_route', $role->default_route) === 'pos' ? 'selected' : ''); ?>>POS (Punto de Venta)</option>
                        <option value="inventory.index" <?php echo e(old('default_route', $role->default_route) === 'inventory.index' ? 'selected' : ''); ?>>Inventario</option>
                        <option value="categories.index" <?php echo e(old('default_route', $role->default_route) === 'categories.index' ? 'selected' : ''); ?>>Categorías</option>
                        <option value="employees.index" <?php echo e(old('default_route', $role->default_route) === 'employees.index' ? 'selected' : ''); ?>>Empleados</option>
                        <option value="roles.index" <?php echo e(old('default_route', $role->default_route) === 'roles.index' ? 'selected' : ''); ?>>Roles y Permisos</option>
                        <option value="cash-register.index" <?php echo e(old('default_route', $role->default_route) === 'cash-register.index' ? 'selected' : ''); ?>>Caja / Arqueo</option>
                        <option value="returns.index" <?php echo e(old('default_route', $role->default_route) === 'returns.index' ? 'selected' : ''); ?>>Devoluciones</option>
                        <option value="reports.index" <?php echo e(old('default_route', $role->default_route) === 'reports.index' ? 'selected' : ''); ?>>Reportes</option>
                        <option value="audit.index" <?php echo e(old('default_route', $role->default_route) === 'audit.index' ? 'selected' : ''); ?>>Bitácora</option>
                        <option value="settings.index" <?php echo e(old('default_route', $role->default_route) === 'settings.index' ? 'selected' : ''); ?>>Configuración</option>
                    </select>
                    <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">Página a la que se redirige al usuario tras iniciar sesión. Si se deja vacío, se usará la redirección automática según permisos.</p>
                    <?php $__errorArgs = ['default_route'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-brand-100 pt-6 dark:border-neutral-800 sm:flex-row sm:justify-end">
                    <a
                        href="<?php echo e(route('roles.index')); ?>"
                        class="inline-flex items-center justify-center rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                    >
                        Cancelar
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 focus-visible:ring-offset-2"
                    >
                        <i class="fa-solid fa-floppy-disk text-sm" aria-hidden="true"></i>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        <aside class="space-y-6">
            <?php if($role->users_count > 0): ?>
                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                    <h3 class="flex items-center gap-2 text-sm font-semibold text-neutral-900 dark:text-white">
                        <i class="fa-solid fa-users text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                        Empleados con este rol
                    </h3>
                    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400"><?php echo e($role->users_count); ?> <?php echo e($role->users_count === 1 ? 'empleado asignado' : 'empleados asignados'); ?>.</p>
                </div>
            <?php endif; ?>

            <div class="rounded-2xl border border-brand-200 bg-brand-50/60 p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-800/40">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-neutral-900 dark:text-white">
                    <i class="fa-solid fa-lightbulb text-amber-500" aria-hidden="true"></i>
                    Consejos
                </h3>
                <ul class="mt-4 space-y-3 text-sm text-neutral-600 dark:text-neutral-400">
                    <li class="flex gap-2.5">
                        <i class="fa-solid fa-circle-check mt-0.5 text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                        Cambiar el nombre del rol no afecta a los empleados ya asignados.
                    </li>
                    <li class="flex gap-2.5">
                        <i class="fa-solid fa-circle-check mt-0.5 text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                        Para reactivar un rol, usa este formulario y guarda los cambios.
                    </li>
                    <li class="flex gap-2.5">
                        <i class="fa-solid fa-circle-check mt-0.5 text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                        Desactiva (no elimines) roles que ya no se utilicen.
                    </li>
                </ul>
            </div>
        </aside>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views/modules/roles/edit.blade.php ENDPATH**/ ?>