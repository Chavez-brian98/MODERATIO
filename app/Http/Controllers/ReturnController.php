<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReturn;
use App\Models\Sale;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 10);

        $returns = ProductReturn::query()
            ->with(['sale', 'user', 'details.product'])
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $stats = [
            'total_returns' => ProductReturn::count(),
            'total_refunded' => ProductReturn::sum('total_returned'),
            'today_returns' => ProductReturn::whereDate('created_at', today())->count(),
            'today_refunded' => ProductReturn::whereDate('created_at', today())->sum('total_returned'),
        ];

        return view('modules.returns.index', [
            'returns' => $returns,
            'stats' => $stats,
        ]);
    }

    public function create(): View
    {
        $sales = Sale::notCancelled()
            ->with(['customer', 'details.product'])
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        return view('modules.returns.create', ['sales' => $sales]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sale_id' => ['required', 'exists:sales,id'],
            'reason' => ['required', 'string', 'max:500'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            'products.*.subtotal' => ['required', 'numeric', 'min:0'],
        ]);

        $totalReturned = collect($validated['products'])->sum('subtotal');

        $return = ProductReturn::create([
            'sale_id' => $validated['sale_id'],
            'user_id' => (int) session('user_id', 1),
            'cash_register_id' => null,
            'reason' => $validated['reason'],
            'total_returned' => $totalReturned,
            'created_at' => now(),
        ]);

        $productDetails = [];

        foreach ($validated['products'] as $item) {
            $return->details()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'subtotal_returned' => $item['subtotal'],
            ]);

            Product::where('id', $item['product_id'])
                ->increment('current_stock', $item['quantity']);

            $productDetails[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
            ];
        }

        AuditService::log('CREATED', 'returns', $return->id, [
            'sale_id' => $validated['sale_id'],
            'reason' => $validated['reason'],
            'total_returned' => $totalReturned,
            'products' => $productDetails,
        ]);

        return redirect()->route('returns.show', $return)
            ->with('success', 'Devolución registrada correctamente.');
    }

    public function show(ProductReturn $return): View
    {
        $return->load(['sale.customer', 'user', 'details.product', 'cashRegister']);

        return view('modules.returns.show', ['return' => $return]);
    }
}
