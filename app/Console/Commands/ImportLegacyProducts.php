<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLegacyProducts extends Command
{
    protected $signature = 'products:import-legacy {--execute : Perform actual INSERT into tb_product}';

    protected $description = 'Safely import legacy produk rows into tb_product (read-only source)';

    public function handle(): int
    {
        $isExecute = (bool) $this->option('execute');

        $this->line($isExecute ? 'LEGACY PRODUCT IMPORT — EXECUTE' : 'LEGACY PRODUCT IMPORT — DRY RUN');
        $this->line('');
        $this->line('Source: produk');
        $this->line('Target: tb_product');
        $this->line('');

        $legacyRows = DB::select('SELECT id_produk, nama_produk, kategori, harga, stok FROM produk ORDER BY id_produk');

        $legacyCount = count($legacyRows);
        $alreadyExisting = 0;
        $willImport = 0;
        $skipped = 0;
        $invalid = 0;
        $stockDetected = 0;
        $missingCategories = [];
        $categoriesMatched = 0;
        $categoriesToCreate = [];
        $invalidRows = [];

        $categoryCache = [];
        $loadCategories = function () use (&$categoryCache) {
            $categoryCache = [];
            foreach (Category::all() as $cat) {
                $categoryCache[mb_strtolower(trim($cat->category_name))] = $cat;
            }
        };
        $loadCategories();

        $plan = [];

        foreach ($legacyRows as $row) {
            if ($row->stok !== null) {
                $stockDetected++;
            }

            $name = trim((string) ($row->nama_produk ?? ''));
            $rawCat = $row->kategori ?? '';
            $normCat = trim((string) $rawCat);
            $catKey = mb_strtolower($normCat);
            $price = $row->harga;

            if ($name === '' || $normCat === '' || ! is_numeric($price) || (float) $price < 0) {
                $invalid++;
                $skipped++;
                $invalidRows[] = [
                    'id_produk' => $row->id_produk,
                    'nama_produk' => $name,
                    'kategori' => $normCat,
                    'harga' => $price,
                ];
                continue;
            }

            $category = $categoryCache[$catKey] ?? null;
            if (! $category) {
                if (! isset($missingCategories[$normCat])) {
                    $missingCategories[$normCat] = 0;
                }
                $missingCategories[$normCat]++;
                if (! in_array($normCat, $categoriesToCreate, true)) {
                    $categoriesToCreate[] = $normCat;
                }
                $plan[] = ['row' => $row, 'category_id' => null, 'category_name' => $normCat, 'status' => 'pending-category'];
                continue;
            }

            $categoriesMatched++;

            $exists = Product::where('product_name', $name)
                ->where('category_id', $category->category_id)
                ->exists();

            if ($exists) {
                $alreadyExisting++;
                $skipped++;
                continue;
            }

            $willImport++;
            $plan[] = ['row' => $row, 'category_id' => $category->category_id, 'category_name' => $category->category_name, 'status' => 'ready'];
        }

        $newCategoriesRequired = count($categoriesToCreate);
        $pendingCount = count(array_filter($plan, fn ($p) => $p['status'] === 'pending-category'));
        $willImport += $pendingCount;

        $this->line("Legacy products found: {$legacyCount}");
        $this->line("Already existing: {$alreadyExisting}");
        $this->line("Will import: {$willImport}");
        $this->line("Skipped: {$skipped}");
        $this->line("Missing categories: " . count($missingCategories));
        $this->line("New categories required: {$newCategoriesRequired}");
        $this->line("Stock fields detected: {$stockDetected}");
        $this->line('Stock imported: 0');

        if (! empty($missingCategories)) {
            $this->line('');
            $this->line('Missing category detail:');
            foreach ($missingCategories as $catName => $count) {
                $this->line("  - '{$catName}': {$count} row(s)");
            }
        }

        if (! empty($invalidRows)) {
            $this->line('');
            $this->line('Invalid rows skipped:');
            foreach ($invalidRows as $inv) {
                $this->line("  - id_produk={$inv['id_produk']} name='{$inv['nama_produk']}' kategori='{$inv['kategori']}' harga='{$inv['harga']}'");
            }
        }

        if (! $isExecute) {
            $this->line('');
            $this->line('Database changes: NONE');
            $this->line('');
            $this->line('Run with --execute to perform the import.');

            return self::SUCCESS;
        }

        $imported = 0;
        $duplicatesPrevented = 0;
        $categoriesCreated = 0;

        DB::transaction(function () use (&$imported, &$duplicatesPrevented, &$categoriesCreated, $plan, $legacyRows, $invalidRows, $loadCategories, &$categoryCache) {
            foreach ($plan as $item) {
                $row = $item['row'];
                $name = trim((string) $row->nama_produk);

                $categoryId = $item['category_id'];
                if ($categoryId === null) {
                    $normCat = $item['category_name'];
                    $catKey = mb_strtolower($normCat);
                    $existing = Category::whereRaw('LOWER(TRIM(category_name)) = ?', [$catKey])->first();
                    if (! $existing) {
                        $existing = Category::create(['category_name' => $normCat]);
                        $categoriesCreated++;
                    }
                    $categoryId = $existing->category_id;
                    $categoryCache[$catKey] = $existing;
                }

                $exists = Product::where('product_name', $name)
                    ->where('category_id', $categoryId)
                    ->exists();

                if ($exists) {
                    $duplicatesPrevented++;
                    continue;
                }

                Product::create([
                    'category_id' => $categoryId,
                    'product_name' => $name,
                    'product_price' => (float) $row->harga,
                    'product_description' => 'Imported from legacy produk table.',
                    'product_image' => '',
                    'product_status' => 1,
                ]);
                $imported++;
            }
        });

        $this->line('');
        $this->line("Imported: {$imported}");
        $this->line("Duplicates prevented: {$duplicatesPrevented}");
        $this->line("Categories matched: existing reused");
        $this->line("Categories created: {$categoriesCreated}");
        $this->line("Invalid rows: " . count($invalidRows));
        $this->line('Database changes: INSERT ONLY into tb_category (if needed) + tb_product');

        return self::SUCCESS;
    }
}
