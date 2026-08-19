@extends('layouts.app')

@section('title', 'Empleados · ' . config('app.name'))

@section('content')
    @php
        $roleLabels = [
            'ADMINISTRATOR' => 'Administrador',
            'CASHIER' => 'Cajero',
            'WAREHOUSE' => 'Almacén',
        ];
        $roleBadges = [
            'ADMINISTRATOR' => 'bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400',
            'CASHIER' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
            'WAREHOUSE' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
        ];
        $stats = [
            [
                'label' => 'Empleados registrados',
                'value' => $employees->count(),
                'sub' => 'Total en el sistema',
                'path' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
            ],
            [
                'label' => 'Empleados activos',
                'value' => $employees->where('is_active', true)->count(),
                'sub' => 'Con acceso al sistema',
                'path' => 'M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
            ],
            [
                'label' => 'Empleados inactivos',
                'value' => $employees->where('is_active', false)->count(),
                'sub' => 'Acceso deshabilitado',
                'path' => 'M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3',
            ],
        ];
    @endphp

    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Empleados'],
    ]])

    <header class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">Empleados</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Gestiona el personal que opera el sistema.</p>
        </div>
    </header>

    <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
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

    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="relative w-full sm:max-w-sm">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400 dark:text-neutral-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" class="stroke-current" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <input
                type="search"
                id="employee-search"
                placeholder="Buscar empleado..."
                autocomplete="off"
                class="w-full rounded-xl border border-neutral-300 bg-white py-2.5 pl-10 pr-4 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
            />
        </div>

        <a
            href="{{ route('employees.create') }}"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 focus-visible:ring-offset-2"
        >
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            Nuevo empleado
        </a>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-neutral-200 text-sm dark:divide-neutral-800">
                <thead class="bg-brand-50/60 dark:bg-neutral-800/60">
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                    <th class="px-4 py-3 sm:px-6">ID</th>
                    <th class="px-4 py-3 sm:px-6">Empleado</th>
                    <th class="hidden px-4 py-3 sm:px-6 md:table-cell">DUI</th>
                    <th class="hidden px-4 py-3 sm:px-6 lg:table-cell">Dirección</th>
                    <th class="hidden px-4 py-3 sm:px-6 md:table-cell">Rol</th>
                    <th class="px-4 py-3 sm:px-6">Estado</th>
                    <th class="hidden px-4 py-3 sm:px-6 lg:table-cell">Creada</th>
                    <th class="px-4 py-3 text-right sm:px-6">Acciones</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @forelse ($employees as $employee)
                    <tr
                        data-employee-row
                        data-employee-search="{{ strtolower(trim($employee->full_name . ' ' . $employee->email . ' ' . ($employee->DUI ?? '') . ' ' . ($employee->address ?? '') . ' ' . ($roleLabels[$employee->roles->first()?->name] ?? $employee->roles->first()?->name ?? ''))) }}"
                        class="{{ $employee->is_active ? 'hover:bg-brand-50/40 dark:hover:bg-neutral-800/50' : 'bg-neutral-50 hover:bg-neutral-100/60 dark:bg-neutral-800/30 dark:hover:bg-neutral-800/60' }}"
                    >
                        <td class="whitespace-nowrap px-4 py-3 text-neutral-500 dark:text-neutral-400 sm:px-6">#{{ $employee->id }}</td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                            <p class="{{ $employee->is_active ? 'font-medium text-neutral-900 dark:text-white' : 'font-medium text-neutral-400 dark:text-neutral-500' }}">{{ $employee->full_name }}</p>
                            <p class="mt-0.5 text-xs text-neutral-400 dark:text-neutral-500">{{ $employee->email }}</p>
                        </td>
                        <td class="hidden whitespace-nowrap px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400 sm:px-6 md:table-cell">
                            {{ $employee->DUI ?? '—' }}
                        </td>
                        <td class="hidden max-w-[200px] truncate px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400 sm:px-6 lg:table-cell">
                            {{ $employee->address ?? '—' }}
                        </td>
                        <td class="hidden whitespace-nowrap px-4 py-3 sm:px-6 md:table-cell">
                            @if ($employee->roles->first())
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $roleBadges[$employee->roles->first()->name] ?? 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400' }}">
                                    {{ $roleLabels[$employee->roles->first()->name] ?? $employee->roles->first()->name }}
                                </span>
                            @else
                                <span class="text-neutral-400 dark:text-neutral-500">—</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                            @if ($employee->is_active)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-2.5 py-0.5 text-xs font-medium text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-neutral-400"></span>
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="hidden whitespace-nowrap px-4 py-3 text-neutral-500 dark:text-neutral-400 sm:px-6 lg:table-cell">
                            {{ $employee->created_at->format('d/m/Y') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right sm:px-6">
                            <div class="flex items-center justify-end gap-1.5">
                                @if (Route::has('employees.show'))
                                    <button
                                        type="button"
                                        title="Ver detalle"
                                        data-view-employee="{{ route('employees.show', $employee) }}"
                                        class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-brand-700 transition-all hover:scale-110 hover:bg-brand-100 hover:shadow-sm dark:text-brand-400 dark:hover:bg-brand-900/40"
                                    >
                                        <i class="fa-solid fa-eye text-sm" aria-hidden="true"></i>
                                    </button>
                                @endif

                                @if (Route::has('employees.edit'))
                                    <a
                                        href="{{ route('employees.edit', $employee) }}"
                                        title="Editar"
                                        class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-blue-600 transition-all hover:scale-110 hover:bg-blue-100 hover:shadow-sm dark:text-blue-400 dark:hover:bg-blue-900/40"
                                    >
                                        <i class="fa-solid fa-pen-to-square text-sm" aria-hidden="true"></i>
                                    </a>
                                @endif

                                @if ($employee->is_active && Route::has('employees.toggle'))
                                    <form method="POST" action="{{ route('employees.toggle', $employee) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            data-disable-employee
                                            title="Deshabilitar"
                                            class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-amber-600 transition-all hover:scale-110 hover:bg-amber-100 hover:shadow-sm dark:text-amber-400 dark:hover:bg-amber-900/40"
                                        >
                                            <i class="fa-solid fa-ban text-sm" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                @endif

                                @if (! $employee->is_active && Route::has('employees.destroy'))
                                    <form method="POST" action="{{ route('employees.destroy', $employee) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            data-delete-employee
                                            title="Eliminar"
                                            class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-lg text-red-600 transition-all hover:scale-110 hover:bg-red-100 hover:shadow-sm dark:text-red-400 dark:hover:bg-red-900/40"
                                        >
                                            <i class="fa-solid fa-trash text-sm" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center sm:px-6">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" class="stroke-current" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-medium text-neutral-500 dark:text-neutral-400">No hay empleados registrados</p>
                            <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">Crea tu primer empleado para empezar.</p>
                        </td>
                    </tr>
                @endforelse

                <tr id="no-results-row" class="hidden">
                    <td colspan="8" class="px-4 py-12 text-center sm:px-6">
                        <p class="text-sm font-medium text-neutral-500 dark:text-neutral-400">Sin resultados</p>
                        <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">No se encontraron empleados para la búsqueda.</p>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div id="employee-modal-container" aria-hidden="true"></div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('employee-search');
            const rows = document.querySelectorAll('[data-employee-row]');
            const noResultsRow = document.getElementById('no-results-row');

            searchInput.addEventListener('input', () => {
                const term = searchInput.value.trim().toLowerCase();
                let visible = 0;

                if (rows.length === 0) {
                    noResultsRow.classList.add('hidden');
                    return;
                }

                rows.forEach((row) => {
                    const show = row.dataset.employeeSearch.includes(term);
                    row.classList.toggle('hidden', !show);
                    if (show) {
                        visible++;
                    }
                });

                noResultsRow.classList.toggle('hidden', visible > 0);
            });

            document.querySelectorAll('[data-disable-employee]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    const form = button.closest('form');

                    Swal.fire({
                        title: '¿Deshabilitar empleado?',
                        text: 'El empleado no podrá acceder al sistema. Puedes volver a activarlo desde la edición.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, deshabilitar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#D76AA2',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            document.querySelectorAll('[data-delete-employee]').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    const form = button.closest('form');

                    Swal.fire({
                        title: '¿Eliminar empleado?',
                        text: 'Esta acción eliminará al empleado de forma permanente.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#dc2626',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            const modalContainer = document.getElementById('employee-modal-container');
            const modalTrigger = { element: null };

            const closeEmployeeModal = () => {
                modalContainer.innerHTML = '';
                modalContainer.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
                modalTrigger.element?.focus();
            };

            document.querySelectorAll('[data-view-employee]').forEach((button) => {
                button.addEventListener('click', async () => {
                    modalTrigger.element = document.activeElement;
                    modalContainer.innerHTML = '';

                    try {
                        const response = await fetch(button.dataset.viewEmployee, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'text/html',
                            },
                        });

                        if (!response.ok) {
                            return;
                        }

                        modalContainer.innerHTML = await response.text();

                        const modal = modalContainer.querySelector('[data-employee-modal]');
                        if (!modal) {
                            closeEmployeeModal();
                            return;
                        }

                        modalContainer.setAttribute('aria-hidden', 'false');
                        document.body.classList.add('overflow-hidden');

                        const focusable = modal.querySelectorAll('button, [href], [tabindex]:not([tabindex="-1"])');
                        const firstFocusable = focusable[0];
                        const lastFocusable = focusable[focusable.length - 1];
                        firstFocusable?.focus();

                        modal.addEventListener('click', (event) => {
                            if (event.target === modal.querySelector('[data-modal-backdrop]')) {
                                closeEmployeeModal();
                            }
                        });

                        modal.addEventListener('keydown', (event) => {
                            if (event.key === 'Escape') {
                                closeEmployeeModal();
                                return;
                            }

                            if (event.key === 'Tab' && focusable.length > 0) {
                                if (event.shiftKey && document.activeElement === firstFocusable) {
                                    event.preventDefault();
                                    lastFocusable.focus();
                                } else if (!event.shiftKey && document.activeElement === lastFocusable) {
                                    event.preventDefault();
                                    firstFocusable.focus();
                                }
                            }
                        });

                        modal.querySelectorAll('[data-modal-close]').forEach((element) => {
                            element.addEventListener('click', closeEmployeeModal);
                        });
                    } catch (error) {
                        closeEmployeeModal();
                    }
                });
            });
        });
    </script>
@endsection
