<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // 🔥 CORRECTO: order_id como uuid (para referenciar orders.id)
            $table->uuid('order_id');
            
            // 🔥 CORRECTO: product_id como string (igual que en cart_items y favorites)
            $table->string('product_id');
            
            // Información del producto al momento de la compra
            $table->string('product_name');
            $table->string('product_code');
            $table->text('product_description')->nullable();
            $table->json('product_image')->nullable(); // Imagen principal del producto
            
            // Variaciones del producto (igual que cart_items)
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            
            // Precios y cantidades
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            
            $table->timestamps();
            
            // 🔥 CORRECTO: Foreign keys con los tipos correctos
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            // Índices para optimizar consultas
            $table->index(['order_id']);
            $table->index(['product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};