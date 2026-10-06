<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category')->orderBy('product_id', 'desc');

        if ($search = trim((string) $request->query('search', ''))) {
            $query->whereRaw('LOWER(product_name) LIKE ?', ['%' . strtolower($search) . '%']);
        }

        if ($categoryId = (int) $request->query('category', 0)) {
            $query->where('category_id', $categoryId);
        }

        $status = $request->query('status');
        if ($status !== null && $status !== '') {
            $query->where('product_status', (int) $status);
        }

        $products = $query->get();
        $categories = Category::orderBy('category_name', 'asc')->get();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search ?? '',
            'categoryId' => $categoryId ?? 0,
            'status' => $status ?? '',
        ]);
    }

    public function create(): View
    {
        $categories = Category::orderBy('category_name', 'asc')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:tb_category,category_id'],
            'product_price' => ['required', 'numeric', 'min:0'],
            'product_description' => ['required', 'string'],
            'product_status' => ['required', 'in:0,1'],
            'product_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:5120'],
        ]);

        $imageFilename = '';
        if ($request->hasFile('product_image')) {
            $imageFilename = $this->storeImage($request->file('product_image'));
        }

        Product::create([
            'category_id' => $validated['category_id'],
            'product_name' => $validated['product_name'],
            'product_price' => $validated['product_price'],
            'product_description' => $validated['product_description'],
            'product_image' => $imageFilename,
            'product_status' => $validated['product_status'],
        ]);

        return redirect()->route('products.index')->with('success', 'Product added successfully.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('category_name', 'asc')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:tb_category,category_id'],
            'product_price' => ['required', 'numeric', 'min:0'],
            'product_description' => ['required', 'string'],
            'product_status' => ['required', 'in:0,1'],
            'product_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:5120'],
        ]);

        $oldImage = $product->product_image;
        $newImage = $oldImage;

        if ($request->hasFile('product_image')) {
            $uploaded = $this->storeImage($request->file('product_image'));
            $newImage = $uploaded;
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'product_name' => $validated['product_name'],
            'product_price' => $validated['product_price'],
            'product_description' => $validated['product_description'],
            'product_image' => $newImage,
            'product_status' => $validated['product_status'],
        ]);

        if (isset($uploaded) && $uploaded && $oldImage) {
            $this->deleteImage($oldImage);
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $image = $product->product_image;
        $product->delete();

        if ($image) {
            $this->deleteImage($image);
        }

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->update([
            'product_status' => $product->product_status == 1 ? 0 : 1,
        ]);

        $status = $product->product_status == 1 ? 'Active' : 'Inactive';

        return redirect()->route('products.index')->with('success', "Product status updated to {$status}.");
    }

    private function storeImage($file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (! in_array($extension, $allowed, true)) {
            $extension = 'jpg';
        }

        $filename = 'p_' . bin2hex(random_bytes(8)) . '.' . $extension;

        $file->storeAs('products', $filename, 'public');

        return $filename;
    }

    private function deleteImage(?string $filename): void
    {
        if (! $filename) {
            return;
        }

        $path = 'products/' . basename($filename);

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
