<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        
        $products = Product::query()
            ->with('category')
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                           ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($category, function($query, $category) {
                return $query->where('category_id', $category);
            })
            ->latest()
            ->paginate(10);
        
        $categories = Category::active()->get();
        
        return view('admin.products.index', compact('products', 'search', 'category', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'code_format' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Upload image
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                
                if (!$file->isValid()) {
                    throw new \Exception('File upload is not valid');
                }
                
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = storage_path('app/public/products');
                
                if (!file_exists($destinationPath)) {
                    if (!mkdir($destinationPath, 0755, true)) {
                        throw new \Exception('Failed to create directory: ' . $destinationPath);
                    }
                }
                
                if (!is_writable($destinationPath)) {
                    throw new \Exception('Directory is not writable: ' . $destinationPath);
                }
                
                if (!$file->move($destinationPath, $filename)) {
                    throw new \Exception('Failed to move uploaded file');
                }
                
                $finalPath = $destinationPath . DIRECTORY_SEPARATOR . $filename;
                if (!file_exists($finalPath)) {
                    throw new \Exception('File was not saved: ' . $finalPath);
                }
                
                $validated['image'] = 'products/' . $filename;
                
                \Log::info('Product image uploaded successfully', [
                    'filename' => $filename,
                    'path' => $finalPath,
                    'size' => filesize($finalPath)
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Product image upload failed', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
                
                return back()
                    ->withInput()
                    ->with('error', 'Image upload failed: ' . $e->getMessage());
            }
        }

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'code_format' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        // Upload new image
        if ($request->hasFile('image')) {
            try {
                // Delete old image
                if ($product->image) {
                    $oldPath = storage_path('app/public/' . $product->image);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = storage_path('app/public/products');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                $file->move($destinationPath, $filename);
                
                $validated['image'] = 'products/' . $filename;
            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->with('error', 'Image upload failed: ' . $e->getMessage());
            }
        }

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        // Check if product has transactions
        if ($product->transactions()->count() > 0) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Cannot delete product with existing transactions!');
        }

        // Delete image
        if ($product->image) {
            $imagePath = storage_path('app/public/' . $product->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Delete related voucher codes
        $product->voucherCodes()->delete();

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    public function toggleActive(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product status updated!');
    }
}