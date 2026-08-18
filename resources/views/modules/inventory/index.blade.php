@extends('layouts.app')

@section('title', 'Inventario')

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [['label' => 'Inventario']]])

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Inventario</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Gestiona los productos, precios y stock del almacén.</p>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Total</p>
                    <p class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <i class="fa-solid fa-check-circle text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Activos</p>
                    <p class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Stock Bajo</p>
                    <p class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">{{ $stats['low_stock'] }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                    <i class="fa-solid fa-ban text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Sin Stock</p>
                    <p class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">{{ $stats['out_of_stock'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative flex-1 sm:max-w-xs">
            <i class="fa-solid fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400"></i>
            <input
                type="text"
                id="inventory-search"
                placeholder="Buscar por nombre, código o categoría..."
                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 pl-10 pr-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-200 dark:placeholder:text-neutral-500"
            />
        </div>
        <a href="{{ route('inventory.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700 active:bg-brand-800">
            <i class="fa-solid fa-plus text-xs"></i> Nuevo Producto
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-100 bg-brand-50/60 dark:border-neutral-700 dark:bg-neutral-800/60">
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Producto</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 sm:table-cell dark:text-brand-200">Código</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 md:table-cell dark:text-brand-200">Categoría</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 md:table-cell dark:text-brand-200">Precio Compra</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Precio Venta</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Stock</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Estado</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Acciones</th>
                    </tr>
                </thead>
                <tbody id="inventory-tbody" class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse ($products as $product)
                        <tr class="transition-colors hover:bg-brand-50/40 dark:hover:bg-neutral-800/40" data-search="{{ strtolower($product->name . ' ' . ($product->barcode ?? '') . ' ' . $product->category->name) }}">
                            <td class="px-4 py-3">
                                <div class="font-medium text-neutral-800 dark:text-neutral-200">{{ $product->name }}</div>
                                @if ($product->has_tax)
                                    <span class="text-xs text-neutral-400 dark:text-neutral-500">IVA {{ $product->tax_percentage }}%</span>
                                @endif
                            </td>
                            <td class="hidden px-4 py-3 sm:table-cell font-mono text-xs dark:text-neutral-400">{{ $product->barcode ?? '—' }}</td>
                            <td class="hidden px-4 py-3 md:table-cell dark:text-neutral-300">{{ $product->category->name }}</td>
                            <td class="hidden px-4 py-3 md:table-cell dark:text-neutral-300">${{ number_format($product->purchase_price, 2) }}</td>
                            <td class="px-4 py-3 font-semibold text-neutral-800 dark:text-neutral-100">${{ number_format($product->sale_price, 2) }}</td>
                            <td class="px-4 py-3">
                                @if ($product->current_stock <= 0)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        {{ $product->current_stock }}
                                    </span>
                                @elseif ($product->current_stock <= $product->min_stock)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        {{ $product->current_stock }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        {{ $product->current_stock }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if ($product->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <i class="fa-solid fa-circle text-[6px]"></i> Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                                        <i class="fa-solid fa-circle text-[6px]"></i> Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('inventory.show', $product) }}" class="inline-flex items-center justify-center rounded-lg p-2 text-brand-600 transition-colors hover:bg-brand-50 dark:text-brand-400 dark:hover:bg-neutral-800" title="Ver">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    @if ($product->is_active)
                                        <a href="{{ route('inventory.edit', $product) }}" class="inline-flex items-center justify-center rounded-lg p-2 text-amber-600 transition-colors hover:bg-amber-50 dark:text-amber-400 dark:hover:bg-neutral-800" title="Editar">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        <form method="POST" action="{{ route('inventory.toggle', $product) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="inline-flex items-center justify-center rounded-lg p-2 text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-neutral-800" title="Desactivar"
                                                onclick="return confirm('¿Desactivar este producto?')">
                                                <i class="fa-solid fa-ban text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('inventory.toggle', $product) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="inline-flex items-center justify-center rounded-lg p-2 text-emerald-600 transition-colors hover:bg-emerald-50 dark:text-emerald-400 dark:hover:bg-neutral-800" title="Reactivar">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('inventory.destroy', $product) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="inline-flex items-center justify-center rounded-lg p-2 text-red-600 transition-colors hover:bg-red-50 dark:text-red-400 dark:hover:bg-neutral-800" title="Eliminar"
                                                onclick="return confirm('¿Eliminar este producto permanentemente?')">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-100 text-brand-400 dark:bg-brand-900/30 dark:text-brand-500">
                                        <i class="fa-solid fa-boxes-stacked text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-neutral-700 dark:text-neutral-300">No hay productos</p>
                                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Agrega un nuevo producto para comenzar.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('inventory-search')?.addEventListener('input', function () {
            const query = this.value.toLowerCase();
            document.querySelectorAll('#inventory-tbody tr[data-search]').forEach(function (row) {
                row.style.display = row.dataset.search.includes(query) ? '' : 'none';
            });
        });
    </script>
@endsection
