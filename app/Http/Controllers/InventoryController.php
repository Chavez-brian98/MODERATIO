<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with('category')
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'low_stock' => Product::where('is_active', true)
                ->whereColumn('current_stock', '<=', 'min_stock')
                ->where('current_stock', '>', 0)
                ->count(),
            'out_of_stock' => Product::where('is_active', true)
                ->where('current_stock', '<=', 0)
                ->count(),
        ];

        return view('modules.inventory.index', [
            'products' => $products,
            'stats' => $stats,
        ]);
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('modules.inventory.create', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'has_tax' => ['sometimes', 'boolean'],
            'tax_percentage' => ['required_if:has_tax,true', 'nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $validated['has_tax'] = $request->boolean('has_tax');
        if (! $validated['has_tax']) {
            $validated['tax_percentage'] = 0;
        }

        $product = Product::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(Product $product): View
    {
        $product->load('category');

        return view('modules.inventory.show', ['product' => $product]);
    }

    public function edit(Product $product): View
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('modules.inventory.edit', [
            'product' => $product,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode,'.$product->id],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
            'has_tax' => ['sometimes', 'boolean'],
            'tax_percentage' => ['required_if:has_tax,true', 'nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $validated['has_tax'] = $request->boolean('has_tax');
        if (! $validated['has_tax']) {
            $validated['tax_percentage'] = 0;
        }

        $product->fill($validated);
        $product->save();

        return redirect()->route('inventory.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function toggleActive(Product $product): RedirectResponse
    {
        $product->is_active = ! $product->is_active;

        Model::withoutEvents(fn () => $product->save());

        AuditService::log('TOGGLED', 'products', $product->id, [
            'is_active' => $product->is_active,
        ]);

        return redirect()->route('inventory.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('inventory.index');
    }
}
