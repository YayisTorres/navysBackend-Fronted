<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    protected $casts = [
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

    // Scope para obtener favoritos de un usuario específico
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope para verificar si un producto está en favoritos
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
