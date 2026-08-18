<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $dashboard): View
    {
        return view('dashboard', [
            'salesToday' => $dashboard->salesToday(),
            'monthSalesTotal' => $dashboard->monthSalesTotal(),
            'lowStockCount' => $dashboard->lowStockCount(),
            'inventoryValue' => $dashboard->inventoryValue(),
            'openCashRegister' => $dashboard->openCashRegister(),
            'salesTrend' => $dashboard->salesTrend(),
            'paymentMethods' => $dashboard->paymentMethodBreakdown(),
            'topProducts' => $dashboard->topProducts(),
            'lowStockProducts' => $dashboard->lowStockProducts(),
        ]);
    }
}
