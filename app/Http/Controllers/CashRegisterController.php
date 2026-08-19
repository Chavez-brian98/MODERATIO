<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Services\AuditService;
use App\Services\CashRegisterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashRegisterController extends Controller
{
    public function __construct(
        private readonly CashRegisterService $service,
    ) {}

    public function index(): View
    {
        $registers = CashRegister::query()
            ->with('user')
            ->withCount(['sales' => fn ($q) => $q->notCancelled()])
            ->withSum(['sales' => fn ($q) => $q->notCancelled()], 'total')
            ->orderByDesc('opening_date')
            ->get();

        $stats = $this->service->getStats();

        return view('modules.cash-registers.index', [
            'registers' => $registers,
            'stats' => $stats,
        ]);
    }

    public function create(): View
    {
        return view('modules.cash-registers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opening_amount' => ['required', 'numeric', 'min:0'],
            'shift' => ['nullable', 'string', 'max:20'],
        ]);

        $register = $this->service->open(
            userId: (int) session('user_id', 1),
            openingAmount: (float) $validated['opening_amount'],
            shift: $validated['shift'] ?? null,
        );

        AuditService::log('OPENED', 'cash_registers', $register->id, [
            'opening_amount' => $register->opening_amount,
            'shift' => $register->shift,
        ]);

        return redirect()->route('cash-register.index')
            ->with('success', 'Caja abierta correctamente.');
    }

    public function show(CashRegister $cashRegister): View
    {
        $cashRegister->load('user');
        $cashRegister->loadCount(['sales' => fn ($q) => $q->notCancelled()]);
        $cashRegister->loadSum(['sales' => fn ($q) => $q->notCancelled()], 'total_sales');

        $arqueo = $this->service->getArqueo($cashRegister);

        return view('modules.cash-registers.show', [
            'register' => $cashRegister,
            'arqueo' => $arqueo,
        ]);
    }

    public function close(Request $request, CashRegister $cashRegister): RedirectResponse
    {
        $validated = $request->validate([
            'actual_closing_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $this->service->close(
            register: $cashRegister,
            actualClosingAmount: (float) $validated['actual_closing_amount'],
        );

        $cashRegister->refresh();

        AuditService::log('CLOSED', 'cash_registers', $cashRegister->id, [
            'opening_amount' => $cashRegister->opening_amount,
            'theoretical_closing_amount' => $cashRegister->theoretical_closing_amount,
            'actual_closing_amount' => $cashRegister->actual_closing_amount,
            'difference' => $cashRegister->difference,
        ]);

        return redirect()->route('cash-register.show', $cashRegister)
            ->with('success', 'Caja cerrada correctamente.');
    }
}
