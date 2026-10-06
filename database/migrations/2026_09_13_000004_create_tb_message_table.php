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
        Schema::create('tb_message', function (Blueprint $table) {
            $table->id('message_id');
            $table->string('name', 100);
            $table->string('email', 100);
            $table->text('message');
            $table->timestampTz('date_sent')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_message');
    }
};
