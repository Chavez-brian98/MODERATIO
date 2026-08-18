<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->with('parent')
            ->withCount(['products', 'children'])
            ->get();

        return view('modules.categories.index', ['categories' => $categories]);
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

        Category::create($validated);

        return redirect()->route('categories.index');
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

        return redirect()->route('categories.index');
    }

    public function toggleActive(Category $category): RedirectResponse
    {
        $category->is_active = ! $category->is_active;
        $category->save();

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('categories.index');
    }
}
