@extends('layouts.app')

@section('title', 'Abrir Caja')

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Caja / Arqueo', 'url' => route('cash-register.index')],
        ['label' => 'Abrir Caja'],
    ]])

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Abrir Caja</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Registra el monto de apertura y turno para iniciar sesión de caja.</p>
        </div>
        <a href="{{ route('cash-register.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
            <i class="fa-solid fa-arrow-left text-xs"></i> Volver
        </a>
    </div>

    <div class="mx-auto max-w-2xl">
        <form method="POST" action="{{ route('cash-register.store') }}" class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            @csrf

            <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <i class="fa-solid fa-lock-open"></i>
                </div>
                <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Datos de Apertura</span>
            </div>

            <div class="space-y-5">
                <div>
                    <label for="opening_amount" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Monto de Apertura <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">$</span>
                        <input
                            type="number"
                            name="opening_amount"
                            id="opening_amount"
                            value="{{ old('opening_amount', '0.00') }}"
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

                <div>
                    <label for="shift" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Turno</label>
                    <select
                        name="shift"
                        id="shift"
                        class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                    >
                        <option value="">Sin turno asignado</option>
                        <option value="MORNING" {{ old('shift') === 'MORNING' ? 'selected' : '' }}>Mañana</option>
                        <option value="AFTERNOON" {{ old('shift') === 'AFTERNOON' ? 'selected' : '' }}>Tarde</option>
                        <option value="NIGHT" {{ old('shift') === 'NIGHT' ? 'selected' : '' }}>Noche</option>
                    </select>
                    @error('shift')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 border-t border-brand-100 pt-5 dark:border-neutral-700">
                <a href="{{ route('cash-register.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700 active:bg-brand-800">
                    <i class="fa-solid fa-lock-open text-xs"></i> Abrir Caja
                </button>
            </div>
        </form>
    </div>
@endsection
