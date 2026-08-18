<?php

namespace App\Http\Controllers;

use App\Models\ProductReturn;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $sales = Sale::notCancelled()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['details.product', 'customer'])
            ->get();

        $returns = ProductReturn::whereBetween('created_at', [$startDate, $endDate])
            ->with('details.product')
            ->get();

        $stats = [
            'total_sales' => $sales->sum('total'),
            'total_transactions' => $sales->count(),
            'total_returns' => $returns->sum('total_returned'),
            'net_sales' => $sales->sum('total') - $returns->sum('total_returned'),
            'avg_ticket' => $sales->count() > 0 ? $sales->sum('total') / $sales->count() : 0,
        ];

        $salesByMethod = $sales->groupBy('payment_method')->map(fn ($group) => [
            'count' => $group->count(),
            'total' => $group->sum('total'),
        ])->toArray();

        $topProducts = $sales->flatMap->details
            ->groupBy('product_id')
            ->map(fn ($details) => [
                'name' => $details->first()->product->name ?? 'Producto',
                'quantity' => $details->sum('quantity'),
                'total' => $details->sum('subtotal'),
            ])
            ->sortByDesc('total')
            ->take(10)
            ->values()
            ->toArray();

        $dailySales = $sales->groupBy(fn ($sale) => $sale->created_at->format('Y-m-d'))
            ->map(fn ($daySales) => $daySales->sum('total'))
            ->sortKeys()
            ->toArray();

        $categorySales = $sales->flatMap->details
            ->groupBy(fn ($d) => $d->product->category_id ?? 0)
            ->map(function ($details) {
                $categoryName = $details->first()->product->category->name ?? 'Sin categoría';

                return [
                    'name' => $categoryName,
                    'total' => $details->sum('subtotal'),
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->toArray();

        return view('modules.reports.index', [
            'stats' => $stats,
            'salesByMethod' => $salesByMethod,
            'topProducts' => $topProducts,
            'dailySales' => $dailySales,
            'categorySales' => $categorySales,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
    }
}
