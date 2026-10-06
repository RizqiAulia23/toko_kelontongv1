<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Sembako & Beras',
            'Minyak & Bumbu Dapur',
            'Minuman & Kopi',
            'Makanan Ringan & Biskuit',
            'Kebutuhan Rumah Tangga',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['category_name' => $name],
                ['category_name' => $name]
            );
        }
    }
}
