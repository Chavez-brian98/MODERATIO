<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Iniciar sesión · {{ config('app.name') }}</title>

    @fonts

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-white font-sans text-neutral-900 antialiased">
<main class="min-h-screen lg:grid lg:grid-cols-2">
    {{-- Left: espacio reservado para la imagen del negocio --}}
    <div class="relative hidden overflow-hidden bg-white lg:block">
        <img src="{{ asset('storage/portada.jpg') }}" alt="Glenda Store" class="h-full w-full object-cover"/>
    </div>

    {{-- Right: login card --}}
    <div class="flex items-center justify-center px-4 py-12 sm:px-8">
        <div class="w-full max-w-md">
            <div class="rounded-3xl border border-neutral-200 bg-white p-8 shadow-xl shadow-brand-500/15 sm:p-10">
                <div class="flex flex-col items-center">
                    <img src="{{ asset('storage/logobg.png') }}" alt="Glenda Store" class="h-20 w-auto"/>
                </div>
                <h1 class="mt-7 text-center text-2xl font-semibold tracking-tight text-neutral-900">Bienvenido de
                    nuevo</h1>
                <p class="mt-2 text-center text-sm text-neutral-500">Inicia sesión para continuar.</p>
                <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-neutral-700">Correo
                            electrónico</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            autofocus
                            value="{{ old('email') }}"
                            class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 @error('email') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        />
                        @error('email')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password"
                               class="mb-1.5 block text-sm font-medium text-neutral-700">Contraseña</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="mt-1 block w-full rounded-lg border border-neutral-300 bg-white px-3.5 py-2.5 text-sm text-neutral-900 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 @error('password') border-red-400 focus:border-red-500 focus:ring-red-500/20 @enderror"
                        />
                        @error('password')
                            <p class="mt-1.5 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 flex justify-end">
                            <a
                                href="#"
                                class="rounded-sm text-sm font-medium text-brand-700 hover:text-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40"
                            >
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40 focus-visible:ring-offset-2"
                    >
                        Iniciar sesión
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-sm text-neutral-500">© {{ date('Y') }} Glenda Store</p>
        </div>
    </div>
</main>
</body>
</html>
