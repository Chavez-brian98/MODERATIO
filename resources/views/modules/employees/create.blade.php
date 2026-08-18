@extends('layouts.app')

@section('title', 'Nuevo empleado · ' . config('app.name'))

@section('content')
    @php
        $roleLabels = [
            'ADMINISTRATOR' => 'Administrador',
            'CASHIER' => 'Cajero',
            'WAREHOUSE' => 'Almacén',
        ];
    @endphp

    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Empleados', 'url' => route('employees.index')],
        ['label' => 'Nuevo empleado'],
    ]])

    <header class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400 sm:flex">
                <i class="fa-solid fa-user-plus text-lg" aria-hidden="true"></i>
            </div>
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">Nuevo empleado</h1>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Da de alta a una persona para que opere el sistema.</p>
            </div>
        </div>
        <a
            href="{{ route('employees.index') }}"
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
                <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-700 dark:text-neutral-300">Información del empleado</h2>
            </div>

            <form method="POST" action="{{ route('employees.store') }}" class="space-y-6 px-6 py-6 sm:px-8">
                @csrf

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="full_name" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Nombre completo <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="full_name"
                            name="full_name"
                            type="text"
                            value="{{ old('full_name') }}"
                            required
                            placeholder="Ej. Ana María López"
                            autocomplete="name"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                        />
                        @error('full_name')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Correo electrónico <span class="text-red-500">*</span>
                        </label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400 dark:text-neutral-500">
                                <i class="fa-solid fa-envelope text-sm" aria-hidden="true"></i>
                            </span>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                placeholder="ejemplo@correo.com"
                                autocomplete="email"
                                class="block w-full rounded-lg border border-neutral-300 bg-white py-2.5 pl-10 pr-3.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                            />
                        </div>
                        <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">Será el correo para iniciar sesión.</p>
                        @error('email')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="address" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Dirección
                        </label>
                        <input
                            id="address"
                            name="address"
                            type="text"
                            value="{{ old('address') }}"
                            placeholder="Ej. Colonia San Benito, San Salvador"
                            autocomplete="street-address"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                        />
                        @error('address')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="DUI" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            DUI
                        </label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400 dark:text-neutral-500">
                                <i class="fa-solid fa-id-card text-sm" aria-hidden="true"></i>
                            </span>
                            <input
                                id="DUI"
                                name="DUI"
                                type="text"
                                value="{{ old('DUI') }}"
                                placeholder="00000000-0"
                                maxlength="10"
                                autocomplete="off"
                                class="block w-full rounded-lg border border-neutral-300 bg-white py-2.5 pl-10 pr-3.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                            />
                        </div>
                        @error('DUI')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="birthday" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Fecha de nacimiento
                        </label>
                        <input
                            id="birthday"
                            name="birthday"
                            type="date"
                            value="{{ old('birthday') }}"
                            max="{{ date('Y-m-d') }}"
                            autocomplete="bday"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                        />
                        @error('birthday')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            minlength="8"
                            placeholder="Mínimo 8 caracteres"
                            autocomplete="new-password"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:placeholder:text-neutral-500"
                        />
                        <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">Se almacena encriptada. El empleado podrá usarla para entrar al sistema.</p>
                        @error('password')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="role_id" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                        Rol <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="role_id"
                        name="role_id"
                        required
                        class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                    >
                        <option value="">Selecciona un rol...</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                                {{ $roleLabels[$role->name] ?? $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">Determina los permisos de acceso dentro del sistema.</p>
                    @error('role_id')
                        <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between gap-4 rounded-xl border border-brand-200 bg-brand-50/40 px-4 py-3.5 dark:border-neutral-800 dark:bg-neutral-800/40">
                    <div>
                        <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">Empleado activo</p>
                        <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">Los empleados inactivos no pueden iniciar sesión.</p>
                    </div>
                    <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="peer sr-only" />
                        <div class="peer relative h-6 w-11 rounded-full bg-neutral-300 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:bg-brand-600 peer-checked:after:translate-x-5 dark:bg-neutral-600"></div>
                    </label>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-brand-100 pt-6 dark:border-neutral-800 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('employees.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-neutral-300 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
                    >
                        Cancelar
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 focus-visible:ring-offset-2"
                    >
                        <i class="fa-solid fa-user-plus text-sm" aria-hidden="true"></i>
                        Crear empleado
                    </button>
                </div>
            </form>
        </div>

        <aside class="space-y-6">
            <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-neutral-900 dark:text-white">
                    <i class="fa-solid fa-user-shield text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                    Roles disponibles
                </h3>
                <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">{{ $roles->count() }} {{ $roles->count() === 1 ? 'rol definido' : 'roles definidos' }} en el sistema.</p>

                @if ($roles->isNotEmpty())
                    <ul class="mt-4 space-y-3">
                        @foreach ($roles as $role)
                            <li class="flex items-start gap-3 text-sm">
                                <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                                    <i class="fa-solid fa-shield-halved text-xs" aria-hidden="true"></i>
                                </span>
                                <span class="min-w-0">
                                    <span class="block font-medium text-neutral-800 dark:text-neutral-200">{{ $roleLabels[$role->name] ?? $role->name }}</span>
                                    @if ($role->description)
                                        <span class="block text-xs text-neutral-500 dark:text-neutral-400">{{ $role->description }}</span>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-4 rounded-xl bg-neutral-50 px-4 py-3 text-xs text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        Aún no hay roles configurados.
                    </p>
                @endif
            </div>

            <div class="rounded-2xl border border-brand-200 bg-brand-50/60 p-6 shadow-sm dark:border-neutral-800 dark:bg-neutral-800/40">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-neutral-900 dark:text-white">
                    <i class="fa-solid fa-lightbulb text-amber-500" aria-hidden="true"></i>
                    Consejos
                </h3>
                <ul class="mt-4 space-y-3 text-sm text-neutral-600 dark:text-neutral-400">
                    <li class="flex gap-2.5">
                        <i class="fa-solid fa-circle-check mt-0.5 text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                        Usa el nombre completo tal como aparece en documentos oficiales.
                    </li>
                    <li class="flex gap-2.5">
                        <i class="fa-solid fa-circle-check mt-0.5 text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                        Asigna un rol acorde a las funciones de la persona.
                    </li>
                    <li class="flex gap-2.5">
                        <i class="fa-solid fa-circle-check mt-0.5 text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                        Desactiva (no elimines) empleados que ya no operen el sistema.
                    </li>
                </ul>
            </div>
        </aside>
    </div>
@endsection
