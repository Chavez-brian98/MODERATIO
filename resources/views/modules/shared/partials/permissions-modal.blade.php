<div
    data-role-permissions-modal
    role="dialog"
    aria-modal="true"
    aria-labelledby="role-permissions-modal-title"
    class="fixed inset-0 z-[70] flex items-end justify-center sm:items-center sm:p-6"
>
    <div
        data-modal-backdrop
        class="absolute inset-0 bg-neutral-900/60 backdrop-blur-sm animate-[modal-backdrop-in_200ms_ease-out]"
    ></div>

    <div
        class="relative flex max-h-[88vh] w-full flex-col overflow-hidden border border-brand-200 bg-white shadow-2xl animate-[modal-panel-in_250ms_ease-out] dark:border-neutral-700 dark:bg-neutral-900 sm:max-w-2xl rounded-t-2xl sm:rounded-2xl"
    >
        <header class="flex items-center justify-between gap-4 border-b border-brand-100 px-6 py-4 dark:border-neutral-800">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-400">
                    <i class="fa-solid fa-user-shield" aria-hidden="true"></i>
                </div>
                <div class="min-w-0">
                    <h2 id="role-permissions-modal-title" class="truncate text-lg font-semibold text-neutral-900 dark:text-white">
                        {{ $modalTitle }}
                    </h2>
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">Marca las acciones permitidas.</p>
                </div>
            </div>

            <button
                type="button"
                data-modal-close
                aria-label="Cerrar permisos"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-neutral-400 transition-all hover:bg-red-50 hover:text-red-600 dark:text-neutral-500 dark:hover:bg-red-900/40 dark:hover:text-red-400"
            >
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </header>

        <form
            data-permissions-form
            data-permissions-mode="{{ $matrixMode }}"
            action="{{ $formAction }}"
            class="flex min-h-0 flex-1 flex-col"
        >
            @csrf

            <div class="overflow-y-auto px-6 py-5">
                @if ($superToggleChecked !== null)
                    <div class="flex items-center justify-between gap-4 rounded-xl border px-4 py-3.5 {{ $superToggleChecked ? 'border-violet-300 bg-violet-50/60 dark:border-violet-800 dark:bg-violet-900/20' : 'border-brand-200 bg-brand-50/40 dark:border-neutral-800 dark:bg-neutral-800/40' }}">
                        <div>
                            <p class="text-sm font-medium text-neutral-800 dark:text-neutral-200">Super administrador</p>
                            <p class="mt-0.5 text-xs text-neutral-500 dark:text-neutral-400">Acceso total implícito, sin necesidad de marcar permisos.</p>
                        </div>
                        <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                            <input
                                type="checkbox"
                                name="is_super_admin"
                                value="1"
                                data-super-admin-toggle
                                @checked($superToggleChecked)
                                class="peer sr-only"
                            />
                            <div class="peer relative h-6 w-11 rounded-full bg-neutral-300 transition-colors after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-transform peer-checked:bg-violet-600 peer-checked:after:translate-x-5 dark:bg-neutral-600"></div>
                        </label>
                    </div>
                @endif

                @if ($lockedHint !== null || $superToggleChecked !== null)
                    <p
                        data-permissions-locked-hint
                        class="mt-4 flex items-center gap-2 rounded-lg bg-violet-50 px-3 py-2 text-xs font-medium text-violet-700 dark:bg-violet-900/30 dark:text-violet-300 {{ ($lockedMatrix || $superToggleChecked) ? '' : 'hidden' }}"
                    >
                        <i class="fa-solid fa-lock text-[10px]" aria-hidden="true"></i>
                        {{ $lockedHint ?? 'Acceso total activo: todos los permisos están marcados y bloqueados.' }}
                    </p>
                @endif

                <div
                    data-permissions-matrix
                    data-inherited="{{ json_encode(array_values($matrixInheritedIds)) }}"
                    class="mt-5 {{ ($lockedMatrix || $superToggleChecked) ? 'opacity-40' : '' }}"
                >
                    <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-800">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-brand-50/60 text-left text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:bg-neutral-800/60 dark:text-neutral-400">
                                    <th class="px-4 py-3">Recurso</th>
                                    @foreach ($actions as $action)
                                        <th class="px-4 py-3 text-center">{{ $action->display_name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                                @foreach ($resources as $resource)
                                    <tr class="hover:bg-brand-50/30 dark:hover:bg-neutral-800/40">
                                        <td class="px-4 py-2.5 font-medium text-neutral-800 dark:text-neutral-200">
                                            {{ $resource->display_name }}
                                        </td>
                                        @foreach ($actions as $action)
                                            @php
                                                $permission = $resource->permissions->firstWhere('action_id', $action->id);
                                            @endphp
                                            <td class="px-4 py-2.5 text-center">
                                                @if ($permission)
                                                    @if ($matrixMode === 'ids')
                                                        <input
                                                            type="checkbox"
                                                            name="permissions[]"
                                                            value="{{ $permission->id }}"
                                                            @checked($matrixStates[$permission->id] ?? false)
                                                            @disabled($lockedMatrix || $superToggleChecked)
                                                            title="{{ $permission->display_name }}"
                                                            class="h-4 w-4 cursor-pointer rounded border-neutral-300 text-brand-600 focus:ring-brand-500 disabled:cursor-not-allowed dark:border-neutral-600 dark:bg-neutral-800"
                                                        />
                                                    @else
                                                        <input
                                                            type="checkbox"
                                                            data-permission-id="{{ $permission->id }}"
                                                            @checked($matrixStates[$permission->id] ?? false)
                                                            @disabled($lockedMatrix || $superToggleChecked)
                                                            title="{{ $permission->display_name }}"
                                                            class="h-4 w-4 cursor-pointer rounded border-neutral-300 text-brand-600 focus:ring-brand-500 disabled:cursor-not-allowed dark:border-neutral-600 dark:bg-neutral-800"
                                                        />
                                                    @endif
                                                @else
                                                    <span class="text-neutral-300 dark:text-neutral-700">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($legend)
                        <p class="mt-3 flex items-start gap-2 text-xs text-neutral-500 dark:text-neutral-400">
                            <i class="fa-solid fa-circle-info mt-0.5" aria-hidden="true"></i>
                            {{ $legend }}
                        </p>
                    @endif
                </div>
            </div>

            <footer class="flex flex-col-reverse gap-3 border-t border-brand-100 bg-brand-50/50 px-6 py-4 dark:border-neutral-800 dark:bg-neutral-800/50 sm:flex-row sm:justify-end">
                <button
                    type="button"
                    data-modal-close
                    class="rounded-xl border border-brand-200 bg-white px-4 py-2 text-sm font-medium text-neutral-700 transition-all hover:-translate-y-0.5 hover:border-brand-300 hover:bg-brand-50 hover:shadow-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:border-neutral-600 dark:hover:bg-neutral-700"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    data-permissions-submit
                    class="inline-flex items-center gap-2 rounded-xl bg-brand-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-brand-800 hover:shadow-md"
                >
                    <i class="fa-solid fa-floppy-disk text-sm" aria-hidden="true"></i>
                    Guardar permisos
                </button>
            </footer>
        </form>
    </div>
</div>
