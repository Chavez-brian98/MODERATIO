<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\User;
use App\Services\AuditService;
use App\Services\CashRegisterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CashRegisterController extends Controller
{
    public function __construct(
        private readonly CashRegisterService $service,
    ) {}

    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 10);

        $registers = CashRegister::query()
            ->with(['user', 'responsible'])
            ->withCount(['sales' => fn ($q) => $q->notCancelled()])
            ->withSum(['sales' => fn ($q) => $q->notCancelled()], 'total')
            ->orderByDesc('opening_date')
            ->paginate($perPage)
            ->withQueryString();

        $stats = $this->service->getStats();

        return view('modules.cash-registers.index', [
            'registers' => $registers,
            'stats' => $stats,
        ]);
    }

    public function create(): View
    {
        return view('modules.cash-registers.create', [
            'employees' => $this->posCapableEmployees(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opening_amount' => ['required', 'numeric', 'min:0'],
            'shift' => ['nullable', 'string', 'max:20'],
            'responsible_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $register = $this->service->open(
            userId: (int) auth()->id(),
            openingAmount: (float) $validated['opening_amount'],
            shift: $validated['shift'] ?? null,
            responsibleId: $validated['responsible_id'] ?? null,
        );

        AuditService::log('OPENED', 'cash_registers', $register->id, [
            'opening_amount' => $register->opening_amount,
            'shift' => $register->shift,
            'responsible_id' => $register->responsible_id,
        ]);

        return redirect()->route('cash-register.index')
            ->with('success', 'Caja abierta correctamente.');
    }

    public function show(CashRegister $cashRegister): View
    {
        $cashRegister->load(['user', 'responsible']);

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
            'closing_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->service->close(
            register: $cashRegister,
            actualClosingAmount: (float) $validated['actual_closing_amount'],
            notes: $validated['closing_notes'] ?? null,
        );

        $cashRegister->refresh();

        AuditService::log('CLOSED', 'cash_registers', $cashRegister->id, [
            'opening_amount' => $cashRegister->opening_amount,
            'theoretical_closing_amount' => $cashRegister->theoretical_closing_amount,
            'actual_closing_amount' => $cashRegister->actual_closing_amount,
            'difference' => $cashRegister->difference,
            'closing_notes' => $cashRegister->closing_notes,
        ]);

        return redirect()->route('cash-register.show', $cashRegister)
            ->with('success', 'Caja cerrada correctamente.');
    }

    public function edit(CashRegister $cashRegister): View
    {
        $cashRegister->load(['user', 'responsible']);

        return view('modules.cash-registers.edit', [
            'register' => $cashRegister,
            'employees' => $this->posCapableEmployees($cashRegister->id),
            'theoretical' => $this->service->expectedCash($cashRegister),
        ]);
    }

    public function update(Request $request, CashRegister $cashRegister): RedirectResponse
    {
        $validated = $request->validate([
            'opening_amount' => ['required', 'numeric', 'min:0'],
            'shift' => ['nullable', 'in:MORNING,AFTERNOON,NIGHT'],
            'responsible_id' => ['nullable', 'integer', 'exists:users,id'],
            'closing_notes' => ['nullable', 'string', 'max:500'],
            'actual_closing_amount' => [
                Rule::requiredIf($cashRegister->status === 'CLOSED'),
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        $before = $cashRegister->only([
            'opening_amount', 'shift', 'responsible_id', 'closing_notes',
            'actual_closing_amount', 'difference',
        ]);

        $this->service->updateDetails(
            register: $cashRegister,
            openingAmount: (float) $validated['opening_amount'],
            shift: $validated['shift'] ?? null,
            responsibleId: $validated['responsible_id'] ?? null,
            notes: $validated['closing_notes'] ?? null,
            actualClosingAmount: isset($validated['actual_closing_amount']) ? (float) $validated['actual_closing_amount'] : null,
        );

        $after = $cashRegister->fresh()->only([
            'opening_amount', 'shift', 'responsible_id', 'closing_notes',
            'actual_closing_amount', 'difference',
        ]);

        $changes = [];
        foreach ($after as $key => $value) {
            if ((string) $before[$key] !== (string) $value) {
                $changes[$key] = ['before' => $before[$key], 'after' => $value];
            }
        }

        if ($changes !== []) {
            AuditService::log('UPDATED', 'cash_registers', $cashRegister->id, $changes);
        }

        return redirect()->route('cash-register.show', $cashRegister)
            ->with('success', 'Caja actualizada correctamente.');
    }

    public function toggleStatus(CashRegister $cashRegister): RedirectResponse
    {
        $newStatus = $cashRegister->status === 'OPEN' ? 'CLOSED' : 'OPEN';

        $changes = ['status' => ['before' => $cashRegister->status, 'after' => $newStatus]];

        $data = ['status' => $newStatus];

        if ($newStatus === 'CLOSED') {
            $data['closing_date'] = now();
        } else {
            $data['closing_date'] = null;
            $data['actual_closing_amount'] = null;
            $data['theoretical_closing_amount'] = null;
            $data['difference'] = null;
            $data['closing_notes'] = null;
        }

        CashRegister::withoutEvents(fn () => $cashRegister->update($data));

        AuditService::log('TOGGLED', 'cash_registers', $cashRegister->id, $changes);

        $label = $newStatus === 'OPEN' ? 'abierta' : 'cerrada';

        return redirect()->route('cash-register.index')
            ->with('success', "Caja #$cashRegister->id {$label} correctamente.");
    }

    /**
     * Users eligible as "encargado de caja": active users with effective
     * access to the POS (sales_view permission), excluding those already
     * involved in an open register (as opener or responsible).
     *
     * @param  int|null  $exceptRegisterId  When editing, exclude this register
     *                                      from the check so the current
     *                                      responsible stays in the dropdown.
     */
    private function posCapableEmployees(?int $exceptRegisterId = null): Collection
    {
        $query = User::query()
            ->where('is_active', true)
            ->withEffectivePermission('sales_view')
            ->orderBy('full_name');

        $query->whereNotIn('id', function ($sub) use ($exceptRegisterId) {
            $sub->select('user_id')
                ->from('cash_registers')
                ->where('status', 'OPEN')
                ->unionAll(
                    DB::table('cash_registers')
                        ->select('responsible_id')
                        ->where('status', 'OPEN')
                        ->whereNotNull('responsible_id')
                );

            if ($exceptRegisterId !== null) {
                $sub->where('id', '!=', $exceptRegisterId);
            }
        });

        return $query->get(['id', 'full_name']);
    }
}
