@extends('layouts.app')

@section('title', 'Editar Caja #' . $register->id)

@section('content')
    @php
        $isClosed = $register->status === 'CLOSED';
    @endphp

    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Caja / Arqueo', 'url' => route('cash-register.index')],
        ['label' => 'Caja #' . $register->id, 'url' => route('cash-register.show', $register)],
        ['label' => 'Editar'],
    ]])

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Editar Caja #{{ $register->id }}</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Corrige errores de captura en los datos de la caja.</p>
        </div>
        <div class="flex items-center gap-3">
            @if ($isClosed)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                    <i class="fa-solid fa-circle text-[6px]"></i> Cerrada
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <i class="fa-solid fa-circle text-[6px]"></i> Abierta
                </span>
            @endif
            @can('cash_registers_edit')
                <form method="POST" action="{{ route('cash-register.toggle-status', $register) }}" data-swal-confirm="{{ $isClosed ? 'La caja fue cerrada previamente. ¿Estás seguro de que deseas abrirla de nuevo?' : 'La caja se marcará como cerrada.' }}" data-swal-confirm-title="{{ $isClosed ? '¿Reabrir caja?' : '¿Cerrar caja?' }}" data-swal-confirm-icon="{{ $isClosed ? 'warning' : 'question' }}" data-swal-confirm-button="{{ $isClosed ? 'Sí, reabrir' : 'Sí, cerrar' }}" data-swal-confirm-color="{{ $isClosed ? 'warning' : 'brand' }}">
                    @csrf
                    @method('PATCH')
                    @if ($isClosed)
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 shadow-sm transition-colors hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400 dark:hover:bg-emerald-900/40">
                            <i class="fa-solid fa-lock-open text-xs"></i> Reabrir Caja
                        </button>
                    @else
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 shadow-sm transition-colors hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40">
                            <i class="fa-solid fa-lock text-xs"></i> Cerrar Caja
                        </button>
                    @endif
                </form>
            @endcan
            <a href="{{ route('cash-register.show', $register) }}" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
                <i class="fa-solid fa-arrow-left text-xs"></i> Volver
            </a>
        </div>
    </div>

    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('cash-register.update', $register) }}" class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            @csrf
            @method('PUT')

            <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isClosed ? 'bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400' : 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' }}">
                    <i class="{{ $isClosed ? 'fa-solid fa-pen-to-square' : 'fa-solid fa-lock-open' }}"></i>
                </div>
                <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">{{ $isClosed ? 'Datos del cierre' : 'Datos de la caja abierta' }}</span>
            </div>

            @if ($isClosed)
                <div class="mb-6 space-y-1.5 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm dark:border-blue-800 dark:bg-blue-950/30">
                    <p class="font-medium text-blue-800 dark:text-blue-300"><i class="fa-solid fa-circle-info mr-1.5"></i>Esta caja ya está cerrada</p>
                    <p class="text-xs leading-relaxed text-blue-700/80 dark:text-blue-400/80">
                        Al guardar, el monto teórico se recalcula con el nuevo monto de apertura y las ventas registradas (apertura + ventas en efectivo), y la diferencia se actualiza automáticamente.
                    </p>
                </div>
            @endif

            <div class="space-y-5">
                <div>
                    <label for="opening_amount" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Monto de Apertura <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">$</span>
                        <input
                            type="number"
                            name="opening_amount"
                            id="opening_amount"
                            value="{{ old('opening_amount', $register->opening_amount) }}"
                            placeholder="0.00"
                            step="0.01"
                            min="0"
                            required
                            class="w-full rounded-xl border border-brand-200 bg-white py-2.5 pl-8 pr-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                        />
                    </div>
                    @error('opening_amount')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="shift" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Turno</label>
                        <select
                            name="shift"
                            id="shift"
                            class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                        >
                            <option value="">Sin turno asignado</option>
                            <option value="MORNING" @selected(old('shift', $register->shift) === 'MORNING')>Mañana</option>
                            <option value="AFTERNOON" @selected(old('shift', $register->shift) === 'AFTERNOON')>Tarde</option>
                            <option value="NIGHT" @selected(old('shift', $register->shift) === 'NIGHT')>Noche</option>
                        </select>
                        @error('shift')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="responsible_id" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Encargado de caja</label>
                        <select
                            name="responsible_id"
                            id="responsible_id"
                            class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                        >
                            <option value="">Sin encargado asignado</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected((string) old('responsible_id', $register->responsible_id) === (string) $employee->id)>
                                    {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('responsible_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">Solo se listan usuarios activos con acceso al POS.</p>
                    </div>
                </div>

                @if ($isClosed)                    <div>
                        <label for="actual_closing_amount" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Monto Real Contado al Cerrar <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">$</span>
                            <input
                                type="number"
                                name="actual_closing_amount"
                                id="actual_closing_amount"
                                value="{{ old('actual_closing_amount', $register->actual_closing_amount) }}"
                                placeholder="0.00"
                                step="0.01"
                                min="0"
                                required
                                class="w-full rounded-xl border border-brand-200 bg-white py-2.5 pl-8 pr-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                            />
                        </div>
                        <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">Teórico actual con el monto de apertura indicado: <strong class="text-brand-600 dark:text-brand-400">${{ number_format($theoretical, 2) }}</strong></p>
                        @error('actual_closing_amount')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div>
                    <label for="closing_notes" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Descripción / Observaciones</label>
                    <textarea
                        name="closing_notes"
                        id="closing_notes"
                        rows="3"
                        maxlength="500"
                        placeholder="Describe cualquier inconveniente registrado al abrir o cerrar la caja (opcional)"
                        class="w-full rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 dark:placeholder:text-neutral-500"
                    >{{ old('closing_notes', $register->closing_notes) }}</textarea>
                    @error('closing_notes')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t border-brand-100 pt-5 dark:border-neutral-700">
                <a href="{{ route('cash-register.show', $register) }}" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700 active:bg-brand-800">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
@endsection
