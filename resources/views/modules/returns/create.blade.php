@extends('layouts.app')

@section('title', 'Nueva Devolución')

@section('content')
    @include('partials.breadcrumbs', ['crumbs' => [
        ['label' => 'Devoluciones', 'url' => route('returns.index')],
        ['label' => 'Nueva Devolución'],
    ]])

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-brand-800 sm:text-3xl dark:text-brand-200">Nueva Devolución</h1>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Selecciona la venta y los productos a devolver.</p>
        </div>
        <a href="{{ route('returns.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-brand-200 bg-white px-4 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
            <i class="fa-solid fa-arrow-left text-xs"></i> Volver
        </a>
    </div>

    <form method="POST" action="{{ route('returns.store') }}" id="return-form">
        @csrf

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-100 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Venta Original</span>
                    </div>

                    <div>
                        <label for="sale_id" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Seleccionar Venta <span class="text-red-500">*</span></label>
                        <select name="sale_id" id="sale_id" required onchange="loadSaleDetails(this.value)"
                            class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200">
                            <option value="">Seleccionar venta...</option>
                            @foreach ($sales as $sale)
                                <option value="{{ $sale->id }}" data-sale="{{ $sale->toJson() }}">
                                    #{{ $sale->ticket_number }} — {{ $sale->created_at->format('d/m/Y H:i') }} — ${{ number_format($sale->total, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('sale_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div id="sale-details" class="mt-4 hidden">
                        <div class="overflow-x-auto rounded-xl border border-brand-100 dark:border-neutral-700">
                            <table class="w-full text-left text-sm">
                                <thead>
                                    <tr class="border-b border-brand-100 bg-brand-50/60 dark:border-neutral-700 dark:bg-neutral-800/60">
                                        <th class="px-3 py-2 font-semibold text-brand-800 dark:text-brand-200">Producto</th>
                                        <th class="px-3 py-2 font-semibold text-brand-800 dark:text-brand-200">Precio</th>
                                        <th class="px-3 py-2 font-semibold text-brand-800 dark:text-brand-200">Cant.</th>
                                        <th class="px-3 py-2 font-semibold text-brand-800 dark:text-brand-200">Subtotal</th>
                                        <th class="px-3 py-2 font-semibold text-brand-800 dark:text-brand-200">Devolver</th>
                                    </tr>
                                </thead>
                                <tbody id="sale-details-body" class="divide-y divide-neutral-100 dark:divide-neutral-800">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-6 flex items-center gap-3 border-b border-brand-100 pb-4 dark:border-neutral-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                            <i class="fa-solid fa-comment-dots"></i>
                        </div>
                        <span class="text-sm font-semibold text-brand-800 dark:text-brand-200">Motivo de la Devolución</span>
                    </div>

                    <div>
                        <label for="reason" class="mb-1.5 block text-sm font-medium text-neutral-700 dark:text-neutral-300">Razón <span class="text-red-500">*</span></label>
                        <textarea name="reason" id="reason" rows="3" required maxlength="500" placeholder="Describe el motivo de la devolución..."
                            class="w-full rounded-xl border border-brand-200 bg-white py-2.5 px-4 text-sm text-neutral-700 shadow-sm placeholder:text-neutral-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"></textarea>
                        @error('reason') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-brand-200 bg-white p-6 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
                    <p class="text-xs font-medium uppercase tracking-wider text-neutral-500 dark:text-neutral-400">Resumen</p>
                    <div class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-neutral-500 dark:text-neutral-400">Productos a devolver:</span>
                            <span id="summary-count" class="font-medium text-neutral-800 dark:text-neutral-200">0</span>
                        </div>
                        <div class="flex justify-between border-t border-brand-100 pt-2 dark:border-neutral-700">
                            <span class="text-neutral-500 dark:text-neutral-400">Total a reembolsar:</span>
                            <span id="summary-total" class="font-bold text-red-600 dark:text-red-400">$0.00</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('returns.index') }}" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl border border-brand-200 bg-white px-5 py-2.5 text-sm font-medium text-neutral-700 shadow-sm transition-colors hover:bg-brand-50 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:bg-neutral-800">
                        Cancelar
                    </a>
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700 active:bg-brand-800">
                        <i class="fa-solid fa-rotate-left text-xs"></i> Registrar
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        const salesData = {};
        document.querySelectorAll('#sale_id option[data-sale]').forEach(function (opt) {
            try { salesData[opt.value] = JSON.parse(opt.dataset.sale); } catch (e) {}
        });

        function loadSaleDetails(saleId) {
            const container = document.getElementById('sale-details');
            const tbody = document.getElementById('sale-details-body');
            if (!saleId || !salesData[saleId]) {
                container.classList.add('hidden');
                tbody.innerHTML = '';
                updateSummary();
                return;
            }
            const sale = salesData[saleId];
            tbody.innerHTML = '';
            (sale.details || []).forEach(function (detail) {
                const tr = document.createElement('tr');
                tr.className = 'transition-colors hover:bg-brand-50/40 dark:hover:bg-neutral-800/40';
                tr.innerHTML = `
                    <td class="px-3 py-2 text-neutral-800 dark:text-neutral-200">${detail.product?.name || 'Producto #' + detail.product_id}</td>
                    <td class="px-3 py-2 dark:text-neutral-300">$${parseFloat(detail.unit_price).toFixed(2)}</td>
                    <td class="px-3 py-2 dark:text-neutral-300">${detail.quantity}</td>
                    <td class="px-3 py-2 dark:text-neutral-300">$${parseFloat(detail.subtotal).toFixed(2)}</td>
                    <td class="px-3 py-2">
                        <input type="number" min="0" max="${detail.quantity}" value="0" data-unit-price="${detail.unit_price}" data-detail-id="${detail.id}"
                            class="return-qty w-16 rounded-lg border border-brand-200 bg-white px-2 py-1 text-center text-sm dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200"
                            onchange="updateSummary()" oninput="updateSummary()" />
                        <input type="hidden" name="products[${detail.id}][product_id]" value="${detail.product_id}" />
                        <input type="hidden" name="products[${detail.id}][quantity]" class="return-qty-hidden" value="0" />
                        <input type="hidden" name="products[${detail.id}][subtotal]" class="return-subtotal-hidden" value="0" />
                    </td>
                `;
                tbody.appendChild(tr);
            });
            container.classList.remove('hidden');
            updateSummary();
        }

        function updateSummary() {
            let count = 0;
            let total = 0;
            document.querySelectorAll('.return-qty').forEach(function (input) {
                const qty = parseInt(input.value) || 0;
                const unitPrice = parseFloat(input.dataset.unitPrice) || 0;
                const subtotal = qty * unitPrice;
                const row = input.closest('tr');
                row.querySelector('.return-qty-hidden').value = qty;
                row.querySelector('.return-subtotal-hidden').value = subtotal.toFixed(2);
                if (qty > 0) {
                    count++;
                    total += subtotal;
                }
            });
            document.getElementById('summary-count').textContent = count;
            document.getElementById('summary-total').textContent = '$' + total.toFixed(2);
        }

        document.getElementById('return-form')?.addEventListener('submit', function (e) {
            const total = parseFloat(document.getElementById('summary-total').textContent.replace('$', '')) || 0;
            if (total <= 0) {
                e.preventDefault();
                alert('Selecciona al menos un producto para devolver.');
                return false;
            }
            return true;
        });
    </script>
@endsection
