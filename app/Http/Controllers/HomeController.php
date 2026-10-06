<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        // Get search and category filters from query params
        $search = trim((string) $request->query('search', ''));
        $categoryId = (int) $request->query('category', 0);
        
        // Base query: only active products
        $query = Product::with('category')
            ->where('product_status', 1)
            ->orderBy('product_id', 'desc');
            
        // Apply search filter
        if ($search !== '') {
            $query->whereRaw('LOWER(product_name) LIKE ?', ['%' . strtolower($search) . '%']);
        }
        
        // Apply category filter
        if ($categoryId !== 0) {
            $query->where('category_id', $categoryId);
        }
        
        $products = $query->get();
        $categories = Category::orderBy('category_name', 'asc')->get();
        
        return view('home', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'categoryId' => $categoryId,
        ]);
    }
}