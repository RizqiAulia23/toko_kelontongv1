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
        Schema::create('tb_admin', function (Blueprint $table) {
            $table->id('admin_id');
            $table->string('admin_name', 100);
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->string('admin_telp', 25);
            $table->string('admin_email', 100);
            $table->text('admin_address');
            $table->string('level', 20)->default('admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_admin');
    }
};
