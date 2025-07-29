<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'size',
        'color',
        'price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el producto
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    // Scope para obtener items del carrito de un usuario específico
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Calcular el subtotal del item
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->price;
    }

    // Accessor para obtener el subtotal formateado
    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 2);
    }
}
