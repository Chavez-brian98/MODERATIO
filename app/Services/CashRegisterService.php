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

    public function open(int $userId, float $openingAmount, ?string $shift, ?int $responsibleId = null): CashRegister
    {
        return CashRegister::create([
            'user_id' => $userId,
            'responsible_id' => $responsibleId,
            'shift' => $shift,
            'opening_amount' => $openingAmount,
            'status' => 'OPEN',
            'opening_date' => now(),
        ]);
    }

    public function close(CashRegister $register, float $actualClosingAmount, ?string $notes = null): CashRegister
    {
        $theoreticalAmount = $this->expectedCash($register);
        $difference = round($actualClosingAmount - $theoreticalAmount, 2);

        $register->update([
            'theoretical_closing_amount' => $theoreticalAmount,
            'actual_closing_amount' => $actualClosingAmount,
            'difference' => $difference,
            'closing_notes' => $notes,
            'status' => 'CLOSED',
            'closing_date' => now(),
        ]);

        return $register->fresh();
    }

    /**
     * Fix typos on a register. When the register is already closed, the
     * theoretical amount and difference are recalculated against the new data.
     */
    public function updateDetails(
        CashRegister $register,
        float $openingAmount,
        ?string $shift,
        ?int $responsibleId,
        ?string $notes,
        ?float $actualClosingAmount = null,
    ): CashRegister {
        $register->opening_amount = $openingAmount;
        $register->shift = $shift;
        $register->responsible_id = $responsibleId;
        $register->closing_notes = $notes;

        if ($register->status === 'CLOSED') {
            $register->actual_closing_amount = $actualClosingAmount ?? $register->actual_closing_amount;
            $register->theoretical_closing_amount = $this->expectedCash($register);
            $register->difference = round((float) $register->actual_closing_amount - (float) $register->theoretical_closing_amount, 2);
        }

        $register->save();

        return $register->fresh();
    }

    public function getArqueo(CashRegister $register): array
    {
        $sales = $register->sales()
            ->notCancelled()
            ->with(['customer', 'details.product'])
            ->orderBy('created_at')
            ->get();

        $salesByMethod = $sales->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total'),
            ];
        });

        $cashSales = $sales->where('payment_method', 'CASH')->sum('total');

        return [
            'register' => $register,
            'sales' => $sales,
            'sales_by_method' => $salesByMethod,
            'total_sales' => $sales->sum('total'),
            'sales_count' => $sales->count(),
            'theoretical_amount' => $register->opening_amount + $cashSales,
            'cash_sales' => $cashSales,
            'card_sales' => $sales->where('payment_method', 'CARD')->sum('total'),
            'transfer_sales' => $sales->where('payment_method', 'TRANSFER')->sum('total'),
        ];
    }

    /**
     * Money that should be inside the drawer: opening float plus cash sales
     * only. Card and transfer payments never touch the drawer.
     */
    public function expectedCash(CashRegister $register): float
    {
        $cashSales = $register->sales()
            ->notCancelled()
            ->where('payment_method', 'CASH')
            ->sum('total');

        return (float) $register->opening_amount + (float) $cashSales;
    }
}
