@extends('layouts.app')

@section('title', 'Devoluciones / Notas de Crédito')

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [['label' => 'Devoluciones / Notas de Crédito']]])

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Devoluciones / Notas de Crédito</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Historial de devoluciones y notas de crédito emitidas.</p>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                    <i class="fa-solid fa-rotate-left text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Total Devoluciones</p>
                    <p class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">{{ $stats['total_returns'] }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                    <i class="fa-solid fa-dollar-sign text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Total Reembolsado</p>
                    <p class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">${{ number_format($stats['total_refunded'], 2) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <i class="fa-solid fa-calendar-day text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Hoy</p>
                    <p class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">{{ $stats['today_returns'] }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                    <i class="fa-solid fa-coins text-lg"></i>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Reembolso Hoy</p>
                    <p class="text-2xl font-bold text-neutral-800 dark:text-neutral-100">${{ number_format($stats['today_refunded'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative w-full sm:max-w-sm">
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400 dark:text-neutral-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" class="stroke-current" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input
                    type="search"
                    id="returns-search"
                    placeholder="Buscar por venta, empleado..."
                    autocomplete="off"
                    class="w-full rounded-xl border border-neutral-300 bg-white py-2.5 pl-10 pr-4 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                />
            </div>

            <input
                type="date"
                id="filter-date-from"
                aria-label="Fecha desde"
                class="rounded-xl border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
            />

            <input
                type="date"
                id="filter-date-to"
                aria-label="Fecha hasta"
                class="rounded-xl border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
            />

            <select
                id="filter-employee"
                aria-label="Filtrar por empleado"
                class="rounded-xl border border-neutral-300 bg-white px-3 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
            >
                <option value="">Todos los empleados</option>
            </select>
        </div>

        @can('returns_create')
            <a
                href="{{ route('returns.create') }}"
                class="ml-auto inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 focus-visible:ring-offset-2"
            >
                <i class="fa-solid fa-plus" aria-hidden="true"></i> Nueva Devolución
            </a>
        @endcan
    </div>

    <div class="overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" role="grid">
                <thead>
                    <tr class="border-b border-brand-100 bg-brand-50/60 dark:border-neutral-700 dark:bg-neutral-800/60">
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">ID</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Fecha</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 sm:table-cell dark:text-brand-200">Venta</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 md:table-cell dark:text-brand-200">Empleado</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 lg:table-cell dark:text-brand-200">Productos</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Total</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Acciones</th>
                    </tr>
                </thead>
                <tbody id="returns-tbody" class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse ($returns as $return)
                        <tr class="transition-colors hover:bg-brand-50/40 dark:hover:bg-neutral-800/40"
                            data-search="{{ strtolower('#' . $return->id . ' ' . $return->sale->ticket_number . ' ' . $return->user->full_name) }}"
                            data-employee="{{ $return->user->full_name }}"
                            data-date="{{ $return->created_at?->format('Y-m-d') }}">
                            <td class="px-4 py-3 font-medium text-neutral-800 dark:text-neutral-200">#{{ $return->id }}</td>
                            <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $return->created_at->format('d/m/Y H:i') }}</td>
                            <td class="hidden px-4 py-3 sm:table-cell dark:text-neutral-300">{{ $return->sale->ticket_number }}</td>
                            <td class="hidden px-4 py-3 md:table-cell dark:text-neutral-300">{{ $return->user->full_name }}</td>
                            <td class="hidden px-4 py-3 lg:table-cell dark:text-neutral-300">{{ $return->details->count() }} producto(s)</td>
                            <td class="px-4 py-3 font-semibold text-red-600 dark:text-red-400">-${{ number_format($return->total_returned, 2) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end">
                                    <a href="{{ route('returns.show', $return) }}" title="Ver detalle" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-brand-700 transition-all hover:scale-110 hover:bg-brand-100 hover:shadow-sm dark:text-brand-400 dark:hover:bg-brand-900/40">
                                        <i class="fa-solid fa-eye text-sm" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-100 text-brand-400 dark:bg-brand-900/30 dark:text-brand-500">
                                        <i class="fa-solid fa-rotate-left text-2xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-neutral-700 dark:text-neutral-300">No hay devoluciones</p>
                                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Aún no se han registrado devoluciones.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('partials.pagination', ['paginator' => $returns])
    </div>
@endsection

@section('scripts')
    <script>
        (function () {
            var employeeSelect = document.getElementById('filter-employee');
            var seen = {};
            document.querySelectorAll('#returns-tbody tr[data-search]').forEach(function (row) {
                var name = row.dataset.employee;
                if (name && !seen[name]) {
                    seen[name] = true;
                    var opt = document.createElement('option');
                    opt.value = name;
                    opt.textContent = name;
                    employeeSelect.appendChild(opt);
                }
            });

            var searchInput = document.getElementById('returns-search');
            var dateFrom = document.getElementById('filter-date-from');
            var dateTo = document.getElementById('filter-date-to');

            function applyFilters() {
                var query = searchInput ? searchInput.value.toLowerCase() : '';
                var from = dateFrom ? dateFrom.value : '';
                var to = dateTo ? dateTo.value : '';
                var emp = employeeSelect ? employeeSelect.value : '';

                document.querySelectorAll('#returns-tbody tr[data-search]').forEach(function (row) {
                    var match = true;
                    if (query && !row.dataset.search.includes(query)) match = false;
                    if (emp && row.dataset.employee !== emp) match = false;
                    if (from && row.dataset.date < from) match = false;
                    if (to && row.dataset.date > to) match = false;
                    row.style.display = match ? '' : 'none';
                });
            }

            searchInput && searchInput.addEventListener('input', applyFilters);
            dateFrom && dateFrom.addEventListener('change', applyFilters);
            dateTo && dateTo.addEventListener('change', applyFilters);
            employeeSelect && employeeSelect.addEventListener('change', applyFilters);
        })();
    </script>
@endsection