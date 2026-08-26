@extends('layouts.app')

@section('title', 'Mi perfil · ' . config('app.name'))

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Mi perfil'],
    ]])

    <header class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400 sm:flex">
                <i class="fa-solid fa-user text-lg" aria-hidden="true"></i>
            </div>
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white">Mi perfil</h1>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Administra tu foto de perfil y tu contraseña.</p>
            </div>
        </div>
    </header>

    <div class="mt-6 grid items-start gap-6 lg:grid-cols-3">
        {{-- Foto de perfil --}}
        <div class="overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-center gap-2 border-b border-brand-100 bg-brand-50/40 px-6 py-4 dark:border-neutral-800 dark:bg-neutral-800/40">
                <i class="fa-solid fa-camera text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-700 dark:text-neutral-300">Foto de perfil</h2>
            </div>

            <div class="flex flex-col items-center gap-5 px-6 py-8 sm:px-8">
                <div class="relative">
                    @if ($user->photoUrl())
                        <img
                            src="{{ $user->photoUrl() }}"
                            alt="Foto de {{ $user->full_name }}"
                            class="h-32 w-32 rounded-full border-4 border-brand-100 object-cover shadow-md dark:border-neutral-800"
                        />
                    @else
                        <span class="flex h-32 w-32 items-center justify-center rounded-full border-4 border-brand-100 bg-brand-600 text-3xl font-bold text-white shadow-md dark:border-neutral-800">
                            {{ $user->initials() }}
                        </span>
                    @endif
                    <span class="absolute bottom-1 right-1 flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-brand-700 text-xs text-white dark:border-neutral-900">
                        <i class="fa-solid fa-camera" aria-hidden="true"></i>
                    </span>
                </div>

                <form method="POST" action="{{ route('profile.photo') }}" enctype="multipart/form-data" class="w-full space-y-3">
                    @csrf
                    <label for="photo" class="block w-full cursor-pointer rounded-xl border border-dashed border-brand-300 bg-brand-50/50 px-4 py-3 text-center text-sm font-medium text-brand-700 transition-colors hover:bg-brand-100 dark:border-neutral-700 dark:bg-neutral-800/60 dark:text-brand-400 dark:hover:bg-neutral-800">
                        <i class="fa-solid fa-upload mr-1.5" aria-hidden="true"></i>
                        {{ $user->photo ? 'Cambiar foto' : 'Subir foto' }}
                        <input
                            id="photo"
                            name="photo"
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp"
                            class="sr-only"
                            onchange="this.closest('form').submit()"
                        />
                    </label>
                    <p class="text-center text-xs text-neutral-400 dark:text-neutral-500">JPG, PNG o WebP · máx. 2 MB</p>
                    @error('photo')
                        <p class="text-center text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </form>

                @if ($user->photo)
                    <form method="POST" action="{{ route('profile.photo.destroy') }}" class="w-full">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="w-full rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 dark:border-red-900/50 dark:bg-transparent dark:text-red-400 dark:hover:bg-red-950/30"
                        >
                            <i class="fa-solid fa-trash mr-1.5" aria-hidden="true"></i>
                            Quitar foto
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Datos + contraseña --}}
        <div class="space-y-6 lg:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-2 border-b border-brand-100 bg-brand-50/40 px-6 py-4 sm:px-8 dark:border-neutral-800 dark:bg-neutral-800/40">
                    <i class="fa-solid fa-id-card text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-700 dark:text-neutral-300">Mis datos</h2>
                </div>

                <dl class="grid gap-x-6 gap-y-5 px-6 py-6 sm:grid-cols-2 sm:px-8">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Nombre</dt>
                        <dd class="mt-1 text-sm font-medium text-neutral-900 dark:text-white">{{ $user->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Correo electrónico</dt>
                        <dd class="mt-1 text-sm font-medium text-neutral-900 dark:text-white">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-neutral-400 dark:text-neutral-500">Rol</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-medium text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                                {{ $user->roles->first()?->name ?? 'Sin rol' }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-800 dark:bg-neutral-900">
                <div class="flex items-center gap-2 border-b border-brand-100 bg-brand-50/40 px-6 py-4 sm:px-8 dark:border-neutral-800 dark:bg-neutral-800/40">
                    <i class="fa-solid fa-key text-brand-600 dark:text-brand-400" aria-hidden="true"></i>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral-700 dark:text-neutral-300">Cambiar contraseña</h2>
                </div>

                <form method="POST" action="{{ route('profile.password') }}" class="space-y-6 px-6 py-6 sm:px-8">
                    @csrf

                    <div>
                        <label for="current_password" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Contraseña actual <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="current_password"
                            name="current_password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white @error('current_password') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        />
                        @error('current_password')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                Nueva contraseña <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white @error('password') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                            />
                            @error('password')
                                <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                                Confirmar contraseña <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                required
                                minlength="8"
                                class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-brand-800 hover:shadow-md"
                        >
                            <i class="fa-solid fa-floppy-disk text-sm" aria-hidden="true"></i>
                            Actualizar contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
