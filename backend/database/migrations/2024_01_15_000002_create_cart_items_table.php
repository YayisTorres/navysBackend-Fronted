<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('product_id'); // UUID del producto
            $table->integer('quantity')->default(1);
            $table->string('size')->nullable(); // Talla seleccionada
            $table->string('color')->nullable(); // Color seleccionado
            $table->decimal('price', 10, 2); // Precio al momento de agregar al carrito
            $table->timestamps();

            // Índices y relaciones
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            // Evitar duplicados del mismo producto con misma talla y color
            $table->unique(['user_id', 'product_id', 'size', 'color']);

            // Índices para optimizar consultas
            $table->index(['user_id']);
            $table->index(['product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
};
