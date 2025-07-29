<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('product_id'); // UUID del producto
            $table->timestamps();

            // Índices y relaciones
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Evitar duplicados - un usuario solo puede tener un producto en favoritos una vez
            $table->unique(['user_id', 'product_id']);

            // Índices para optimizar consultas
            $table->index(['user_id']);
            $table->index(['product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('favorites');
    }
};
