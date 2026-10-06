<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('product_status', 1)->count();
        $totalCategories = Category::count();

        $recentProducts = Product::with('category')
            ->orderBy('product_id', 'desc')
            ->limit(5)
            ->get();

        $categories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'activeProducts',
            'totalCategories',
            'recentProducts',
            'categories'
        ));
    }
}
