<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    // List semua categories
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $categories = Category::query()
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                           ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);
        
        return view('admin.categories.index', compact('categories', 'search'));
    }

    // Show form create
    public function create()
    {
        return view('admin.categories.create');
    }

    // Store new category
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
            'is_active' => 'boolean',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Handle icon upload
        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('categories', 'public');
            $validated['icon'] = $iconPath;
        }

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    // Show form edit
    public function edit(Category $category)
    {
        $game = $category;
        return view('admin.categories.edit', compact('game'));
    }

    // Update category
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'boolean',
            'account_fields' => 'nullable|json',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active') ? true : false;

        // Handle account fields
        if ($request->filled('account_fields')) {
            $validated['account_fields'] = json_decode($request->account_fields, true);
        } else {
            $validated['account_fields'] = null;
        }

        // Handle icon upload
        if ($request->hasFile('icon')) {
            // Delete old icon
            if ($category->icon && Storage::disk('public')->exists($category->icon)) {
                Storage::disk('public')->delete($category->icon);
            }

            $iconPath = $request->file('icon')->store('categories', 'public');
            $validated['icon'] = $iconPath;
        }

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    // Delete category
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Cannot delete category with products!');
        }

        // Delete icon
        if ($category->icon && Storage::disk('public')->exists($category->icon)) {
            Storage::disk('public')->delete($category->icon);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    // Toggle active status
    public function toggleActive(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category status updated!');
    }
}