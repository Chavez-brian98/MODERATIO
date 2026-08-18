@extends('layouts.app')

@section('title', $product->name)

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Inventario', 'url' => route('inventory.index')],
        ['label' => $product->name],
    ]])

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">{{ $product->name }}</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                {{ $product->category->name }} @if ($product->barcode) — <span class="font-mono">{{ $product->barcode }}</span> @endif
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if ($product->is_active)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <i class="fa-solid fa-circle text-[6px]"></i> Activo
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                    <i class="fa-solid fa-circle text-[6px]"></i> Inactivo
                </span>
            @endif
            <a href="{{ route('inventory.edit', $product) }}" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
                <i class="fa-solid fa-pen text-xs"></i> Editar
            </a>
            <a href="{{ route('inventory.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
                <i class="fa-solid fa-arrow-left text-xs"></i> Volver
            </a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Información del Producto</span>
                </div>

                <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Nombre</dt>
                        <dd class="mt-1 text-sm font-medium text-neutral-800 dark:text-neutral-200">{{ $product->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Código de Barras</dt>
                        <dd class="mt-1 font-mono text-sm text-neutral-800 dark:text-neutral-200">{{ $product->barcode ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Categoría</dt>
                        <dd class="mt-1 text-sm text-neutral-800 dark:text-neutral-200">{{ $product->category->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Creado</dt>
                        <dd class="mt-1 text-sm text-neutral-800 dark:text-neutral-200">{{ $product->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if ($product->description)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Descripción</dt>
                            <dd class="mt-1 text-sm text-neutral-700 dark:text-neutral-300">{{ $product->description }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                        <i class="fa-solid fa-dollar-sign"></i>
                    </div>
                    <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Precios</span>
                </div>

                <div class="grid gap-6 sm:grid-cols-3">
                    <div class="text-center">
                        <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Compra</p>
                        <p class="mt-1 text-xl font-bold text-neutral-800 dark:text-neutral-100">${{ number_format($product->purchase_price, 2) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Venta</p>
                        <p class="mt-1 text-xl font-bold text-brand-600 dark:text-brand-400">${{ number_format($product->sale_price, 2) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Margen</p>
                        <p class="mt-1 text-xl font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($product->sale_price - $product->purchase_price, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                    <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Stock</span>
                </div>

                <div class="space-y-4">
                    <div class="text-center">
                        <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Stock Actual</p>
                        @if ($product->current_stock <= 0)
                            <p class="mt-1 text-3xl font-bold text-red-600 dark:text-red-400">{{ $product->current_stock }}</p>
                        @elseif ($product->current_stock <= $product->min_stock)
                            <p class="mt-1 text-3xl font-bold text-amber-600 dark:text-amber-400">{{ $product->current_stock }}</p>
                        @else
                            <p class="mt-1 text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $product->current_stock }}</p>
                        @endif
                    </div>
                    <div class="text-center">
                        <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Stock Mínimo</p>
                        <p class="mt-1 text-lg font-bold text-neutral-600 dark:text-neutral-400">{{ $product->min_stock }}</p>
                    </div>
                    @if ($product->current_stock <= $product->min_stock)
                        <div class="rounded-xl bg-amber-50 p-3 text-center text-sm font-medium text-amber-700 dark:bg-amber-900/20 dark:text-amber-400">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                            {{ $product->current_stock <= 0 ? 'Sin stock disponible' : 'Stock por debajo del mínimo' }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                        <i class="fa-solid fa-percent"></i>
                    </div>
                    <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Impuesto</span>
                </div>

                <div class="text-center">
                    @if ($product->has_tax)
                        <p class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">{{ $product->tax_percentage }}%</p>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400">IVA incluido en precio de venta</p>
                    @else
                        <p class="text-lg font-medium text-neutral-500 dark:text-neutral-400">Exento de IVA</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
