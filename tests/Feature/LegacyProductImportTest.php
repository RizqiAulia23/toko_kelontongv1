<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LegacyProductImportTest extends TestCase
{
    use RefreshDatabase;

    protected array $legacyRows = [
        ['id_produk' => 1, 'nama_produk' => 'Indomie Goreng', 'kategori' => 'Makanan', 'harga' => 3500.00, 'stok' => 41],
        ['id_produk' => 2, 'nama_produk' => 'Beras 5kg', 'kategori' => 'Sembako', 'harga' => 75000.00, 'stok' => 17],
        ['id_produk' => 3, 'nama_produk' => 'Teh Celup', 'kategori' => 'Minuman', 'harga' => 8000.00, 'stok' => 37],
        ['id_produk' => 4, 'nama_produk' => 'Biskuit', 'kategori' => 'Makanan', 'harga' => 10000.00, 'stok' => 27],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\AdminSeeder::class);

        // Create legacy "produk" table in the in-memory test DB (READ-ONLY simulation)
        DB::statement('CREATE TABLE produk (
            id_produk INTEGER PRIMARY KEY,
            nama_produk VARCHAR(100),
            kategori VARCHAR(50),
            harga NUMERIC(12,2),
            stok INTEGER
        )');

        foreach ($this->legacyRows as $row) {
            DB::table('produk')->insert($row);
        }
    }

    protected function tearDown(): void
    {
        DB::statement('DROP TABLE IF EXISTS produk');
        parent::tearDown();
    }

    public function test_dry_run_does_not_modify_database(): void
    {
        Artisan::call('products:import-legacy');

        $this->assertDatabaseCount('tb_product', 0);
        $this->assertDatabaseCount('tb_category', 0);
        $this->assertDatabaseCount('produk', 4);
    }

    public function test_import_inserts_data_into_tb_product(): void
    {
        Artisan::call('products:import-legacy', ['--execute' => true]);

        $this->assertDatabaseCount('tb_product', 4);
    }

    public function test_category_id_matches_resolved_category(): void
    {
        Artisan::call('products:import-legacy', ['--execute' => true]);

        $makanan = Category::where('category_name', 'Makanan')->firstOrFail();
        $sembako = Category::where('category_name', 'Sembako')->firstOrFail();

        $this->assertSame($makanan->category_id, Product::where('product_name', 'Indomie Goreng')->firstOrFail()->category_id);
        $this->assertSame($sembako->category_id, Product::where('product_name', 'Beras 5kg')->firstOrFail()->category_id);
    }

    public function test_product_name_correct(): void
    {
        Artisan::call('products:import-legacy', ['--execute' => true]);

        $names = Product::pluck('product_name')->all();
        sort($names);
        $this->assertEquals(['Beras 5kg', 'Biskuit', 'Indomie Goreng', 'Teh Celup'], $names);
    }

    public function test_product_price_correct(): void
    {
        Artisan::call('products:import-legacy', ['--execute' => true]);

        $beras = Product::where('product_name', 'Beras 5kg')->firstOrFail();
        $this->assertEqualsWithDelta(75000.00, (float) $beras->product_price, 0.01);

        $teh = Product::where('product_name', 'Teh Celup')->firstOrFail();
        $this->assertEqualsWithDelta(8000.00, (float) $teh->product_price, 0.01);
    }

    public function test_product_status_defaults_to_one(): void
    {
        Artisan::call('products:import-legacy', ['--execute' => true]);
        $this->assertEquals(1, Product::firstOrFail()->product_status);
    }

    public function test_duplicate_prevented_on_second_run(): void
    {
        Artisan::call('products:import-legacy', ['--execute' => true]);
        Artisan::call('products:import-legacy', ['--execute' => true]);

        $this->assertDatabaseCount('tb_product', 4);
    }

    public function test_stock_not_written_to_tb_product(): void
    {
        Artisan::call('products:import-legacy', ['--execute' => true]);

        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasColumn('tb_product', 'stok'));
    }

    public function test_legacy_produk_table_untouched(): void
    {
        Artisan::call('products:import-legacy', ['--execute' => true]);

        $this->assertDatabaseCount('produk', 4);
        $this->assertDatabaseHas('produk', ['id_produk' => 1, 'nama_produk' => 'Indomie Goreng', 'stok' => 41]);
        $this->assertDatabaseHas('produk', ['id_produk' => 3, 'nama_produk' => 'Teh Celup', 'stok' => 37]);
    }
}