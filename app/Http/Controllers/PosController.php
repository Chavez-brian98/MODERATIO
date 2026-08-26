<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use App\Services\CashRegisterService;
use App\Services\PosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(
        private readonly PosService $posService,
        private readonly CashRegisterService $cashRegisterService,
    ) {}

    public function index(): View
    {
        $products = $this->posService->getActiveProducts();
        $cashRegister = $this->posService->getOpenCashRegisterForUser((int) auth()->id());

        $cashSummary = null;

        if ($cashRegister !== null) {
            $cashSales = (float) $cashRegister->sales()
                ->notCancelled()
                ->where('payment_method', 'CASH')
                ->sum('total');

            $cashSummary = [
                'opening_amount' => (float) $cashRegister->opening_amount,
                'cash_sales' => $cashSales,
                'theoretical' => round((float) $cashRegister->opening_amount + $cashSales, 2),
            ];
        }

        $productsJson = $products->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'barcode' => $p->barcode,
            'sale_price' => $p->sale_price,
            'purchase_price' => $p->purchase_price,
            'current_stock' => $p->current_stock,
            'has_tax' => (bool) $p->has_tax,
            'tax_percentage' => $p->tax_percentage,
            'category_id' => $p->category_id,
            'category_name' => $p->category?->name ?? '',
        ])->values();

        return view('modules.pos.index', [
            'products' => $products,
            'productsJson' => $productsJson,
            'cashRegister' => $cashRegister,
            'cashSummary' => $cashSummary,
        ]);
    }

    public function searchProducts(Request $request): JsonResponse
    {
        $request->validate(['q' => ['sometimes', 'string', 'max:100']]);

        $products = $this->posService->getActiveProducts($request->input('q', ''));

        return response()->json($products);
    }

    public function searchCustomers(Request $request): JsonResponse
    {
        $request->validate(['q' => ['sometimes', 'string', 'max:100']]);

        $customers = $this->posService->searchCustomers($request->input('q', ''));

        return response()->json($customers);
    }

    public function openCashRegister(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'opening_amount' => ['required', 'numeric', 'min:0'],
            'shift' => ['required', 'string', 'in:MORNING,AFTERNOON,NIGHT'],
        ]);

        $register = $this->posService->openCashRegister(
            userId: (int) auth()->id(),
            openingAmount: $validated['opening_amount'],
            shift: $validated['shift'],
        );

        AuditService::log('OPENED', 'cash_registers', $register->id, [
            'opening_amount' => $register->opening_amount,
            'shift' => $register->shift,
        ]);

        return response()->json($register);
    }

    public function closeCashRegister(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'actual_closing_amount' => ['required', 'numeric', 'min:0'],
            'closing_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $cashRegister = $this->posService->getOpenCashRegisterForUser((int) auth()->id());

        if ($cashRegister === null) {
            return response()->json([
                'error' => 'No tienes una caja abierta a tu cargo para cerrar.',
            ], 422);
        }

        $register = $this->cashRegisterService->close(
            register: $cashRegister,
            actualClosingAmount: (float) $validated['actual_closing_amount'],
            notes: $validated['closing_notes'] ?? null,
        );

        AuditService::log('CLOSED', 'cash_registers', $register->id, [
            'opening_amount' => $register->opening_amount,
            'theoretical_closing_amount' => $register->theoretical_closing_amount,
            'actual_closing_amount' => $register->actual_closing_amount,
            'difference' => $register->difference,
            'closing_notes' => $register->closing_notes,
        ]);

        return response()->json([
            'message' => 'Caja cerrada correctamente.',
            'register' => $register,
        ]);
    }

    public function completeSale(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['sometimes', 'numeric', 'min:0'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'payment_method' => ['required', 'string', 'in:CASH,CARD,TRANSFER'],
            'amount_received' => ['required', 'numeric', 'min:0'],
            'observations' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $cashRegister = $this->posService->getOpenCashRegisterForUser((int) auth()->id());

            if ($cashRegister === null) {
                return response()->json([
                    'error' => 'No tienes una caja abierta a tu cargo. Debes abrir una caja antes de poder vender.',
                ], 422);
            }

            $sale = $this->posService->completeSale(
                items: $validated['items'],
                userId: (int) auth()->id(),
                cashRegisterId: $cashRegister->id,
                customerId: $validated['customer_id'] ?? null,
                paymentMethod: $validated['payment_method'],
                amountReceived: $validated['amount_received'],
                observations: $validated['observations'] ?? null,
            );

            AuditService::log('SALE_COMPLETED', 'sales', $sale->id, [
                'ticket_number' => $sale->ticket_number,
                'total' => $sale->total,
                'payment_method' => $sale->payment_method,
                'items_count' => count($validated['items']),
            ]);

            return response()->json(['sale' => $sale]);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
