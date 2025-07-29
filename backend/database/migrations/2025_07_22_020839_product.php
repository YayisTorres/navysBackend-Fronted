<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->json('images')->nullable();
            $table->json('sizes')->nullable();
            $table->json('size2')->nullable();
            $table->string('type')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('category')->nullable();
            $table->integer('quantity')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->string('supplier')->nullable();
            $table->decimal('purchasePrice', 10, 2)->nullable();
            $table->decimal('publicPrice', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};
