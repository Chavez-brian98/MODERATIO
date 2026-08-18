@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [['label' => 'Reportes']]])

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Reportes</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Resumen de ventas, productos y rendimiento del negocio.</p>
    </div>

    <form method="GET" class="mb-8">
        <div class="flex flex-wrap items-end gap-3 rounded-2xl border border-brand-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div>
                <label for="start_date" class="mb-1 block text-xs font-medium text-neutral-500 dark:text-neutral-400">Desde</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                    class="rounded-xl border border-brand-200 bg-white py-2 px-3 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
            </div>
            <div>
                <label for="end_date" class="mb-1 block text-xs font-medium text-neutral-500 dark:text-neutral-400">Hasta</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                    class="rounded-xl border border-brand-200 bg-white py-2 px-3 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200" />
            </div>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700">
                <i class="fa-solid fa-filter text-xs"></i> Filtrar
            </button>
            <a href="{{ route('reports.index') }}" class="text-sm text-neutral-500 hover:text-brand-700 dark:text-neutral-400 dark:hover:text-brand-400">Limpiar</a>
        </div>
    </form>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Ventas Totales</p>
            <p class="mt-1 text-2xl font-bold text-neutral-800 dark:text-neutral-100">${{ number_format($stats['total_sales'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Transacciones</p>
            <p class="mt-1 text-2xl font-bold text-neutral-800 dark:text-neutral-100">{{ $stats['total_transactions'] }}</p>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Ticket Promedio</p>
            <p class="mt-1 text-2xl font-bold text-neutral-800 dark:text-neutral-100">${{ number_format($stats['avg_ticket'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Devoluciones</p>
            <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">${{ number_format($stats['total_returns'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Ventas Netas</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($stats['net_sales'], 2) }}</p>
        </div>
    </div>

    <div class="mb-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-4 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Ventas por Método de Pago</span>
            </div>
            <div class="space-y-3">
                @forelse ($salesByMethod as $method => $data)
                    @php
                        $pct = $stats['total_sales'] > 0 ? ($data['total'] / $stats['total_sales']) * 100 : 0;
                        $color = match($method) { 'CASH' => 'emerald', 'CARD' => 'blue', default => 'violet' };
                        $label = match($method) { 'CASH' => 'Efectivo', 'CARD' => 'Tarjeta', default => 'Transferencia' };
                    @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between text-sm">
                            <span class="font-medium text-neutral-700 dark:text-neutral-300">{{ $label }}</span>
                            <span class="text-neutral-500 dark:text-neutral-400">{{ $data['count'] }} — ${{ number_format($data['total'], 2) }}</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                            <div class="h-full rounded-full bg-{{ $color }}-500 transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-neutral-500 dark:text-neutral-400">Sin datos en el período seleccionado.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-4 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Ventas por Día</span>
            </div>
            @if (count($dailySales) > 0)
                <div class="space-y-2">
                    @php $maxDaily = max($dailySales); @endphp
                    @foreach ($dailySales as $date => $total)
                        @php $pct = $maxDaily > 0 ? ($total / $maxDaily) * 100 : 0; @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-24 shrink-0 text-xs text-neutral-500 dark:text-neutral-400">{{ \Carbon\Carbon::parse($date)->format('d/m') }}</span>
                            <div class="h-4 flex-1 overflow-hidden rounded bg-neutral-100 dark:bg-neutral-800">
                                <div class="h-full rounded bg-brand-400 dark:bg-brand-500" style="width: {{ $pct }}%"></div>
                            </div>
                            <span class="w-20 text-right text-xs font-medium text-neutral-700 dark:text-neutral-300">${{ number_format($total, 0) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Sin datos en el período seleccionado.</p>
            @endif
        </div>
    </div>

    <div class="mb-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-4 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Top 10 Productos</span>
            </div>
            @if (count($topProducts) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-brand-100 dark:border-neutral-700">
                                <th class="pb-2 font-semibold text-brand-800 dark:text-brand-200">#</th>
                                <th class="pb-2 font-semibold text-brand-800 dark:text-brand-200">Producto</th>
                                <th class="pb-2 text-right font-semibold text-brand-800 dark:text-brand-200">Cant.</th>
                                <th class="pb-2 text-right font-semibold text-brand-800 dark:text-brand-200">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @foreach ($topProducts as $i => $product)
                                <tr>
                                    <td class="py-2 text-neutral-500 dark:text-neutral-400">{{ $i + 1 }}</td>
                                    <td class="py-2 font-medium text-neutral-800 dark:text-neutral-200">{{ $product['name'] }}</td>
                                    <td class="py-2 text-right dark:text-neutral-300">{{ $product['quantity'] }}</td>
                                    <td class="py-2 text-right font-semibold text-neutral-800 dark:text-neutral-100">${{ number_format($product['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Sin datos en el período seleccionado.</p>
            @endif
        </div>

        <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-4 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Ventas por Categoría</span>
            </div>
            @if (count($categorySales) > 0)
                @php $maxCat = max(array_column($categorySales, 'total')); @endphp
                <div class="space-y-3">
                    @foreach ($categorySales as $cat)
                        @php $pct = $maxCat > 0 ? ($cat['total'] / $maxCat) * 100 : 0; @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-neutral-700 dark:text-neutral-300">{{ $cat['name'] }}</span>
                                <span class="text-neutral-500 dark:text-neutral-400">${{ number_format($cat['total'], 2) }}</span>
                            </div>
                            <div class="h-2 overflow-hidden rounded-full bg-neutral-100 dark:bg-neutral-800">
                                <div class="h-full rounded-full bg-violet-500 transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-neutral-500 dark:text-neutral-400">Sin datos en el período seleccionado.</p>
            @endif
        </div>
    </div>
@endsection
