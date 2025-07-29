<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // 🔥 CORRECTO: user_id como unsignedBigInteger (igual que cart_items y favorites)
            $table->unsignedBigInteger('user_id');
            
            $table->string('order_number')->unique(); // Número de pedido único
            $table->enum('status', [
                'pending',      // Pendiente
                'confirmed',    // Confirmado
                'processing',   // En proceso
                'shipped',      // Enviado
                'delivered',    // Entregado
                'cancelled'     // Cancelado
            ])->default('pending');
            
            // Información del pedido
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('shipping', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            // Información de envío
            $table->string('shipping_name');
            $table->string('shipping_email');
            $table->string('shipping_phone');
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_postal_code');
            $table->string('shipping_country')->default('México');
            
            // Información de pago
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'paypal'])->default('cash');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->timestamp('payment_date')->nullable();
            
            // Notas y observaciones
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Fechas importantes
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            
            $table->timestamps();
            
            // 🔥 CORRECTO: Foreign key igual que en cart_items y favorites
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['status', 'created_at']);
            $table->index('order_number');
            $table->index(['user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};