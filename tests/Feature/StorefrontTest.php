<?php

namespace Tests\Feature;

use App\Http\Controllers\HomeController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            \Database\Seeders\CategorySeeder::class,
        ]);
    }

    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_home_controller_renders_storefront_view(): void
    {
        $category = Category::first();

        Product::create([
            'category_id' => $category->category_id,
            'product_name' => 'Produk Aktif Test',
            'product_price' => 15000,
            'product_description' => 'Deskripsi aktif',
            'product_status' => 1,
        ]);

        Product::create([
            'category_id' => $category->category_id,
            'product_name' => 'Produk Nonaktif Test',
            'product_price' => 25000,
            'product_description' => 'Deskripsi nonaktif',
            'product_status' => 0,
        ]);

        $controller = new HomeController();
        $view = $controller->index(Request::create('/', 'GET'));

        $this->assertEquals('home', $view->name());
        $this->assertCount(1, $view->getData()['products']);
        $this->assertEquals('Produk Aktif Test', $view->getData()['products']->first()->product_name);
    }
}

