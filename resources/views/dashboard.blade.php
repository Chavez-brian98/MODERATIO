@extends('layouts.app')

@section('title', 'Dashboard · ' . config('app.name'))

@section('content')
    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">Dashboard</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Bienvenido — aquí tienes el resumen del día.</p>
        </div>
        <div class="text-left sm:text-right">
            <p class="text-sm text-neutral-500 dark:text-neutral-400">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
            @if ($openCashRegister)
                <p class="mt-1.5 inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                    Caja abierta
                </p>
            @else
                <p class="mt-1.5 inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-neutral-400"></span>
                    Sin caja abierta
                </p>
            @endif
        </div>
    </header>

    <section class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @php
            $stats = [
                [
                    'label' => 'Ventas de hoy',
                    'value' => '$' . number_format($salesToday['total'], 2),
                    'sub' => $salesToday['count'] . ' transacciones',
                    'path' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                ],
                [
                    'label' => 'Ventas del mes',
                    'value' => '$' . number_format($monthSalesTotal, 2),
                    'sub' => now()->format('F Y'),
                    'path' => 'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z',
                ],
                [
                    'label' => 'Productos bajo stock',
                    'value' => $lowStockCount,
                    'sub' => 'Necesitan reposición',
                    'path' => 'M20.25 7.5l-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m10.5 0V5.25a2.25 2.25 0 0 0-2.25-2.25h-3a2.25 2.25 0 0 0-2.25 2.25v2.25m-6 0h18',
                ],
                [
                    'label' => 'Valor del inventario',
                    'value' => '$' . number_format($inventoryValue, 2),
                    'sub' => 'Al costo de compra',
                    'path' => 'M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125S7.444 2.25 12 2.25s8.25 1.847 8.25 4.125Zm0 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125M20.25 6.375V21c0 2.278-3.694 4.125-8.25 4.125S3.75 23.278 3.75 21V6.375',
                ],
            ];
        @endphp

        @foreach ($stats as $stat)
            <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="{{ $stat['path'] }}" class="stroke-current" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="truncate text-sm text-neutral-500 dark:text-neutral-400">{{ $stat['label'] }}</p>
                        <p class="text-xl font-semibold tracking-tight text-neutral-900 dark:text-white sm:text-2xl">{{ $stat['value'] }}</p>
                        <p class="text-xs text-neutral-400 dark:text-neutral-500">{{ $stat['sub'] }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    <section class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900 md:col-span-2 lg:col-span-2">
            <h2 class="text-lg font-semibold tracking-tight text-neutral-900 dark:text-white">Ventas — últimos 14 días</h2>
            <div id="sales-trend-chart" class="mt-4 min-h-[260px] sm:min-h-[320px]"></div>
        </div>

        <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
            <h2 class="text-lg font-semibold tracking-tight text-neutral-900 dark:text-white">Métodos de pago</h2>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Últimos 30 días</p>
            <div id="payment-methods-chart" class="mt-4 min-h-[260px] sm:min-h-[320px]"></div>
        </div>
    </section>

    <section class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900 md:col-span-2 lg:col-span-2">
            <h2 class="text-lg font-semibold tracking-tight text-neutral-900 dark:text-white">Productos más vendidos</h2>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Por unidades vendidas</p>
            <div id="top-products-chart" class="mt-4 min-h-[260px] sm:min-h-[320px]"></div>
        </div>

        <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
            <h2 class="text-lg font-semibold tracking-tight text-neutral-900 dark:text-white">Bajo stock</h2>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Productos que necesitan reposición</p>
            <ul class="mt-4 space-y-3">
                @forelse ($lowStockProducts as $product)
                    <li class="flex items-center justify-between gap-3 rounded-xl border border-neutral-100 bg-neutral-50 px-4 py-3 dark:border-neutral-800 dark:bg-neutral-800/50">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-neutral-900 dark:text-white">{{ $product->name }}</p>
                            <p class="truncate text-xs text-neutral-500 dark:text-neutral-400">{{ $product->category?->name }}</p>
                        </div>
                        <p class="shrink-0 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-900/40 dark:text-red-400">
                            {{ $product->current_stock }} / {{ $product->min_stock }}
                        </p>
                    </li>
                @empty
                    <li class="rounded-xl border border-neutral-100 bg-neutral-50 px-4 py-3 text-sm text-neutral-500 dark:border-neutral-800 dark:bg-neutral-800/50 dark:text-neutral-400">
                        No hay productos con bajo stock.
                    </li>
                @endforelse
            </ul>
        </div>
    </section>
@endsection

@php
    $dashboardChartData = [
        'salesTrend' => [
            'categories' => $salesTrend['dates'],
            'series' => $salesTrend['totals'],
        ],
        'paymentMethods' => $paymentMethods,
        'topProducts' => $topProducts,
    ];
@endphp

@section('scripts')
    <script>
        window.dashboardChartData = @json($dashboardChartData);
    </script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/js/dashboard.js'])
    @endif
@endsection
