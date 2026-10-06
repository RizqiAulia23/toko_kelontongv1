<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
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

    public function test_guest_blocked(): void
    {
        $response = $this->get('/admin/categories');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_categories(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/categories');
        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
    }

    public function test_admin_can_access_create_form(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/categories/create');
        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.create');
    }

    public function test_admin_can_create_category(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $response = $this->actingAs($admin)->post('/admin/categories', [
            'category_name' => 'New Category Unique',
        ]);

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('tb_category', [
            'category_name' => 'New Category Unique',
        ]);
    }

    public function test_duplicate_category_rejected(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $category = Category::first();

        $response = $this->actingAs($admin)->post('/admin/categories', [
            'category_name' => $category->category_name,
        ]);

        $response->assertSessionHasErrors('category_name');
    }

    public function test_admin_can_edit_category(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $category = Category::first();

        $response = $this->actingAs($admin)->get("/admin/categories/{$category->category_id}/edit");
        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.edit');
    }

    public function test_admin_can_update_category(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $category = Category::first();

        $response = $this->actingAs($admin)->patch("/admin/categories/{$category->category_id}", [
            'category_name' => 'Renamed Category',
        ]);

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('tb_category', [
            'category_id' => $category->category_id,
            'category_name' => 'Renamed Category',
        ]);
    }

    public function test_unused_category_can_be_deleted(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $category = Category::create(['category_name' => 'Empty Cat']);

        $response = $this->actingAs($admin)->delete("/admin/categories/{$category->category_id}");
        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseMissing('tb_category', [
            'category_id' => $category->category_id,
        ]);
    }

    public function test_category_used_by_product_cannot_be_deleted(): void
    {
        $admin = Admin::where('username', 'admin')->first();
        $category = Category::first();

        Product::create([
            'category_id' => $category->category_id,
            'product_name' => 'Product Bound',
            'product_price' => 10000,
            'product_description' => 'desc',
            'product_image' => '',
            'product_status' => 1,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/categories/{$category->category_id}");
        $response->assertRedirect('/admin/categories');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('tb_category', [
            'category_id' => $category->category_id,
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
