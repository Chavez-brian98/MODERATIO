@extends('layouts.app')

@section('title', 'Editar: ' . $product->name)

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Inventario', 'url' => route('inventory.index')],
        ['label' => $product->name, 'url' => route('inventory.show', $product)],
        ['label' => 'Editar'],
    ]])

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Editar Producto</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ $product->name }}</p>
        </div>
        <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
            <i class="fa-solid fa-arrow-left text-xs"></i> Volver
        </a>
    </div>

    <form method="POST" action="{{ route('inventory.update', $product) }}">
        @csrf
        @method('PUT')

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                            <i class="fa-solid fa-box"></i>
                        </div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Información del Producto</span>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Nombre <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required maxlength="150"
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="barcode" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Código de Barras</label>
                            <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode) }}" maxlength="50"
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            @error('barcode') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="category_id" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Categoría <span class="text-red-500">*</span></label>
                            <select name="category_id" id="category_id" required
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200">
                                <option value="">Seleccionar categoría</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="description" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Descripción</label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200">{{ old('description', $product->description) }}</textarea>
                            @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                            <i class="fa-solid fa-dollar-sign"></i>
                        </div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Precios</span>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="purchase_price" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Precio de Compra <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">$</span>
                                <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price', number_format($product->purchase_price, 2, '.', '')) }}" step="0.01" min="0" required
                                    class="w-full rounded-xl border border-brand-200 bg-white py-2.5 pl-8 pr-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            </div>
                            @error('purchase_price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="sale_price" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Precio de Venta <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">$</span>
                                <input type="number" name="sale_price" id="sale_price" value="{{ old('sale_price', number_format($product->sale_price, 2, '.', '')) }}" step="0.01" min="0" required
                                    class="w-full rounded-xl border border-brand-200 bg-white py-2.5 pl-8 pr-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            </div>
                            @error('sale_price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                            <i class="fa-solid fa-warehouse"></i>
                        </div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Stock</span>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="current_stock" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Stock Actual <span class="text-red-500">*</span></label>
                            <input type="number" name="current_stock" id="current_stock" value="{{ old('current_stock', $product->current_stock) }}" min="0" required
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            @error('current_stock') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="min_stock" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Stock Mínimo <span class="text-red-500">*</span></label>
                            <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', $product->min_stock) }}" min="0" required
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            @error('min_stock') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                            <i class="fa-solid fa-percent"></i>
                        </div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Impuesto</span>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label for="has_tax" class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Aplica IVA</label>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" name="has_tax" id="has_tax" value="1" {{ old('has_tax', $product->has_tax ? '1' : '0') === '1' ? 'checked' : '' }} class="peer sr-only" onchange="document.getElementById('tax_percentage_group').style.display = this.checked ? '' : 'none'" />
                                <div class="peer h-6 w-11 rounded-full bg-neutral-300 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-sm after:transition-all peer-checked:bg-brand-600 peer-checked:after:translate-x-full dark:bg-neutral-600 dark:peer-checked:bg-brand-500"></div>
                            </label>
                        </div>
                        <div id="tax_percentage_group" style="{{ old('has_tax', $product->has_tax ? '1' : '0') !== '1' ? 'display:none' : '' }}">
                            <label for="tax_percentage" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Porcentaje IVA (%)</label>
                            <input type="number" name="tax_percentage" id="tax_percentage" value="{{ old('tax_percentage', $product->tax_percentage) }}" step="0.01" min="0" max="100"
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
                            @error('tax_percentage') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Resumen</p>
                    <div class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-neutral-500 dark:text-neutral-400">Precio compra:</span>
                            <span id="summary-purchase" class="font-medium text-neutral-800 dark:text-neutral-200">${{ number_format($product->purchase_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-neutral-500 dark:text-neutral-400">Precio venta:</span>
                            <span id="summary-sale" class="font-medium text-neutral-800 dark:text-neutral-200">${{ number_format($product->sale_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t border-brand-100 pt-2 dark:border-neutral-700">
                            <span class="text-neutral-500 dark:text-neutral-400">Margen:</span>
                            <span id="summary-margin" class="font-semibold text-emerald-600 dark:text-emerald-400">${{ number_format($product->sale_price - $product->purchase_price, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('inventory.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
                        Cancelar
                    </a>
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700 active:bg-brand-800">
                        <i class="fa-solid fa-floppy-disk text-xs"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        const purchaseEl = document.getElementById('purchase_price');
        const saleEl = document.getElementById('sale_price');
        function updateMargin() {
            const p = parseFloat(purchaseEl.value) || 0;
            const s = parseFloat(saleEl.value) || 0;
            document.getElementById('summary-purchase').textContent = '$' + p.toFixed(2);
            document.getElementById('summary-sale').textContent = '$' + s.toFixed(2);
            document.getElementById('summary-margin').textContent = '$' + (s - p).toFixed(2);
        }
        purchaseEl?.addEventListener('input', updateMargin);
        saleEl?.addEventListener('input', updateMargin);
    </script>
@endsection
