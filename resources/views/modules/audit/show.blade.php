@extends('layouts.app')

@section('title', 'Bitácora · Registro #' . $log->id)

@section('content')
    @php
        $actionLabels = [
            'CREATED' => 'Creación',
            'UPDATED' => 'Actualización',
            'DELETED' => 'Eliminación',
            'TOGGLED' => 'Cambio de estado',
            'LOGIN' => 'Inicio de sesión',
            'LOGOUT' => 'Cierre de sesión',
            'OPENED' => 'Apertura de caja',
            'CLOSED' => 'Cierre de caja',
            'SALE_COMPLETED' => 'Venta realizada',
            'PERMISSIONS_UPDATED' => 'Permisos actualizados',
        ];
        $actionBadges = [
            'CREATED' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
            'UPDATED' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
            'DELETED' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
            'TOGGLED' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
            'LOGIN' => 'bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400',
            'LOGOUT' => 'bg-neutral-200 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400',
            'OPENED' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400',
            'CLOSED' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-400',
            'SALE_COMPLETED' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-400',
            'PERMISSIONS_UPDATED' => 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-900/40 dark:text-fuchsia-400',
        ];
        $tableLabels = [
            'users' => 'Empleados',
            'products' => 'Productos',
            'categories' => 'Categorías',
            'roles' => 'Roles',
            'sales' => 'Ventas',
            'sale_details' => 'Detalle de ventas',
            'cash_registers' => 'Cajas',
            'customers' => 'Clientes',
            'returns' => 'Devoluciones',
            'settings' => 'Configuración',
            'user_has_permissions' => 'Permisos de usuario',
        ];
        $oldValues = $log->old_values ?? [];
        $newValues = $log->new_values ?? [];
        $changeKeys = collect($oldValues)
            ->keys()
            ->merge(collect($newValues)->keys())
            ->unique()
            ->values();
        $fieldLabels = [
            'name' => 'Nombre',
            'description' => 'Descripción',
            'email' => 'Correo electrónico',
            'password' => 'Contraseña',
            'first_name' => 'Primer nombre',
            'last_name' => 'Apellido',
            'phone' => 'Teléfono',
            'address' => 'Dirección',
            'DUI' => 'DUI',
            'birthday' => 'Fecha de nacimiento',
            'photo' => 'Foto',
            'is_active' => 'Activo',
            'barcode' => 'Código de barras',
            'purchase_price' => 'Precio de compra',
            'sale_price' => 'Precio de venta',
            'current_stock' => 'Stock actual',
            'min_stock' => 'Stock mínimo',
            'has_tax' => 'Lleva IVA',
            'tax_percentage' => 'Porcentaje de IVA',
            'category_id' => 'Categoría',
            'parent_category_id' => 'Categoría padre',
        ];
        $formatValue = function (mixed $value): string {
            return match (true) {
                $value === null => '—',
                is_bool($value) => $value ? 'Sí' : 'No',
                is_array($value) => json_encode($value, JSON_UNESCAPED_UNICODE),
                default => (string) $value,
            };
        };
        $responsibleRole = $log->user?->roles->first();
    @endphp

    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Bitácora (Auditoría)', 'url' => route('audit.index')],
        ['label' => 'Registro #' . $log->id],
    ]])

    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">
                Registro #{{ $log->id }}
            </h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                Detalle completo de la acción registrada en el sistema.
            </p>
        </div>
        <a
            href="{{ route('audit.index') }}"
            class="inline-flex shrink-0 items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800"
        >
            <i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i>
            Volver a la bitácora
        </a>
    </header>

    <section class="mt-6 grid items-start gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            {{-- Información de la acción --}}
            <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                    </div>
                    <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Información de la acción</span>
                </div>

                <dl class="divide-y divide-neutral-100 rounded-xl border border-neutral-100 dark:divide-neutral-800 dark:border-neutral-800">
                    <div class="flex items-center justify-between gap-6 px-4 py-3">
                        <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Acción</dt>
                        <dd>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $actionBadges[$log->action] ?? 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400' }}">
                                {{ $actionLabels[$log->action] ?? $log->action }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-6 px-4 py-3">
                        <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Tabla afectada</dt>
                        <dd class="text-right text-sm font-medium text-neutral-900 dark:text-white">{{ $tableLabels[$log->affected_table] ?? $log->affected_table }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-6 px-4 py-3">
                        <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">ID del registro</dt>
                        <dd class="text-right text-sm font-medium text-neutral-900 dark:text-white">{{ $log->record_id ? '#' . $log->record_id : '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-6 px-4 py-3">
                        <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Fecha y hora</dt>
                        <dd class="text-right text-sm font-medium text-neutral-900 dark:text-white">{{ $log->created_at->format('d/m/Y \a \l\a\s H:i:s') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-6 px-4 py-3">
                        <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Dirección IP</dt>
                        <dd class="text-right text-sm font-medium text-neutral-900 dark:text-white">{{ $log->source_ip ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Datos anteriores y nuevos --}}
            @if ($changeKeys->isNotEmpty())
                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                            <i class="fa-solid fa-code-compare" aria-hidden="true"></i>
                        </div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">
                            @if ($log->action === 'CREATED')
                                Datos creados
                            @elseif ($log->action === 'DELETED')
                                Datos eliminados
                            @else
                                Datos anteriores y nuevos
                            @endif
                        </span>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-neutral-100 dark:border-neutral-800">
                        <table class="min-w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-neutral-100 bg-neutral-50/60 text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:border-neutral-800 dark:bg-neutral-800/60 dark:text-neutral-400">
                                    <th class="px-4 py-2.5">Campo</th>
                                    <th class="px-4 py-2.5">Valor anterior</th>
                                    <th class="px-4 py-2.5">Valor nuevo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                                @foreach ($changeKeys as $key)
                                    <tr>
                                        <td class="px-4 py-2.5 align-top font-medium whitespace-nowrap text-neutral-800 dark:text-neutral-200">
                                            {{ $fieldLabels[$key] ?? $key }}
                                        </td>
                                        <td class="px-4 py-2.5 align-top break-words text-red-600 dark:text-red-400">
                                            {{ $formatValue($oldValues[$key] ?? null) }}
                                        </td>
                                        <td class="px-4 py-2.5 align-top break-words text-green-700 dark:text-green-400">
                                            {{ $formatValue($newValues[$key] ?? null) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Detalles adicionales (eventos semánticos) --}}
            @if ($log->details)
                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                            <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                        </div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Detalles adicionales del evento</span>
                    </div>

                    <div class="rounded-xl border border-neutral-100 bg-neutral-50 p-4 dark:border-neutral-800 dark:bg-neutral-800/50">
                        <pre class="overflow-x-auto text-xs text-neutral-700 whitespace-pre-wrap break-words dark:text-neutral-300">{{ is_array($log->details) ? json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $log->details }}</pre>
                    </div>
                </div>
            @endif
        </div>

        <aside class="space-y-6">
            {{-- Usuario responsable --}}
            <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                        <i class="fa-solid fa-user-pen" aria-hidden="true"></i>
                    </div>
                    <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">¿Quién hizo el cambio?</span>
                </div>

                @if ($log->user)
                    <div class="flex items-center gap-3">
                        @if ($log->user->photoUrl())
                            <img
                                src="{{ $log->user->photoUrl() }}"
                                alt="Foto de {{ $log->user->full_name }}"
                                class="h-12 w-12 shrink-0 rounded-full object-cover ring-2 ring-brand-200 dark:ring-neutral-700"
                            />
                        @else
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white ring-2 ring-brand-200 dark:ring-neutral-700">
                                {{ $log->user->initials() }}
                            </span>
                        @endif
                        <div class="min-w-0">
                            <p class="truncate font-medium text-neutral-900 dark:text-white">{{ $log->user->full_name }}</p>
                            <p class="truncate text-xs text-neutral-500 dark:text-neutral-400">{{ $log->user->email }}</p>
                        </div>
                    </div>
                    @if ($responsibleRole)
                        <p class="mt-4 text-xs text-neutral-500 dark:text-neutral-400">
                            Rol:
                            <span class="ml-1 inline-flex items-center rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-semibold text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                                {{ $responsibleRole->name }}
                            </span>
                        </p>
                    @endif
                @else
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-neutral-200 text-neutral-500 ring-2 ring-neutral-100 dark:bg-neutral-800 dark:text-neutral-400 dark:ring-neutral-800">
                            <i class="fa-solid fa-gear" aria-hidden="true"></i>
                        </span>
                        <div>
                            <p class="font-medium text-neutral-900 dark:text-white">Sistema</p>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Acción automática sin usuario</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Registro afectado --}}
            @if ($affected)
                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                            <i class="fa-solid fa-box-archive" aria-hidden="true"></i>
                        </div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">¿De quién son los datos?</span>
                    </div>

                    <dl class="divide-y divide-neutral-100 rounded-xl border border-neutral-100 dark:divide-neutral-800 dark:border-neutral-800">
                        <div class="flex items-center justify-between gap-6 px-4 py-3">
                            <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Identificación</dt>
                            <dd class="min-w-0 text-right">
                                <p class="truncate text-sm font-medium text-neutral-900 dark:text-white">{{ $affected['title'] }}</p>
                                @if ($affected['subtitle'])
                                    <p class="truncate text-xs text-neutral-500 dark:text-neutral-400">{{ $affected['subtitle'] }}</p>
                                @endif
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-6 px-4 py-3">
                            <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Estado actual</dt>
                            <dd>
                                @if ($affected['exists'])
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700 dark:bg-green-900/40 dark:text-green-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Existe en el sistema
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-900/40 dark:text-red-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                        Eliminado o no encontrado
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex items-center justify-between gap-6 px-4 py-3">
                            <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Referencia</dt>
                            <dd class="text-right text-sm font-medium text-neutral-900 dark:text-white">
                                {{ $tableLabels[$log->affected_table] ?? $log->affected_table }} · #{{ $log->record_id }}
                            </dd>
                        </div>
                    </dl>
                </div>
            @endif

            {{-- Resumen del registro --}}
            <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-4 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-400">
                        <i class="fa-solid fa-clock" aria-hidden="true"></i>
                    </div>
                    <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Resumen</span>
                </div>
                <p class="text-sm leading-relaxed text-neutral-600 dark:text-neutral-400">
                    El usuario
                    <strong class="font-semibold text-neutral-900 dark:text-white">{{ $log->user?->full_name ?? 'Sistema' }}</strong>
                    realizó una acción de tipo
                    <strong class="font-semibold text-neutral-900 dark:text-white">{{ strtolower($actionLabels[$log->action] ?? $log->action) }}</strong>
                    sobre {{ strtolower($tableLabels[$log->affected_table] ?? $log->affected_table) }}
                    @if ($log->record_id)
                        <strong class="font-semibold text-neutral-900 dark:text-white">#{{ $log->record_id }}</strong>
                    @endif
                    el {{ $log->created_at->format('d/m/Y') }} a las {{ $log->created_at->format('H:i:s') }},
                    desde la IP {{ $log->source_ip ?? 'desconocida' }}.
                </p>
            </div>
        </aside>
    </section>
@endsection
