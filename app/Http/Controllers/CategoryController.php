<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 10);
        $type = in_array($request->input('type'), ['parent', 'sub'], true) ? $request->input('type') : 'all';
        $status = in_array($request->input('status'), ['active', 'inactive'], true) ? $request->input('status') : 'all';

        $categories = Category::query()
            ->with('parent')
            ->withCount(['products', 'children'])
            ->when($type === 'parent', fn ($query) => $query->whereNull('parent_category_id'))
            ->when($type === 'sub', fn ($query) => $query->whereNotNull('parent_category_id'))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        return view('modules.categories.index', [
            'categories' => $categories,
            'type' => $type,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        $categories = Category::query()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('modules.categories.create', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'parent_category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $category = Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function show(Category $category): View
    {
        $category->load(['parent'])->loadCount(['products', 'children']);

        return view('modules.categories.show', ['category' => $category]);
    }

    public function edit(Category $category): View
    {
        $categories = Category::query()
            ->where('id', '!=', $category->id)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        return view('modules.categories.edit', [
            'category' => $category,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'parent_category_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $category->fill($validated);
        $category->is_active = $request->boolean('is_active');
        $category->save();

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function toggleActive(Category $category): RedirectResponse
    {
        $category->is_active = ! $category->is_active;

        Model::withoutEvents(fn () => $category->save());

        AuditService::log('TOGGLED', 'categories', $category->id, [
            'is_active' => $category->is_active,
        ]);

        return redirect()->route('categories.index')
            ->with('success', $category->is_active ? 'Categoría activada correctamente.' : 'Categoría deshabilitada correctamente.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
