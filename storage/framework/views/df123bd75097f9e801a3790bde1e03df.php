<?php $__env->startSection('title', 'POS · ' . config('app.name')); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $shiftLabels = [
            'MORNING' => 'Mañana',
            'AFTERNOON' => 'Tarde',
            'NIGHT' => 'Noche',
        ];
    ?>

    <?php echo $__env->make('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'POS', 'url' => route('pos')],
        ['label' => 'Punto de Venta'],
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(! $cashRegister): ?>
        <div class="mb-4 flex flex-wrap items-center gap-3 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800 shadow-sm dark:border-amber-700 dark:bg-amber-950/30 dark:text-amber-200">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span class="flex-1">
                No puedes realizar ventas hasta tener una caja abierta a tu cargo. Puedes abrir una tú mismo para comenzar a vender.
            </span>
        </div>
    <?php endif; ?>

    <div id="pos-app" class="flex flex-col gap-4 lg:h-[calc(100vh-8rem)] lg:flex-row">
        
        <div class="flex min-h-0 flex-1 flex-col">
            
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative min-w-0 flex-1">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400 dark:text-neutral-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" class="stroke-current" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <input
                        type="search"
                        id="pos-search"
                        placeholder="Buscar producto o escanear código de barras..."
                        autocomplete="off"
                        class="w-full rounded-xl border border-neutral-300 bg-white py-2.5 pl-10 pr-4 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                        autofocus
                    />
                </div>

                <?php if(! $cashRegister): ?>
                    <button
                        type="button"
                        id="btn-open-register"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/40 focus-visible:ring-offset-2"
                    >
                        <i class="fa-solid fa-cash-register" aria-hidden="true"></i>
                        Abrir caja
                    </button>
                <?php else: ?>
                    <div class="inline-flex shrink-0 items-center gap-2 rounded-xl border border-brand-200 bg-brand-50 px-4 py-2.5 text-sm font-medium text-brand-700 dark:border-neutral-700 dark:bg-brand-900/30 dark:text-brand-400">
                        <i class="fa-solid fa-cash-register" aria-hidden="true"></i>
                        Caja abierta
                        <span class="ml-1 rounded-full bg-brand-200 px-2 py-0.5 text-xs font-semibold dark:bg-brand-800"><?php echo e($shiftLabels[$cashRegister->shift] ?? $cashRegister->shift); ?></span>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cash_registers_edit')): ?>
                            <button
                                type="button"
                                id="btn-close-register"
                                title="Cerrar esta caja"
                                class="-mr-1 inline-flex items-center gap-1.5 rounded-lg bg-amber-600 px-2.5 py-1 text-xs font-semibold text-white shadow-sm transition-colors hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500/40"
                            >
                                <i class="fa-solid fa-lock" aria-hidden="true"></i> Cerrar caja
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="mt-3 flex flex-wrap gap-2" id="category-filters">
                <button type="button" data-category-filter="" class="active-filter inline-flex items-center rounded-full border border-brand-300 bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700 transition-colors hover:bg-brand-200 dark:border-neutral-600 dark:bg-brand-900/40 dark:text-brand-400 dark:hover:bg-brand-900/60">
                    Todos
                </button>
                <?php
                    $categories = $products->pluck('category')->filter()->unique('id')->sortBy('name');
                ?>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" data-category-filter="<?php echo e($cat->id); ?>" class="inline-flex items-center rounded-full border border-neutral-200 bg-white px-3 py-1 text-xs font-semibold text-neutral-600 transition-colors hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700">
                        <?php echo e($cat->name); ?>

                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div id="product-grid" class="mt-4 grid grid-cols-2 gap-3 overflow-y-auto sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-3 xl:grid-cols-4" style="scrollbar-width: thin;">
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <button
                        type="button"
                        data-product
                        data-product-id="<?php echo e($product->id); ?>"
                        data-product-name="<?php echo e($product->name); ?>"
                        data-product-price="<?php echo e($product->sale_price); ?>"
                        data-product-stock="<?php echo e($product->current_stock); ?>"
                        data-product-tax="<?php echo e($product->has_tax ? $product->tax_percentage : 0); ?>"
                        data-product-category="<?php echo e($product->category_id); ?>"
                        data-product-search="<?php echo e(strtolower($product->name . ' ' . ($product->barcode ?? '') . ' ' . $product->category?->name)); ?>"
                        class="product-card group flex flex-col rounded-2xl border border-brand-200 bg-white p-3 text-left shadow-sm transition-all hover:border-brand-400 hover:shadow-md active:scale-[0.97] dark:border-neutral-700 dark:bg-neutral-800 dark:hover:border-brand-500"
                    >
                        <div class="flex h-14 w-full items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition-colors group-hover:bg-brand-100 dark:bg-neutral-700 dark:text-brand-400 dark:group-hover:bg-neutral-600">
                            <i class="fa-solid fa-box text-xl" aria-hidden="true"></i>
                        </div>
                        <p class="mt-2 truncate text-sm font-semibold text-neutral-900 dark:text-white" title="<?php echo e($product->name); ?>"><?php echo e($product->name); ?></p>
                        <p class="mt-0.5 truncate text-xs text-neutral-500 dark:text-neutral-400"><?php echo e($product->category?->name ?? 'Sin categoría'); ?></p>
                        <div class="mt-auto flex items-end justify-between pt-2">
                            <p class="text-lg font-bold text-brand-700 dark:text-brand-400">$<?php echo e(number_format($product->sale_price, 2)); ?></p>
                            <span class="text-xs <?php echo e($product->current_stock <= $product->min_stock ? 'font-semibold text-amber-600 dark:text-amber-400' : 'text-neutral-400 dark:text-neutral-500'); ?>">
                                Stock: <?php echo e($product->current_stock); ?>

                            </span>
                        </div>
                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full py-16 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                            <i class="fa-solid fa-box-open text-xl" aria-hidden="true"></i>
                        </div>
                        <p class="mt-3 text-sm font-medium text-neutral-500 dark:text-neutral-400">No hay productos disponibles</p>
                        <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">Agrega productos al inventario primero.</p>
                    </div>
                <?php endif; ?>
                <div id="no-products" class="col-span-full hidden py-16 text-center">
                    <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Sin resultados</p>
                    <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">No se encontraron productos para la búsqueda.</p>
                </div>
            </div>
        </div>

        
        <div class="flex w-full flex-col rounded-2xl border border-brand-200 bg-white shadow-sm lg:w-96 lg:min-h-0 dark:border-neutral-800 dark:bg-neutral-900">
            
            <div class="flex items-center justify-between border-b border-brand-100 px-4 py-3 dark:border-neutral-800">
                <h2 class="flex items-center gap-2 text-sm font-semibold text-neutral-900 dark:text-white">
                    <i class="fa-solid fa-cart-shopping text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                    Venta
                    <span id="cart-count" class="hidden inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-brand-600 px-1.5 text-[10px] font-bold text-white">0</span>
                </h2>
                <button type="button" id="btn-clear-cart" class="text-xs text-neutral-400 transition-colors hover:text-red-500 dark:text-neutral-500 dark:hover:text-red-400">
                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i> Vaciar
                </button>
            </div>

            
            <div class="border-b border-brand-100 px-4 py-3 dark:border-neutral-800">
                <label for="customer-search" class="text-xs font-semibold text-neutral-500 dark:text-neutral-400">Cliente (opcional)</label>
                <div class="relative mt-1">
                    <input
                        type="text"
                        id="customer-search"
                        placeholder="Buscar por nombre o NIT..."
                        autocomplete="off"
                        class="w-full rounded-lg border border-neutral-300 bg-white px-3 py-2 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                    />
                    <div id="customer-dropdown" class="absolute z-10 mt-1 hidden max-h-40 w-full overflow-y-auto rounded-lg border border-neutral-200 bg-white shadow-lg dark:border-neutral-700 dark:bg-neutral-800"></div>
                    <input type="hidden" id="customer-id" />
                    <p id="customer-selected" class="mt-1 hidden text-xs text-brand-600 dark:text-brand-400"></p>
                </div>
            </div>

            
            <div id="cart-items" class="flex-1 overflow-y-auto px-4 py-3" style="scrollbar-width: thin;">
                <div id="cart-empty" class="py-8 text-center">
                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-neutral-100 text-neutral-400 dark:bg-neutral-800 dark:text-neutral-500">
                        <i class="fa-solid fa-cart-plus text-lg" aria-hidden="true"></i>
                    </div>
                    <p class="mt-2 text-sm text-neutral-400 dark:text-neutral-500">Carrito vacío</p>
                    <p class="mt-0.5 text-xs text-neutral-300 dark:text-neutral-600">Selecciona un producto para agregar</p>
                </div>
                <div id="cart-items-list" class="space-y-2"></div>
            </div>

            
            <div class="border-t border-brand-100 px-4 py-3 dark:border-neutral-800">
                <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between text-neutral-500 dark:text-neutral-400">
                        <span>Subtotal</span>
                        <span id="cart-subtotal">$0.00</span>
                    </div>
                    <div class="flex justify-between text-neutral-500 dark:text-neutral-400">
                        <span>IVA (13%)</span>
                        <span id="cart-tax">$0.00</span>
                    </div>
                    <div class="flex justify-between border-t border-neutral-100 pt-1.5 text-lg font-bold text-neutral-900 dark:border-neutral-800 dark:text-white">
                        <span>Total</span>
                        <span id="cart-total">$0.00</span>
                    </div>
                </div>
            </div>

            
            <div class="border-t border-brand-100 px-4 py-4 dark:border-neutral-800">
                <label class="text-xs font-semibold text-neutral-500 dark:text-neutral-400">Método de pago</label>
                <div class="mt-2 grid grid-cols-3 gap-2" id="payment-methods">
                    <button type="button" data-method="CASH" class="payment-btn active-payment flex flex-col items-center gap-1 rounded-xl border-2 border-brand-500 bg-brand-50 px-2 py-2.5 text-xs font-semibold text-brand-700 transition-all hover:bg-brand-100 dark:bg-brand-900/40 dark:text-brand-400">
                        <i class="fa-solid fa-money-bill-wave text-base" aria-hidden="true"></i>
                        Efectivo
                    </button>
                    <button type="button" data-method="CARD" class="payment-btn flex flex-col items-center gap-1 rounded-xl border-2 border-neutral-200 bg-white px-2 py-2.5 text-xs font-semibold text-neutral-600 transition-all hover:border-brand-300 hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:border-brand-600">
                        <i class="fa-solid fa-credit-card text-base" aria-hidden="true"></i>
                        Tarjeta
                    </button>
                    <button type="button" data-method="TRANSFER" class="payment-btn flex flex-col items-center gap-1 rounded-xl border-2 border-neutral-200 bg-white px-2 py-2.5 text-xs font-semibold text-neutral-600 transition-all hover:border-brand-300 hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:border-brand-600">
                        <i class="fa-solid fa-building-columns text-base" aria-hidden="true"></i>
                        Transferencia
                    </button>
                </div>

                <div id="cash-input-group" class="mt-3">
                    <label for="amount-received" class="text-xs font-semibold text-neutral-500 dark:text-neutral-400">Recibido</label>
                    <input
                        type="number"
                        id="amount-received"
                        min="0"
                        step="0.01"
                        value="0"
                        class="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-3 py-2.5 text-sm font-semibold text-neutral-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    />
                    <div id="change-display" class="mt-1.5 hidden text-sm font-semibold text-green-600 dark:text-green-400">
                        Cambio: <span id="change-amount">$0.00</span>
                    </div>
                </div>

                <button
                    type="button"
                    id="btn-pay"
                    disabled
                    class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-brand-700 px-4 py-3 text-sm font-bold text-white shadow-sm transition-colors hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-brand-600 dark:hover:bg-brand-500"
                >
                    <i class="fa-solid fa-check-circle" aria-hidden="true"></i>
                    Completar venta
                </button>
            </div>
        </div>
    </div>

    
    <div id="register-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-neutral-900/50 p-4 backdrop-blur-sm" style="display:none;">
        <div class="max-h-[85vh] w-full max-w-sm overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl dark:bg-neutral-900">
            <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Abrir caja</h3>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Ingresa el monto de apertura y selecciona el turno.</p>
            <form id="form-open-register" class="mt-4 space-y-3">
                <div>
                    <label for="register-amount" class="text-xs font-semibold text-neutral-500 dark:text-neutral-400">Monto de apertura</label>
                    <input
                        type="number"
                        id="register-amount"
                        name="opening_amount"
                        min="0"
                        step="0.01"
                        placeholder="0.00"
                        required
                        class="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    />
                </div>
                <div>
                    <label for="register-shift" class="text-xs font-semibold text-neutral-500 dark:text-neutral-400">Turno</label>
                    <select
                        id="register-shift"
                        name="shift"
                        required
                        class="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                        <option value="MORNING">Mañana</option>
                        <option value="AFTERNOON">Tarde</option>
                        <option value="NIGHT">Noche</option>
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" class="btn-cancel-register flex-1 rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm font-semibold text-neutral-700 transition-colors hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-800">
                        Abrir
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <?php if($cashRegister): ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('cash_registers_edit')): ?>
            <div id="close-register-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-neutral-900/50 p-4 backdrop-blur-sm" style="display:none;">
                <div class="max-h-[85vh] w-full max-w-sm overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl dark:bg-neutral-900">
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Cerrar caja</h3>
                    <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Cuadra el efectivo y cierra tu caja sin salir del POS.</p>

                    <div class="mt-4 space-y-1.5 rounded-xl bg-neutral-50 p-3 text-xs dark:bg-neutral-800/60">
                        <div class="flex justify-between">
                            <span class="text-neutral-500 dark:text-neutral-400">Apertura</span>
                            <span class="font-medium text-neutral-800 dark:text-neutral-200">$<?php echo e(number_format($cashSummary['opening_amount'], 2)); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-500 dark:text-neutral-400">+ Ventas en efectivo</span>
                            <span class="font-medium text-neutral-800 dark:text-neutral-200">$<?php echo e(number_format($cashSummary['cash_sales'], 2)); ?></span>
                        </div>
                        <div class="flex justify-between border-t border-neutral-200 pt-1.5 dark:border-neutral-700">
                            <span class="font-semibold text-neutral-700 dark:text-neutral-300">= Teórico</span>
                            <span class="font-bold text-brand-600 dark:text-brand-400">$<?php echo e(number_format($cashSummary['theoretical'], 2)); ?></span>
                        </div>
                    </div>

                    <form id="form-close-register" class="mt-4 space-y-3">
                        <div>
                            <label for="close-register-amount" class="text-xs font-semibold text-neutral-500 dark:text-neutral-400">Monto real en efectivo <span class="text-red-500">*</span></label>
                            <input
                                type="number"
                                id="close-register-amount"
                                name="actual_closing_amount"
                                min="0"
                                step="0.01"
                                placeholder="<?php echo e(number_format($cashSummary['theoretical'], 2, '.', '')); ?>"
                                required
                                class="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                            />
                            <p id="close-difference-display" class="mt-1.5 text-xs font-medium"></p>
                        </div>
                        <div>
                            <label for="close-register-notes" class="text-xs font-semibold text-neutral-500 dark:text-neutral-400">Descripción / Observaciones</label>
                            <textarea
                                id="close-register-notes"
                                name="closing_notes"
                                rows="2"
                                maxlength="500"
                                placeholder="Inconvenientes: sobrante/faltante justificado, gasto de caja, etc. (opcional)"
                                class="mt-1 w-full rounded-lg border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                            ></textarea>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" class="btn-cancel-close-register flex-1 rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm font-semibold text-neutral-700 transition-colors hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700">
                                Cancelar
                            </button>
                            <button type="submit" id="btn-confirm-close-register" class="flex-1 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-amber-700">
                                Cerrar caja
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    
    <div id="receipt-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-neutral-900/50 p-4 backdrop-blur-sm" style="display:none;">
        <div class="max-h-[85vh] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl dark:bg-neutral-900">
            <div class="text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-400">
                    <i class="fa-solid fa-check text-2xl" aria-hidden="true"></i>
                </div>
                <h3 class="mt-3 text-lg font-semibold text-neutral-900 dark:text-white">Venta completada</h3>
                <p id="receipt-ticket" class="mt-1 text-sm text-neutral-500 dark:text-neutral-400"></p>
            </div>
            <div id="receipt-details" class="mt-4 rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            </div>
            <div class="mt-4 flex gap-3">
                <button type="button" id="btn-print-receipt" class="flex flex-1 items-center justify-center gap-2 rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm font-semibold text-neutral-700 transition-colors hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700">
                    <i class="fa-solid fa-print" aria-hidden="true"></i> Imprimir
                </button>
                <button type="button" class="btn-close-receipt flex flex-1 items-center justify-center gap-2 rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-800">
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i> Nueva venta
                </button>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const products = <?php echo json_encode($productsJson, 15, 512) ?>;

            let cart = [];
            let selectedPaymentMethod = 'CASH';
            let selectedCustomerId = null;
            let cashRegisterOpen = <?php echo json_encode($cashRegister !== null, 15, 512) ?>;

            const searchInput = document.getElementById('pos-search');
            const productGrid = document.getElementById('product-grid');
            const productButtons = productGrid.querySelectorAll('[data-product]');
            const noProducts = document.getElementById('no-products');
            const cartCount = document.getElementById('cart-count');
            const cartEmpty = document.getElementById('cart-empty');
            const cartItemsList = document.getElementById('cart-items-list');
            const cartSubtotal = document.getElementById('cart-subtotal');
            const cartTax = document.getElementById('cart-tax');
            const cartTotal = document.getElementById('cart-total');
            const amountReceived = document.getElementById('amount-received');
            const changeDisplay = document.getElementById('change-display');
            const changeAmount = document.getElementById('change-amount');
            const btnPay = document.getElementById('btn-pay');
            const cashInputGroup = document.getElementById('cash-input-group');
            const btnClearCart = document.getElementById('btn-clear-cart');
            const categoryFilters = document.getElementById('category-filters');

            // Product search
            searchInput.addEventListener('input', () => {
                filterProducts();
            });

            // Category filters
            categoryFilters.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-category-filter]');
                if (!btn) return;

                categoryFilters.querySelectorAll('button').forEach((b) => {
                    b.classList.remove('active-filter', 'border-brand-300', 'bg-brand-100', 'text-brand-700', 'dark:bg-brand-900/40', 'dark:text-brand-400', 'dark:border-neutral-600');
                    b.classList.add('border-neutral-200', 'bg-white', 'text-neutral-600', 'dark:border-neutral-700', 'dark:bg-neutral-800', 'dark:text-neutral-400');
                });
                btn.classList.add('active-filter', 'border-brand-300', 'bg-brand-100', 'text-brand-700', 'dark:bg-brand-900/40', 'dark:text-brand-400', 'dark:border-neutral-600');
                btn.classList.remove('border-neutral-200', 'bg-white', 'text-neutral-600', 'dark:border-neutral-700', 'dark:bg-neutral-800', 'dark:text-neutral-400');

                filterProducts();
            });

            function getActiveCategoryFilter() {
                const activeBtn = categoryFilters.querySelector('.active-filter');
                return activeBtn ? activeBtn.dataset.categoryFilter : '';
            }

            function filterProducts() {
                const term = searchInput.value.trim().toLowerCase();
                const catFilter = getActiveCategoryFilter();
                let visible = 0;

                productButtons.forEach((btn) => {
                    const searchText = btn.dataset.productSearch;
                    const catId = btn.dataset.productCategory;
                    const matchesSearch = !term || searchText.includes(term);
                    const matchesCategory = !catFilter || catId === catFilter;
                    const show = matchesSearch && matchesCategory;
                    btn.classList.toggle('hidden', !show);
                    if (show) visible++;
                });

                noProducts.classList.toggle('hidden', visible > 0);
            }

            // Add to cart
            productGrid.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-product]');
                if (!btn) return;

                const productId = parseInt(btn.dataset.productId);
                const existing = cart.find((item) => item.product_id === productId);

                if (existing) {
                    if (existing.quantity >= parseInt(btn.dataset.productStock)) {
                        Swal.fire({
                            title: 'Sin stock',
                            text: 'No hay más unidades disponibles.',
                            icon: 'warning',
                            confirmButtonText: 'OK',
                            confirmButtonColor: window.SwalColors.warning,
                        });
                        return;
                    }
                    existing.quantity++;
                } else {
                    if (parseInt(btn.dataset.productStock) <= 0) {
                        return;
                    }
                    cart.push({
                        product_id: productId,
                        name: btn.dataset.productName,
                        unit_price: parseFloat(btn.dataset.productPrice),
                        quantity: 1,
                        stock: parseInt(btn.dataset.productStock),
                        discount: 0,
                        has_tax: products.find((p) => p.id === productId)?.has_tax || false,
                        tax_percentage: parseFloat(btn.dataset.productTax),
                    });
                }

                renderCart();
            });

            // Cart actions (remove, quantity)
            cartItemsList.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('[data-remove-item]');
                if (removeBtn) {
                    const id = parseInt(removeBtn.dataset.removeItem);
                    cart = cart.filter((item) => item.product_id !== id);
                    renderCart();
                    return;
                }

                const minusBtn = e.target.closest('[data-minus]');
                if (minusBtn) {
                    const id = parseInt(minusBtn.dataset.minus);
                    const item = cart.find((i) => i.product_id === id);
                    if (item) {
                        if (item.quantity <= 1) {
                            cart = cart.filter((i) => i.product_id !== id);
                        } else {
                            item.quantity--;
                        }
                        renderCart();
                    }
                }

                const plusBtn = e.target.closest('[data-plus]');
                if (plusBtn) {
                    const id = parseInt(plusBtn.dataset.plus);
                    const item = cart.find((i) => i.product_id === id);
                    if (item && item.quantity < item.stock) {
                        item.quantity++;
                        renderCart();
                    }
                }
            });

            // Discount inputs
            cartItemsList.addEventListener('change', (e) => {
                const discountInput = e.target.closest('[data-discount-input]');
                if (!discountInput) return;
                const id = parseInt(discountInput.dataset.discountInput);
                const item = cart.find((i) => i.product_id === id);
                if (item) {
                    item.discount = Math.max(0, parseFloat(discountInput.value) || 0);
                    renderCart();
                }
            });

            // Clear cart
            btnClearCart.addEventListener('click', () => {
                if (cart.length === 0) return;
                cart = [];
                renderCart();
            });

            // Payment method
            document.getElementById('payment-methods').addEventListener('click', (e) => {
                const btn = e.target.closest('[data-method]');
                if (!btn) return;

                selectedPaymentMethod = btn.dataset.method;

                document.querySelectorAll('.payment-btn').forEach((b) => {
                    b.classList.remove('active-payment', 'border-brand-500', 'bg-brand-50', 'text-brand-700', 'dark:bg-brand-900/40', 'dark:text-brand-400');
                    b.classList.add('border-neutral-200', 'bg-white', 'text-neutral-600', 'dark:border-neutral-700', 'dark:bg-neutral-800', 'dark:text-neutral-400');
                });
                btn.classList.add('active-payment', 'border-brand-500', 'bg-brand-50', 'text-brand-700', 'dark:bg-brand-900/40', 'dark:text-brand-400');
                btn.classList.remove('border-neutral-200', 'bg-white', 'text-neutral-600', 'dark:border-neutral-700', 'dark:bg-neutral-800', 'dark:text-neutral-400');

                cashInputGroup.classList.toggle('hidden', selectedPaymentMethod !== 'CASH');
                updateTotals();
            });

            // Amount received
            amountReceived.addEventListener('input', updateTotals);

            // Customer search
            const customerSearch = document.getElementById('customer-search');
            const customerDropdown = document.getElementById('customer-dropdown');
            const customerIdInput = document.getElementById('customer-id');
            const customerSelected = document.getElementById('customer-selected');
            let customerSearchTimeout;

            customerSearch.addEventListener('input', () => {
                clearTimeout(customerSearchTimeout);
                const q = customerSearch.value.trim();

                if (q.length < 2) {
                    customerDropdown.classList.add('hidden');
                    return;
                }

                customerSearchTimeout = setTimeout(async () => {
                    try {
                        const resp = await fetch('<?php echo e(route("pos.customers")); ?>?q=' + encodeURIComponent(q), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        });
                        const data = await resp.json();
                        if (data.length === 0) {
                            customerDropdown.classList.add('hidden');
                            return;
                        }
                        customerDropdown.innerHTML = data.map((c) =>
                            `<div class="cursor-pointer px-3 py-2 text-sm hover:bg-brand-50 dark:hover:bg-neutral-700" data-customer-id="${c.id}" data-customer-name="${c.first_name} ${c.last_name}" data-customer-tax="${c.tax_id || ''}">
                                ${c.first_name} ${c.last_name}
                                ${c.tax_id ? `<span class="ml-2 text-xs text-neutral-400">NIT: ${c.tax_id}</span>` : ''}
                            </div>`
                        ).join('');
                        customerDropdown.classList.remove('hidden');
                    } catch (err) { /* ignore */ }
                }, 300);
            });

            customerDropdown.addEventListener('click', (e) => {
                const opt = e.target.closest('[data-customer-id]');
                if (!opt) return;
                selectedCustomerId = parseInt(opt.dataset.customerId);
                customerIdInput.value = selectedCustomerId;
                customerSearch.value = '';
                customerDropdown.classList.add('hidden');
                customerSelected.textContent = opt.dataset.customerName + (opt.dataset.customerTax ? ' (NIT: ' + opt.dataset.customerTax + ')' : '');
                customerSelected.classList.remove('hidden');
                customerSearch.classList.add('hidden');
            });

            // Remove customer
            customerSelected.addEventListener('click', () => {
                selectedCustomerId = null;
                customerIdInput.value = '';
                customerSelected.classList.add('hidden');
                customerSearch.classList.remove('hidden');
                customerSearch.value = '';
            });

            // Open cash register modal
            const registerModal = document.getElementById('register-modal');
            const btnOpenRegister = document.getElementById('btn-open-register');

            if (btnOpenRegister) {
                btnOpenRegister.addEventListener('click', () => {
                    registerModal.style.display = 'flex';
                    registerModal.classList.remove('hidden');
                });
            }

            registerModal.querySelectorAll('.btn-cancel-register').forEach((btn) => {
                btn.addEventListener('click', () => {
                    registerModal.style.display = 'none';
                    registerModal.classList.add('hidden');
                });
            });

            <?php if(! $cashRegister): ?>
                Swal.fire({
                    title: 'Sin caja abierta',
                    text: 'No puedes realizar ventas hasta tener una caja abierta a tu cargo. Puedes abrir una tú mismo para comenzar.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Abrir caja ahora',
                    cancelButtonText: 'Más tarde',
                    confirmButtonColor: window.SwalColors.warning,
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        registerModal.style.display = 'flex';
                        registerModal.classList.remove('hidden');
                    }
                });
            <?php endif; ?>

            document.getElementById('form-open-register').addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(e.target);

                try {
                    const resp = await fetch('<?php echo e(route("pos.cash-register.open")); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            opening_amount: parseFloat(formData.get('opening_amount')),
                            shift: formData.get('shift'),
                        }),
                    });

                    if (resp.ok) {
                        cashRegisterOpen = true;
                        registerModal.style.display = 'none';
                        registerModal.classList.add('hidden');
                        window.notifySuccess('Caja abierta correctamente.');
                        setTimeout(() => location.reload(), 900);
                    }
                } catch (err) {
                    Swal.fire({ title: 'Error', text: 'No se pudo abrir la caja.', icon: 'error', confirmButtonText: 'OK', confirmButtonColor: window.SwalColors.danger });
                }
            });

            // Close cash register from POS
            const closeRegisterModal = document.getElementById('close-register-modal');
            const btnCloseRegister = document.getElementById('btn-close-register');

            if (btnCloseRegister && closeRegisterModal) {
                const closeForm = document.getElementById('form-close-register');
                const closeAmount = document.getElementById('close-register-amount');
                const closeDiffDisplay = document.getElementById('close-difference-display');
                const btnConfirmClose = document.getElementById('btn-confirm-close-register');
                const theoretical = <?php echo e(($cashSummary['theoretical'] ?? 0)); ?>;

                btnCloseRegister.addEventListener('click', () => {
                    closeDiffDisplay.textContent = '';
                    closeRegisterModal.style.display = 'flex';
                    closeRegisterModal.classList.remove('hidden');
                    closeAmount.focus();
                });

                closeRegisterModal.querySelectorAll('.btn-cancel-close-register').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        closeRegisterModal.style.display = 'none';
                        closeRegisterModal.classList.add('hidden');
                    });
                });

                closeRegisterModal.addEventListener('click', (e) => {
                    if (e.target === closeRegisterModal) {
                        closeRegisterModal.style.display = 'none';
                        closeRegisterModal.classList.add('hidden');
                    }
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && closeRegisterModal.style.display === 'flex') {
                        closeRegisterModal.style.display = 'none';
                        closeRegisterModal.classList.add('hidden');
                    }
                });

                closeAmount?.addEventListener('input', function () {
                    if (this.value === '') {
                        closeDiffDisplay.textContent = '';
                        return;
                    }
                    const diff = (parseFloat(this.value) || 0) - theoretical;
                    if (diff === 0) {
                        closeDiffDisplay.textContent = 'Coincide con el monto teórico';
                        closeDiffDisplay.className = 'mt-1.5 text-xs font-medium text-green-600 dark:text-green-400';
                    } else if (diff > 0) {
                        closeDiffDisplay.textContent = `Sobrante: $${diff.toFixed(2)}`;
                        closeDiffDisplay.className = 'mt-1.5 text-xs font-medium text-blue-600 dark:text-blue-400';
                    } else {
                        closeDiffDisplay.textContent = `Faltante: $${Math.abs(diff).toFixed(2)}`;
                        closeDiffDisplay.className = 'mt-1.5 text-xs font-medium text-red-600 dark:text-red-400';
                    }
                });

                closeForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const amount = parseFloat(closeAmount.value);
                    if (isNaN(amount) || amount < 0) {
                        Swal.fire({ title: 'Monto inválido', text: 'Ingresa el monto real contado en efectivo.', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: window.SwalColors.warning });
                        return;
                    }

                    const diff = amount - theoretical;
                    const diffMsg = diff === 0
                        ? 'El monto coincide con el teórico.'
                        : diff > 0
                            ? `Sobrante de $${diff.toFixed(2)} respecto al teórico.`
                            : `Faltante de $${Math.abs(diff).toFixed(2)} respecto al teórico.`;

                    const result = await Swal.fire({
                        title: '¿Cerrar caja?',
                        text: diffMsg,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, cerrar caja',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: window.SwalColors.brand,
                    });

                    if (!result.isConfirmed) return;

                    btnConfirmClose.disabled = true;
                    btnConfirmClose.innerHTML = '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Cerrando...';

                    try {
                        const resp = await fetch('<?php echo e(route("pos.cash-register.close")); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                actual_closing_amount: amount,
                                closing_notes: document.getElementById('close-register-notes').value.trim() || null,
                            }),
                        });

                        const data = await resp.json().catch(() => ({}));

                        if (!resp.ok) {
                            throw new Error(data.error || 'No se pudo cerrar la caja.');
                        }

                        cashRegisterOpen = false;
                        closeRegisterModal.style.display = 'none';
                        closeRegisterModal.classList.add('hidden');
                        window.notifySuccess('Caja cerrada correctamente.');
                        setTimeout(() => location.reload(), 900);
                    } catch (err) {
                        Swal.fire({ title: 'Error', text: err.message, icon: 'error', confirmButtonText: 'OK', confirmButtonColor: window.SwalColors.danger });
                    } finally {
                        btnConfirmClose.disabled = false;
                        btnConfirmClose.innerHTML = 'Cerrar caja';
                    }
                });
            }

            // Complete sale
            btnPay.addEventListener('click', async () => {
                if (cart.length === 0) return;
                if (!cashRegisterOpen) {
                    Swal.fire({ title: 'Sin caja abierta', text: 'No tienes una caja abierta a tu cargo. Ábrela para poder vender.', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: window.SwalColors.warning });
                    return;
                }

                const total = parseFloat(document.getElementById('cart-total').dataset.rawValue || 0);
                const received = selectedPaymentMethod === 'CASH' ? parseFloat(amountReceived.value) || 0 : total;

                if (selectedPaymentMethod === 'CASH' && received < total) {
                    Swal.fire({ title: 'Monto insuficiente', text: 'El monto recibido es menor al total.', icon: 'warning', confirmButtonText: 'OK', confirmButtonColor: window.SwalColors.warning });
                    return;
                }

                btnPay.disabled = true;
                btnPay.innerHTML = '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Procesando...';

                try {
                    const resp = await fetch('<?php echo e(route("pos.sale.complete")); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            items: cart.map((item) => ({
                                product_id: item.product_id,
                                quantity: item.quantity,
                                unit_price: item.unit_price,
                                discount: item.discount,
                            })),
                            customer_id: selectedCustomerId,
                            payment_method: selectedPaymentMethod,
                            amount_received: received,
                        }),
                    });

                    const data = await resp.json();

                    if (!resp.ok) {
                        throw new Error(data.error || 'Error al procesar la venta.');
                    }

                    showReceipt(data.sale);
                    cart = [];
                    selectedCustomerId = null;
                    customerIdInput.value = '';
                    customerSelected.classList.add('hidden');
                    customerSearch.classList.remove('hidden');
                    customerSearch.value = '';
                    amountReceived.value = '0';
                    renderCart();
                } catch (err) {
                    Swal.fire({ title: 'Error', text: err.message, icon: 'error', confirmButtonText: 'OK', confirmButtonColor: window.SwalColors.danger });
                } finally {
                    btnPay.disabled = false;
                    btnPay.innerHTML = '<i class="fa-solid fa-check-circle" aria-hidden="true"></i> Completar venta';
                }
            });

            // Receipt modal
            const receiptModal = document.getElementById('receipt-modal');

            function showReceipt(sale) {
                document.getElementById('receipt-ticket').textContent = 'Ticket: ' + sale.ticket_number;

                const items = sale.details || [];
                const itemsHtml = items.map((d) => `
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-600 dark:text-neutral-400">${d.quantity}x ${d.product?.name || 'Producto'}</span>
                        <span class="font-medium text-neutral-900 dark:text-white">$${parseFloat(d.subtotal).toFixed(2)}</span>
                    </div>
                `).join('');

                document.getElementById('receipt-details').innerHTML = `
                    <div class="space-y-1.5">${itemsHtml}</div>
                    <div class="mt-3 border-t border-neutral-200 pt-2 dark:border-neutral-700">
                        <div class="flex justify-between text-sm font-bold text-neutral-900 dark:text-white">
                            <span>Total</span>
                            <span>$${parseFloat(sale.total).toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between text-sm text-neutral-500 dark:text-neutral-400">
                            <span>Pagado</span>
                            <span>$${parseFloat(sale.amount_received).toFixed(2)}</span>
                        </div>
                        ${sale.change_due > 0 ? `<div class="flex justify-between text-sm font-semibold text-green-600 dark:text-green-400"><span>Cambio</span><span>$${parseFloat(sale.change_due).toFixed(2)}</span></div>` : ''}
                        <div class="flex justify-between text-xs text-neutral-400 dark:text-neutral-500">
                            <span>Método</span>
                            <span>${sale.payment_method === 'CASH' ? 'Efectivo' : sale.payment_method === 'CARD' ? 'Tarjeta' : 'Transferencia'}</span>
                        </div>
                    </div>
                `;

                receiptModal.style.display = 'flex';
                receiptModal.classList.remove('hidden');
            }

            receiptModal.querySelectorAll('.btn-close-receipt').forEach((btn) => {
                btn.addEventListener('click', () => {
                    receiptModal.style.display = 'none';
                    receiptModal.classList.add('hidden');
                    searchInput.focus();
                });
            });

            document.getElementById('btn-print-receipt')?.addEventListener('click', () => {
                window.print();
            });

            // Render cart
            function renderCart() {
                const count = cart.reduce((sum, item) => sum + item.quantity, 0);
                cartCount.textContent = count;
                cartCount.classList.toggle('hidden', count === 0);
                cartEmpty.classList.toggle('hidden', cart.length > 0);

                cartItemsList.innerHTML = cart.map((item) => `
                    <div class="flex items-start gap-3 rounded-xl border border-neutral-100 bg-neutral-50 p-3 dark:border-neutral-800 dark:bg-neutral-800/50">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-neutral-900 dark:text-white">${item.name}</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">$${item.unit_price.toFixed(2)} c/u</p>
                            <div class="mt-1.5 flex items-center gap-2">
                                <label class="text-[10px] text-neutral-400 dark:text-neutral-500">Dto:</label>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    value="${item.discount}"
                                    data-discount-input="${item.product_id}"
                                    class="w-16 rounded border border-neutral-200 bg-white px-1.5 py-0.5 text-[11px] text-neutral-700 focus:border-brand-500 focus:outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300"
                                />
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <button type="button" data-remove-item="${item.product_id}" class="text-neutral-400 transition-colors hover:text-red-500 dark:text-neutral-500 dark:hover:text-red-400">
                                <i class="fa-solid fa-xmark text-xs" aria-hidden="true"></i>
                            </button>
                            <div class="flex items-center gap-1">
                                <button type="button" data-minus="${item.product_id}" class="flex h-6 w-6 items-center justify-center rounded-md border border-neutral-200 bg-white text-neutral-600 transition-colors hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700">
                                    <i class="fa-solid fa-minus text-[10px]" aria-hidden="true"></i>
                                </button>
                                <span class="w-6 text-center text-sm font-semibold text-neutral-900 dark:text-white">${item.quantity}</span>
                                <button type="button" data-plus="${item.product_id}" class="flex h-6 w-6 items-center justify-center rounded-md border border-neutral-200 bg-white text-neutral-600 transition-colors hover:bg-neutral-100 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-400 dark:hover:bg-neutral-700">
                                    <i class="fa-solid fa-plus text-[10px]" aria-hidden="true"></i>
                                </button>
                            </div>
                            <p class="text-sm font-bold text-brand-700 dark:text-brand-400">$${((item.unit_price * item.quantity) - item.discount).toFixed(2)}</p>
                        </div>
                    </div>
                `).join('');

                updateTotals();
            }

            function updateTotals() {
                let subtotal = 0;
                let tax = 0;

                cart.forEach((item) => {
                    const line = (item.unit_price * item.quantity) - item.discount;
                    subtotal += line;
                    if (item.has_tax) {
                        tax += line * (item.tax_percentage / 100);
                    }
                });

                const total = subtotal + tax;
                const received = selectedPaymentMethod === 'CASH' ? parseFloat(amountReceived.value) || 0 : total;
                const change = Math.max(0, received - total);

                cartSubtotal.textContent = '$' + subtotal.toFixed(2);
                cartTax.textContent = '$' + tax.toFixed(2);
                cartTotal.textContent = '$' + total.toFixed(2);
                cartTotal.dataset.rawValue = total.toFixed(2);

                if (selectedPaymentMethod === 'CASH' && received > 0) {
                    changeDisplay.classList.remove('hidden');
                    changeAmount.textContent = '$' + change.toFixed(2);
                } else {
                    changeDisplay.classList.add('hidden');
                }

                btnPay.disabled = cart.length === 0 || !cashRegisterOpen;
            }

            // Initial render
            renderCart();

            // Focus search on / key
            document.addEventListener('keydown', (e) => {
                if (e.key === '/' && document.activeElement !== searchInput && document.activeElement.tagName !== 'INPUT') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Brian\PhpstormProjects\Glenda_Store\resources\views/modules/pos/index.blade.php ENDPATH**/ ?>