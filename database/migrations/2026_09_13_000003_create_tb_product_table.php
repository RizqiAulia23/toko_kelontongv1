<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_product', function (Blueprint $table) {
            $table->id('product_id');
            $table->foreignId('category_id')
                  ->constrained('tb_category', 'category_id')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->string('product_name', 150);
            $table->decimal('product_price', 12, 2)->default(0.00);
            $table->text('product_description');
            $table->string('product_image', 255)->default('');
            $table->smallInteger('product_status')->default(1);
            $table->timestampTz('date_created')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_product');
    }
};
