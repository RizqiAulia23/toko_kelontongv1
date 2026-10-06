<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([
            \Database\Seeders\AdminSeeder::class,
            \Database\Seeders\CategorySeeder::class,
        ]);
    }

    public function test_guest_cannot_access_products(): void
    {
        $response = $this->get('/admin/products');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_access_products(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/products');
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.index');
    }

    public function test_admin_can_access_create_form(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/products/create');
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.create');
    }

    public function test_admin_can_create_product(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $category = Category::first();

        $response = $this->actingAs($admin)->post('/admin/products', [
            'product_name' => 'Test Product',
            'category_id' => $category->category_id,
            'product_price' => 50000,
            'product_description' => 'Test description',
            'product_status' => 1,
        ]);

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('tb_product', [
            'product_name' => 'Test Product',
            'category_id' => $category->category_id,
            'product_price' => 50000,
        ]);
    }

    public function test_product_creation_requires_name(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $category = Category::first();

        $response = $this->actingAs($admin)->post('/admin/products', [
            'product_name' => '',
            'category_id' => $category->category_id,
            'product_price' => 50000,
            'product_description' => 'Test description',
            'product_status' => 1,
        ]);

        $response->assertSessionHasErrors('product_name');
    }

    public function test_product_creation_requires_valid_category(): void
    {
        $admin = Admin::where('username', 'admin')->first();

        $response = $this->actingAs($admin)->post('/admin/products', [
            'product_name' => 'Test Product',
            'category_id' => 99999,
            'product_price' => 50000,
            'product_description' => 'Test description',
            'product_status' => 1,
        ]);

        $response->assertSessionHasErrors('category_id');
    }

    public function test_admin_can_edit_product(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $product = Product::create([
            'category_id' => Category::first()->category_id,
            'product_name' => 'Original Name',
            'product_price' => 50000,
            'product_description' => 'Test',
            'product_image' => '',
            'product_status' => 1,
        ]);

        $response = $this->actingAs($admin)->get("/admin/products/{$product->product_id}/edit");
        $response->assertStatus(200);
        $response->assertViewIs('admin.products.edit');
    }

    public function test_admin_can_update_product(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $product = Product::create([
            'category_id' => Category::first()->category_id,
            'product_name' => 'Original Name',
            'product_price' => 50000,
            'product_description' => 'Test',
            'product_image' => '',
            'product_status' => 1,
        ]);

        $response = $this->actingAs($admin)->put("/admin/products/{$product->product_id}", [
            'product_name' => 'Updated Name',
            'category_id' => Category::first()->category_id,
            'product_price' => 60000,
            'product_description' => 'Updated description',
            'product_status' => 1,
        ]);

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('tb_product', [
            'product_id' => $product->product_id,
            'product_name' => 'Updated Name',
            'product_price' => 60000,
        ]);
    }

    public function test_admin_can_toggle_product_status(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $product = Product::create([
            'category_id' => Category::first()->category_id,
            'product_name' => 'Test Product',
            'product_price' => 50000,
            'product_description' => 'Test',
            'product_image' => '',
            'product_status' => 1,
        ]);

        $response = $this->actingAs($admin)->patch("/admin/products/{$product->product_id}/toggle-status");
        $response->assertRedirect('/admin/products');

        $product->refresh();
        $this->assertEquals(0, $product->product_status);
    }

    public function test_admin_can_delete_product(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $product = Product::create([
            'category_id' => Category::first()->category_id,
            'product_name' => 'Delete Me',
            'product_price' => 50000,
            'product_description' => 'Test',
            'product_image' => '',
            'product_status' => 1,
        ]);

        $productId = $product->product_id;

        $response = $this->actingAs($admin)->delete("/admin/products/{$product->product_id}");
        $response->assertRedirect('/admin/products');

        $this->assertDatabaseMissing('tb_product', [
            'product_id' => $productId,
        ]);
    }

    public function test_existing_pgsql_tables_untouched(): void
    {
        $pdo = new \PDO('pgsql:host=127.0.0.1;port=5432;dbname=toko_kelontong', 'postgres', '05112010');
        $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema='public'");
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $this->assertContains('pelanggan', $tables);
        $this->assertContains('produk', $tables);
        $this->assertContains('transaksi', $tables);
    }
}
