@extends('layouts.app')

@section('title', 'Devolución #' . $return->id)

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Devoluciones', 'url' => route('returns.index')],
        ['label' => 'Devolución #' . $return->id],
    ]])

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Devolución #{{ $return->id }}</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                Venta {{ $return->sale->ticket_number }} — {{ $return->created_at->format('d/m/Y H:i') }}
            </p>
        </div>
        <a href="{{ route('returns.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
            <i class="fa-solid fa-arrow-left text-xs"></i> Volver
        </a>
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Total Devuelto</p>
            <p class="mt-1 text-2xl font-bold text-red-600 dark:text-red-400">${{ number_format($return->total_returned, 2) }}</p>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Productos</p>
            <p class="mt-1 text-2xl font-bold text-neutral-800 dark:text-neutral-100">{{ $return->details->count() }}</p>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Empleado</p>
            <p class="mt-1 text-sm font-semibold text-neutral-800 dark:text-neutral-100">{{ $return->user->full_name }}</p>
        </div>
        <div class="rounded-2xl border border-brand-200 bg-white p-5 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Fecha</p>
            <p class="mt-1 text-sm font-semibold text-neutral-800 dark:text-neutral-100">{{ $return->created_at->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-brand-200 bg-white shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="border-b border-brand-100 px-5 py-4 dark:border-neutral-700">
                    <h2 class="flex items-center gap-2 text-sm font-semibold text-brand-800 dark:text-brand-200">
                        <i class="fa-solid fa-boxes-stacked text-brand-500"></i> Productos Devueltos
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-brand-100 bg-brand-50/60 dark:border-neutral-700 dark:bg-neutral-800/60">
                                <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Producto</th>
                                <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Cantidad</th>
                                <th class="px-4 py-3 font-semibold text-brand-800 dark:text-brand-200">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                            @forelse ($return->details as $detail)
                                <tr class="transition-colors hover:bg-brand-50/40 dark:hover:bg-neutral-800/40">
                                    <td class="px-4 py-3 font-medium text-neutral-800 dark:text-neutral-200">{{ $detail->product->name ?? 'Producto #' . $detail->product_id }}</td>
                                    <td class="px-4 py-3 dark:text-neutral-300">{{ $detail->quantity }}</td>
                                    <td class="px-4 py-3 font-semibold text-red-600 dark:text-red-400">-${{ number_format($detail->subtotal_returned, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-neutral-500 dark:text-neutral-400">Sin detalles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-4 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                        <i class="fa-solid fa-comment-dots"></i>
                    </div>
                    <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Motivo</span>
                </div>
                <p class="text-sm text-neutral-700 dark:text-neutral-300">{{ $return->reason }}</p>
            </div>

            <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                <div class="mb-4 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Venta Original</span>
                </div>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-neutral-500 dark:text-neutral-400">Ticket:</dt>
                        <dd class="font-medium text-neutral-800 dark:text-neutral-200">{{ $return->sale->ticket_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-neutral-500 dark:text-neutral-400">Total venta:</dt>
                        <dd class="font-medium text-neutral-800 dark:text-neutral-200">${{ number_format($return->sale->total, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-neutral-500 dark:text-neutral-400">Método pago:</dt>
                        <dd class="text-neutral-800 dark:text-neutral-200">{{ $return->sale->payment_method }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
@endsection
