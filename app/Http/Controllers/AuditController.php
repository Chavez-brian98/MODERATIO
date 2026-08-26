<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductReturn;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 25);

        $dateFrom = $this->filterDate($request->input('date_from'));
        $dateTo = $this->filterDate($request->input('date_to'));

        $logs = AuditLog::query()
            ->with('user')
            ->when($dateFrom !== null, fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo !== null, fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(max(1, $perPage))
            ->withQueryString();

        return view('modules.audit.index', [
            'logs' => $logs,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    public function show(AuditLog $log): View
    {
        $log->load(['user', 'user.roles']);

        return view('modules.audit.show', [
            'log' => $log,
            'affected' => $this->affectedRecord($log),
        ]);
    }

    private function affectedRecord(AuditLog $log): ?array
    {
        if ($log->record_id === null) {
            return null;
        }

        $modelMap = [
            'users' => User::class,
            'customers' => Customer::class,
            'products' => Product::class,
            'categories' => Category::class,
            'roles' => Role::class,
            'sales' => Sale::class,
            'cash_registers' => CashRegister::class,
            'returns' => ProductReturn::class,
        ];

        $modelClass = $modelMap[$log->affected_table] ?? null;

        if ($modelClass === null) {
            return null;
        }

        $record = $modelClass::query()->find($log->record_id);
        $snapshot = $log->new_values ?? $log->old_values ?? [];

        $title = $record?->full_name
            ?? $record?->name
            ?? $record?->ticket_number;

        if (($title === null || $title === '') && $record !== null) {
            $title = match ($log->affected_table) {
                'customers' => trim(($record->first_name ?? '').' '.($record->last_name ?? '')),
                'cash_registers' => 'Caja #'.$record->id,
                'returns' => 'Devolución #'.$record->id,
                default => null,
            };
        }

        if ($title === null || $title === '') {
            $title = $snapshot['full_name']
                ?? $snapshot['name']
                ?? (isset($snapshot['first_name'], $snapshot['last_name'])
                    ? trim($snapshot['first_name'].' '.$snapshot['last_name'])
                    : '#'.$log->record_id);
        }

        $subtitle = $record?->email
            ?? ($record !== null && $record->tax_id !== null ? 'NIT: '.$record->tax_id : null);

        return [
            'exists' => $record !== null,
            'title' => $title !== '' ? $title : '#'.$log->record_id,
            'subtitle' => $subtitle,
        ];
    }

    private function filterDate(?string $value): ?string
    {
        if ($value === null || $value === '' || ! Carbon::canBeCreatedFromFormat($value, 'Y-m-d')) {
            return null;
        }

        $date = Carbon::createFromFormat('Y-m-d', $value);

        return $date === false ? null : $date->toDateString();
    }
}
