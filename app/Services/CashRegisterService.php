<?php

namespace App\Services;

use App\Models\CashRegister;
use App\Models\Sale;
use Illuminate\Support\Carbon;

class CashRegisterService
{
    public function getStats(): array
    {
        $today = Carbon::today();

        $openCount = CashRegister::where('status', 'OPEN')->count();
        $closedToday = CashRegister::where('status', 'CLOSED')
            ->whereDate('closing_date', $today)
            ->count();
        $openedToday = CashRegister::whereDate('opening_date', $today)->count();

        $salesToday = Sale::notCancelled()
            ->whereDate('created_at', $today)
            ->sum('total');

        $salesByMethod = Sale::notCancelled()
            ->whereDate('created_at', $today)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->pluck('total', 'payment_method');

        return [
            'open_count' => $openCount,
            'closed_today' => $closedToday,
            'opened_today' => $openedToday,
            'sales_today' => $salesToday,
            'sales_by_method' => $salesByMethod,
        ];
    }

    public function open(int $userId, float $openingAmount, ?string $shift): CashRegister
    {
        return CashRegister::create([
            'user_id' => $userId,
            'shift' => $shift,
            'opening_amount' => $openingAmount,
            'status' => 'OPEN',
            'opening_date' => now(),
        ]);
    }

    public function close(CashRegister $register, float $actualClosingAmount, ?string $observations = null): CashRegister
    {
        $theoreticalAmount = $register->opening_amount + $register->sales()->notCancelled()->sum('total');
        $difference = $actualClosingAmount - $theoreticalAmount;

        $register->update([
            'theoretical_closing_amount' => $theoreticalAmount,
            'actual_closing_amount' => $actualClosingAmount,
            'difference' => $difference,
            'status' => 'CLOSED',
            'closing_date' => now(),
        ]);

        return $register->fresh();
    }

    public function getArqueo(CashRegister $register): array
    {
        $sales = $register->sales()
            ->notCancelled()
            ->with('customer')
            ->orderBy('created_at')
            ->get();

        $salesByMethod = $sales->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ];
        });

        $theoretical = $register->opening_amount + $sales->sum('total');

        return [
            'register' => $register,
            'sales' => $sales,
            'sales_by_method' => $salesByMethod,
            'total_sales' => $sales->sum('total'),
            'sales_count' => $sales->count(),
            'theoretical_amount' => $theoretical,
            'cash_sales' => $sales->where('payment_method', 'CASH')->sum('total'),
            'card_sales' => $sales->where('payment_method', 'CARD')->sum('total'),
            'transfer_sales' => $sales->where('payment_method', 'TRANSFER')->sum('total'),
        ];
    }
}
