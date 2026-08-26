<?php

namespace App\Services;

use App\Models\CashRegister;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PosService
{
    public function getActiveProducts(string $search = ''): Collection
    {
        $query = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->where('current_stock', '>', 0);

        if ($search !== '') {
            $term = strtolower($search);
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(barcode) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$term}%"]);
            });
        }

        return $query->orderBy('name')->get();
    }

    public function searchCustomers(string $search): Collection
    {
        if ($search === '') {
            return Customer::query()->where('is_active', true)->orderBy('first_name')->limit(20)->get();
        }

        $term = strtolower($search);

        return Customer::query()
            ->where('is_active', true)
            ->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(first_name) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(tax_id) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$term}%"]);
            })
            ->orderBy('first_name')
            ->limit(20)
            ->get();
    }

    public function openCashRegister(int $userId, float $openingAmount, string $shift): CashRegister
    {
        return CashRegister::create([
            'user_id' => $userId,
            'shift' => $shift,
            'opening_amount' => $openingAmount,
            'status' => 'OPEN',
            'opening_date' => now(),
        ]);
    }

    public function getOpenCashRegister(): ?CashRegister
    {
        return CashRegister::query()
            ->where('status', 'OPEN')
            ->latest('opening_date')
            ->first();
    }

    public function getOpenCashRegisterForUser(int $userId): ?CashRegister
    {
        return CashRegister::query()
            ->where('status', 'OPEN')
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('responsible_id', $userId);
            })
            ->latest('opening_date')
            ->first();
    }

    public function completeSale(array $items, int $userId, ?int $cashRegisterId, ?int $customerId, string $paymentMethod, float $amountReceived, ?string $observations = null): Sale
    {
        $sale = DB::transaction(function () use ($items, $userId, $cashRegisterId, $customerId, $paymentMethod, $amountReceived, $observations) {
            $subtotal = 0;
            $taxTotal = 0;

            foreach ($items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->current_stock < $item['quantity']) {
                    throw new \RuntimeException("Stock insuficiente para {$product->name}. Disponible: {$product->current_stock}");
                }

                $lineSubtotal = round($item['quantity'] * $item['unit_price'] - ($item['discount'] ?? 0), 2);
                $subtotal += $lineSubtotal;

                if ($product->has_tax) {
                    $taxTotal += round($lineSubtotal * ($product->tax_percentage / 100), 2);
                }

                $product->decrement('current_stock', $item['quantity']);
            }

            $total = round($subtotal + $taxTotal, 2);
            $changeDue = $paymentMethod === 'CASH' ? max(0, round($amountReceived - $total, 2)) : 0;

            $sale = Sale::create([
                'cash_register_id' => $cashRegisterId,
                'user_id' => $userId,
                'customer_id' => $customerId,
                'ticket_number' => $this->generateTicketNumber(),
                'total' => $total,
                'amount_received' => $amountReceived,
                'change_due' => $changeDue,
                'payment_method' => $paymentMethod,
                'status' => 'COMPLETED',
                'observations' => $observations,
                'created_at' => now(),
            ]);

            foreach ($items as $item) {
                $product = Product::find($item['product_id']);

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'unit_cost' => $product->purchase_price,
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => round($item['quantity'] * $item['unit_price'] - ($item['discount'] ?? 0), 2),
                ]);
            }

            return $sale->load(['details.product', 'customer', 'cashRegister']);
        });

        if ($customerId) {
            $this->evaluateCustomerType($customerId);
        }

        return $sale;
    }

    private function evaluateCustomerType(int $customerId): void
    {
        $customer = Customer::where('id', $customerId)->first();

        if (! $customer || $customer->customer_type === 'WHOLESALER') {
            return;
        }

        $lastSaleDate = Sale::where('customer_id', $customerId)
            ->latest('created_at')
            ->value('created_at');

        if ($customer->customer_type === 'FREQUENT' && $lastSaleDate && $lastSaleDate->diffInDays(now()) > 45) {
            $customer->update(['customer_type' => 'REGULAR']);

            return;
        }

        $recentSales = Sale::where('customer_id', $customerId)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        if ($recentSales > 5 && $customer->customer_type === 'REGULAR') {
            $customer->update(['customer_type' => 'FREQUENT']);
        }
    }

    private function generateTicketNumber(): string
    {
        $date = now()->format('Ymd');
        $lastSale = Sale::where('ticket_number', 'LIKE', "T{$date}-%")
            ->latest('id')
            ->value('ticket_number');

        $sequence = 1;

        if ($lastSale) {
            $lastSequence = (int) substr($lastSale, -4);
            $sequence = $lastSequence + 1;
        }

        return sprintf('T%s-%04d', $date, $sequence);
    }
}
