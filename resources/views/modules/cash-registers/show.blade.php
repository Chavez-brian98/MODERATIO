@extends('layouts.app')

@section('title', 'Caja #' . $register->id)

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Caja / Arqueo', 'url' => route('cash-register.index')],
        ['label' => 'Caja #' . $register->id],
    ]])

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Caja #{{ $register->id }}</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                {{ $register->shift ? 'Turno: ' . $register->shift . ' — ' : '' }}{{ $register->user->full_name }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if ($register->status === 'OPEN')
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <i class="fa-solid fa-circle text-[6px]"></i> Abierta
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-neutral-100 px-3 py-1 text-xs font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                    <i class="fa-solid fa-circle text-[6px]"></i> Cerrada
                </span>
            @endif
            <a href="{{ route('cash-register.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
                <i class="fa-solid fa-arrow-left text-xs"></i> Volver
            </a>
        </div>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Apertura</p>
            <p class="mt-1 text-2xl font-bold text-neutral-800 dark:text-neutral-100">${{ number_format($register->opening_amount, 2) }}</p>
            <p class="mt-0.5 text-xs text-neutral-400 dark:text-neutral-500">{{ $register->opening_date->format('d/m/Y H:i') }}</p>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Total Ventas</p>
            <p class="mt-1 text-2xl font-bold text-neutral-800 dark:text-neutral-100">${{ number_format($arqueo['total_sales'], 2) }}</p>
            <p class="mt-0.5 text-xs text-neutral-400 dark:text-neutral-500">{{ $arqueo['sales_count'] }} transacciones</p>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Efectivo</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($arqueo['cash_sales'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Teórico en Caja</p>
            <p class="mt-1 text-2xl font-bold text-brand-600 dark:text-brand-400">${{ number_format($arqueo['theoretical_amount'], 2) }}</p>
            <p class="mt-0.5 text-xs text-neutral-400 dark:text-neutral-500">Apertura + Ventas efectivo</p>
        </div>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-brand-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Efectivo</p>
                    <p class="text-lg font-bold text-neutral-800 dark:text-neutral-100">${{ number_format($arqueo['cash_sales'], 2) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Tarjeta</p>
                    <p class="text-lg font-bold text-neutral-800 dark:text-neutral-100">${{ number_format($arqueo['card_sales'], 2) }}</p>
                </div>
            </div>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-neutral-500 dark:text-neutral-400">Transferencia</p>
                    <p class="text-lg font-bold text-neutral-800 dark:text-neutral-100">${{ number_format($arqueo['transfer_sales'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    @if ($register->status === 'OPEN')
        <div class="mx-auto mb-8 max-w-2xl rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm dark:border-amber-800 dark:bg-amber-950/30">
            <div class="mb-4 flex items-center gap-3 border-b border-amber-200 pb-4 dark:border-amber-800">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-200 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <span class="text-sm font-semibold text-amber-800 dark:text-amber-200">Cerrar Caja</span>
            </div>

            <form method="POST" action="{{ route('cash-register.close', $register) }}" onsubmit="return confirmClose(event)">
                @csrf
                @method('PATCH')

                <div class="mb-4 rounded-xl bg-white p-4 dark:bg-neutral-900">
                    <p class="text-sm text-neutral-600 dark:text-neutral-400">Monto teórico en efectivo:</p>
                    <p class="text-xl font-bold text-brand-600 dark:text-brand-400">${{ number_format($arqueo['theoretical_amount'], 2) }}</p>
                </div>

                <div class="mb-5">
                    <label for="actual_closing_amount" class="mb-1.5 block text-sm font-medium text-amber-800 dark:text-amber-200">Monto Real en Efectivo <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400">$</span>
                        <input
                            type="number"
                            name="actual_closing_amount"
                            id="actual_closing_amount"
                            value="{{ old('actual_closing_amount', number_format($arqueo['theoretical_amount'], 2, '.', '')) }}"
                            step="0.01"
                            min="0"
                            required
                            class="w-full rounded-xl border border-amber-300 bg-white py-2.5 pl-8 pr-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-amber-700 dark:bg-neutral-800 dark:text-neutral-200"
                        />
                    </div>
                    @error('actual_closing_amount')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                    <div id="difference-display" class="mt-2 text-sm font-medium"></div>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-amber-700 active:bg-amber-800">
                    <i class="fa-solid fa-lock text-xs"></i> Cerrar Caja
                </button>
            </form>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
        <div class="border-b border-brand-100 px-5 py-4 dark:border-neutral-700">
            <h2 class="flex items-center gap-2 text-sm font-semibold text-brand-800 dark:text-brand-200">
                <i class="fa-solid fa-receipt text-brand-500"></i> Transacciones
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-brand-100 bg-brand-50/60 dark:border-neutral-700 dark:bg-neutral-800/60">
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Ticket</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 sm:table-cell dark:text-brand-200">Hora</th>
                        <th class="hidden px-4 py-3 font-semibold text-brand-800 md:table-cell dark:text-brand-200">Cliente</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Método</th>
                        <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                    @forelse ($arqueo['sales'] as $sale)
                        <tr class="transition-colors hover:bg-brand-50/40 dark:hover:bg-neutral-800/40">
                            <td class="px-4 py-3 font-medium text-neutral-800 dark:text-neutral-200">{{ $sale->ticket_number }}</td>
                            <td class="hidden px-4 py-3 sm:table-cell dark:text-neutral-300">{{ $sale->created_at->format('H:i:s') }}</td>
                            <td class="hidden px-4 py-3 md:table-cell dark:text-neutral-300">{{ $sale->customer->full_name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if ($sale->payment_method === 'CASH')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <i class="fa-solid fa-money-bill-wave text-[8px]"></i> Efectivo
                                    </span>
                                @elseif ($sale->payment_method === 'CARD')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        <i class="fa-solid fa-credit-card text-[8px]"></i> Tarjeta
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-violet-100 px-2 py-0.5 text-xs font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-400">
                                        <i class="fa-solid fa-building-columns text-[8px]"></i> Transferencia
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-semibold text-neutral-800 dark:text-neutral-100">${{ number_format($sale->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-100 text-brand-400 dark:bg-brand-900/30 dark:text-brand-500">
                                        <i class="fa-solid fa-receipt text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-neutral-700 dark:text-neutral-300">Sin transacciones</p>
                                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Aún no se han registrado ventas en esta caja.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function confirmClose(e) {
            const amount = parseFloat(document.getElementById('actual_closing_amount').value) || 0;
            const theoretical = {{ $arqueo['theoretical_amount'] }};
            const diff = amount - theoretical;
            const msg = diff === 0
                ? 'El monto coincide con el teórico. ¿Cerrar caja?'
                : diff > 0
                    ? `Sobrante de $${diff.toFixed(2)}. ¿Cerrar caja?`
                    : `Faltante de $${Math.abs(diff).toFixed(2)}. ¿Cerrar caja?`;
            if (!confirm(msg)) {
                e.preventDefault();
                return false;
            }
            return true;
        }

        document.getElementById('actual_closing_amount')?.addEventListener('input', function () {
            const amount = parseFloat(this.value) || 0;
            const theoretical = {{ $arqueo['theoretical_amount'] }};
            const diff = amount - theoretical;
            const el = document.getElementById('difference-display');
            if (diff === 0) {
                el.textContent = 'Coincide con el monto teórico';
                el.className = 'mt-2 text-sm font-medium text-emerald-600 dark:text-emerald-400';
            } else if (diff > 0) {
                el.textContent = `Sobrante: $${diff.toFixed(2)}`;
                el.className = 'mt-2 text-sm font-medium text-emerald-600 dark:text-emerald-400';
            } else {
                el.textContent = `Faltante: $${Math.abs(diff).toFixed(2)}`;
                el.className = 'mt-2 text-sm font-medium text-red-600 dark:text-red-400';
            }
        });
    </script>
@endsection
