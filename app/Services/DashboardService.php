<?php

namespace App\Services;

use App\Models\CashRegister;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    private const TREND_DAYS = 14;

    public function salesToday(): array
    {
        $sales = Sale::notCancelled()->whereDate('created_at', Carbon::today())->get();

        return [
            'count' => $sales->count(),
            'total' => round($sales->sum('total'), 2),
        ];
    }

    public function monthSalesTotal(): float
    {
        return round(Sale::notCancelled()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->get()
            ->sum('total'), 2);
    }

    public function lowStockCount(): int
    {
        return Product::query()
            ->where('is_active', true)
            ->get()
            ->filter(fn (Product $product) => $product->current_stock <= $product->min_stock)
            ->count();
    }

    public function inventoryValue(): float
    {
        return round(Product::query()->get()->sum(
            fn (Product $product) => $product->current_stock * $product->purchase_price
        ), 2);
    }

    public function openCashRegister(): ?CashRegister
    {
        return CashRegister::query()->where('status', 'OPEN')->latest('opening_date')->first();
    }

    public function salesTrend(): array
    {
        $from = Carbon::today()->subDays(self::TREND_DAYS - 1);

        $totalsByDate = Sale::notCancelled()
            ->where('created_at', '>=', $from)
            ->get()
            ->groupBy(fn (Sale $sale) => $sale->created_at->toDateString())
            ->map(fn (Collection $sales) => round($sales->sum('total'), 2));

        $dates = [];
        $totals = [];

        for ($i = 0; $i < self::TREND_DAYS; $i++) {
            $date = $from->copy()->addDays($i);
            $dates[] = $date->isoFormat('MMM D');
            $totals[] = $totalsByDate->get($date->toDateString(), 0);
        }

        return compact('dates', 'totals');
    }

    public function paymentMethodBreakdown(): array
    {
        return Sale::notCancelled()
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->groupBy('payment_method')
            ->map(fn (Collection $sales) => round($sales->sum('total'), 2))
            ->toArray();
    }

    public function topProducts(int $limit = 5): array
    {
        return SaleDetail::query()
            ->with(['product', 'sale'])
            ->get()
            ->reject(fn (SaleDetail $detail) => $detail->sale?->status === 'CANCELLED')
            ->groupBy('product_id')
            ->map(fn (Collection $details) => [
                'name' => $details->first()->product?->name ?? 'Product',
                'quantity' => $details->sum('quantity'),
            ])
            ->sortByDesc('quantity')
            ->take($limit)
            ->values()
            ->toArray();
    }

    public function lowStockProducts(): Collection
    {
        return Product::query()
            ->with('category')
            ->where('is_active', true)
            ->get()
            ->filter(fn (Product $product) => $product->current_stock <= $product->min_stock)
            ->sortBy('current_stock')
            ->take(8)
            ->values();
    }
}
