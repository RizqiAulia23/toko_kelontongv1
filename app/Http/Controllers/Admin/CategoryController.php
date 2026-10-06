<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')
            ->orderBy('category_id', 'asc')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                Rule::unique('tb_category', 'category_name'),
            ],
        ], [
            'category_name.unique' => 'A category with this name already exists. Duplicates are not permitted.',
            'category_name.min' => 'Category name must be between 2 and 100 characters.',
            'category_name.max' => 'Category name must be between 2 and 100 characters.',
        ]);

        Category::create([
            'category_name' => trim($validated['category_name']),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category added successfully.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'category_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                Rule::unique('tb_category', 'category_name')->ignore($category->category_id, 'category_id'),
            ],
        ], [
            'category_name.unique' => 'A category with this name already exists. Duplicates are not permitted.',
            'category_name.min' => 'Category name must be between 2 and 100 characters.',
            'category_name.max' => 'Category name must be between 2 and 100 characters.',
        ]);

        $category->update([
            'category_name' => trim($validated['category_name']),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $productCount = $category->products()->count();

        if ($productCount > 0) {
            return redirect()->route('categories.index')->with(
                'error',
                "Cannot delete category: {$productCount} product(s) are currently mapped to it. Reassign or delete those products first."
            );
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
