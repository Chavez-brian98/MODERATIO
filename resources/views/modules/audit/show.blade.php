<div
    data-audit-modal
    role="dialog"
    aria-modal="true"
    aria-labelledby="audit-modal-title"
    class="fixed inset-0 z-[70] flex items-end justify-center sm:items-center sm:p-6"
>
    <div
        data-modal-backdrop
        class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm animate-[modal-backdrop-in_200ms_ease-out]"
    ></div>

    <div
        class="relative flex max-h-[88vh] w-full flex-col overflow-hidden border border-brand-200 bg-white shadow-2xl animate-[modal-panel-in_250ms_ease-out] dark:border-neutral-700 dark:bg-neutral-900 sm:max-w-lg sm:rounded-2xl rounded-t-2xl"
    >
        @php
            $actionLabels = [
                'CREATED' => 'Creación',
                'UPDATED' => 'Actualización',
                'DELETED' => 'Eliminación',
                'TOGGLED' => 'Cambio de estado',
                'LOGIN' => 'Inicio de sesión',
                'LOGOUT' => 'Cierre de sesión',
            ];
            $actionBadges = [
                'CREATED' => 'bg-green-50 text-green-700 dark:bg-green-900/40 dark:text-green-400',
                'UPDATED' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
                'DELETED' => 'bg-red-50 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                'TOGGLED' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400',
                'LOGIN' => 'bg-brand-50 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400',
                'LOGOUT' => 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400',
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
            ];
        @endphp

        <header class="flex items-center justify-between gap-4 border-b border-brand-100 px-6 py-4 dark:border-neutral-800">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                    <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <h2 id="audit-modal-title" class="truncate text-lg font-semibold text-neutral-900 dark:text-white">
                        Registro #{{ $log->id }}
                    </h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $log->created_at->format('d/m/Y \a\s H:i:s') }}</p>
                </div>
            </div>

            <button
                type="button"
                data-modal-close
                aria-label="Cerrar detalle"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-neutral-400 transition-all hover:bg-red-50 hover:text-red-600 dark:text-neutral-500 dark:hover:bg-red-900/40 dark:hover:text-red-400"
            >
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </header>

        <div class="overflow-y-auto px-6 py-5">
            <h3 class="mb-3 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">
                <i class="fa-solid fa-circle-info text-sm" aria-hidden="true"></i>
                Información de la acción
            </h3>
            <dl class="divide-y divide-neutral-100 rounded-xl border border-neutral-100 dark:divide-neutral-800 dark:border-neutral-800">
                <div class="flex items-center justify-between gap-6 px-4 py-3">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Acción</dt>
                    <dd>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $actionBadges[$log->action] ?? 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400' }}">
                            {{ $actionLabels[$log->action] ?? $log->action }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-6 px-4 py-3">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Tabla afectada</dt>
                    <dd class="text-sm text-neutral-900 dark:text-white">{{ $tableLabels[$log->affected_table] ?? $log->affected_table }}</dd>
                </div>
                <div class="flex items-center justify-between gap-6 px-4 py-3">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">ID del registro</dt>
                    <dd class="text-sm text-neutral-900 dark:text-white">{{ $log->record_id ? '#' . $log->record_id : '—' }}</dd>
                </div>
                <div class="flex items-center justify-between gap-6 px-4 py-3">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Dirección IP</dt>
                    <dd class="text-sm text-neutral-900 dark:text-white">{{ $log->source_ip ?? '—' }}</dd>
                </div>
            </dl>

            <h3 class="mb-3 mt-6 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">
                <i class="fa-solid fa-user text-sm" aria-hidden="true"></i>
                Usuario responsable
            </h3>
            <dl class="divide-y divide-neutral-100 rounded-xl border border-neutral-100 dark:divide-neutral-800 dark:border-neutral-800">
                <div class="flex items-center justify-between gap-6 px-4 py-3">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Nombre</dt>
                    <dd class="text-sm font-medium text-neutral-900 dark:text-white">{{ $log->user?->full_name ?? 'Sistema' }}</dd>
                </div>
                @if ($log->user)
                    <div class="flex items-center justify-between gap-6 px-4 py-3">
                        <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Correo</dt>
                        <dd class="text-sm text-neutral-900 dark:text-white">{{ $log->user->email }}</dd>
                    </div>
                @endif
            </dl>

            @if ($log->details)
                <h3 class="mb-3 mt-6 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">
                    <i class="fa-solid fa-code text-sm" aria-hidden="true"></i>
                    Detalles
                </h3>
                <div class="rounded-xl border border-neutral-100 bg-neutral-50 p-4 dark:border-neutral-800 dark:bg-neutral-800/50">
                    <pre class="overflow-x-auto text-xs text-neutral-700 whitespace-pre-wrap break-words dark:text-neutral-300">{{ is_array($log->details) ? json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $log->details }}</pre>
                </div>
            @endif

            <h3 class="mb-3 mt-6 flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-neutral-400 dark:text-neutral-500">
                <i class="fa-solid fa-clock text-sm" aria-hidden="true"></i>
                Registro
            </h3>
            <dl class="divide-y divide-neutral-100 rounded-xl border border-neutral-100 dark:divide-neutral-800 dark:border-neutral-800">
                <div class="flex items-center justify-between gap-6 px-4 py-3">
                    <dt class="shrink-0 text-sm text-neutral-500 dark:text-neutral-400">Fecha y hora</dt>
                    <dd class="text-right text-sm text-neutral-900 dark:text-white">{{ $log->created_at->format('d/m/Y \a\s H:i:s') }}</dd>
                </div>
            </dl>
        </div>

        <footer class="flex items-center justify-end gap-2 border-t border-brand-100 bg-brand-50/50 px-6 py-4 dark:border-neutral-800 dark:bg-neutral-800/50">
            <button
                type="button"
                data-modal-close
                class="rounded-xl border border-brand-200 bg-white px-4 py-2 text-sm font-medium text-neutral-700 transition-all hover:-translate-y-0.5 hover:border-brand-300 hover:bg-brand-50 hover:shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:border-neutral-600 dark:hover:bg-neutral-700"
            >
                Cerrar
            </button>
        </footer>
    </div>
</div>
