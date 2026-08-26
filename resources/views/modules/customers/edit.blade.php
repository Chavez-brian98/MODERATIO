@extends('layouts.app')

@section('title', 'Editar cliente · ' . config('app.name'))

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Clientes', 'url' => route('customers.index')],
        ['label' => 'Editar cliente'],
    ]])

    @php
        $typeLabels = ['REGULAR' => 'Regular', 'FREQUENT' => 'Frecuente', 'WHOLESALER' => 'Mayorista'];
    @endphp

    <header class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400 sm:flex">
                <i class="fa-solid fa-user-pen text-lg" aria-hidden="true"></i>
            </div>
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">Editar cliente</h1>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ $customer->first_name }} {{ $customer->last_name }}</p>
            </div>
        </div>
        <a
            href="{{ route('customers.index') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
        >
            <i class="fa-solid fa-arrow-left text-sm" aria-hidden="true"></i>
            Volver
        </a>
    </header>

    <div class="mt-6 grid items-start gap-6 lg:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900 lg:col-span-2">
            <div class="flex items-center gap-2 border-b border-brand-100 bg-brand-50/40 px-6 py-4 sm:px-8 dark:border-neutral-800 dark:bg-neutral-800/40">
                <i class="fa-solid fa-circle-info text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-700 dark:text-neutral-300">Información del cliente</h2>
            </div>

            <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-6 px-6 py-6 sm:px-8">
                @csrf
                @method('PUT')

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Nombres <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="first_name"
                            name="first_name"
                            type="text"
                            value="{{ old('first_name', $customer->first_name) }}"
                            required
                            maxlength="100"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                        />
                        @error('first_name')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Apellidos
                        </label>
                        <input
                            id="last_name"
                            name="last_name"
                            type="text"
                            value="{{ old('last_name', $customer->last_name) }}"
                            maxlength="100"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                        />
                        @error('last_name')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tax_id" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            NIT / DUI
                        </label>
                        <input
                            id="tax_id"
                            name="tax_id"
                            type="text"
                            value="{{ old('tax_id', $customer->tax_id) }}"
                            maxlength="20"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                        />
                        @error('tax_id')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Teléfono
                        </label>
                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            value="{{ old('phone', $customer->phone) }}"
                            maxlength="20"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                        />
                        @error('phone')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Correo electrónico
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $customer->email) }}"
                            maxlength="100"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                        />
                        @error('email')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_type" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Tipo de cliente <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="customer_type"
                            name="customer_type"
                            required
                            class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                        >
                            @php
                                $selectedType = old('customer_type', $customer->customer_type);
                            @endphp
                            @foreach ($typeLabels as $value => $label)
                                <option value="{{ $value }}" {{ $selectedType === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('customer_type')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Dirección
                    </label>
                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        maxlength="500"
                        class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                    >{{ old('address', $customer->address) }}</textarea>
                    @error('address')
                        <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between gap-4 rounded-xl border border-brand-200 bg-brand-50/40 px-4 py-3.5 dark:border-neutral-800 dark:bg-neutral-800/40">
                    <div>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">Cliente activo</p>
                        <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">Los clientes inactivos no se pueden asignar a nuevas ventas.</p>
                    </div>
                    <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $customer->is_active)) class="peer sr-only" />
                        <div class="peer relative h-6 w-11 rounded-full bg-neutral-300 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:bg-brand-600 peer-checked:after:translate-x-5 dark:bg-neutral-600"></div>
                    </label>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-brand-100 pt-6 dark:border-neutral-800 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('customers.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                    >
                        Cancelar
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 focus-visible:ring-offset-2"
                    >
                        <i class="fa-solid fa-floppy-disk text-sm" aria-hidden="true"></i>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-neutral-900 dark:text-white">
                    <i class="fa-solid fa-receipt text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                    Historial
                </h3>
                <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">Compras registradas</p>
                <p class="text-2xl font-bold text-neutral-900 dark:text-white">{{ $customer->sales_count ?? $customer->sales()->count() }}</p>
                <p class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">Cliente desde el {{ $customer->created_at->format('d/m/Y') }}.</p>
            </div>
        </aside>
    </div>
@endsection
