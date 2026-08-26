<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 10);
        $status = in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'all';
        $type = in_array($request->input('type'), ['REGULAR', 'FREQUENT', 'WHOLESALER'], true) ? $request->input('type') : '';

        $customers = Customer::query()
            ->withCount('sales')
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($type !== '', fn ($query) => $query->where('customer_type', $type))
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate($perPage)
            ->withQueryString();

        return view('modules.customers.index', [
            'customers' => $customers,
            'status' => $status,
            'customerType' => $type,
        ]);
    }

    public function create(): View
    {
        $customersCount = Customer::count();

        return view('modules.customers.create', ['customersCount' => $customersCount]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'tax_id' => ['nullable', 'string', 'max:20', 'unique:customers,tax_id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'customer_type' => ['required', 'in:REGULAR,FREQUENT,WHOLESALER'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function show(Customer $customer): View
    {
        $customer->loadCount('sales');

        return view('modules.customers.show', ['customer' => $customer]);
    }

    public function edit(Customer $customer): View
    {
        return view('modules.customers.edit', ['customer' => $customer]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'tax_id' => ['nullable', 'string', 'max:20', 'unique:customers,tax_id,'.$customer->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'customer_type' => ['required', 'in:REGULAR,FREQUENT,WHOLESALER'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $customer->fill($validated);
        $customer->is_active = $request->boolean('is_active');
        $customer->save();

        return redirect()->route('customers.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function toggleActive(Customer $customer): RedirectResponse
    {
        $customer->is_active = ! $customer->is_active;

        Model::withoutEvents(fn () => $customer->save());

        AuditService::log('TOGGLED', 'customers', $customer->id, [
            'is_active' => $customer->is_active,
        ]);

        return redirect()->route('customers.index')
            ->with('success', $customer->is_active ? 'Cliente activado correctamente.' : 'Cliente deshabilitado correctamente.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->sales()->exists()) {
            return redirect()->route('customers.index')
                ->with('error', 'No se puede eliminar el cliente porque tiene ventas registradas. Puedes deshabilitarlo.');
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
