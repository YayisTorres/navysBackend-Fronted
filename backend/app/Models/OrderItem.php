<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderItem extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_code',
        'product_description',
        'product_image',
        'size',
        'color',
        'quantity',
        'unit_price',
        'total_price'
    ];

    protected $casts = [
        'product_image' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderItem) {
            if (empty($orderItem->id)) {
                $orderItem->id = Str::uuid();
            }
        });
    }

    // Relaciones
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Métodos auxiliares
    public function getFormattedVariationsAttribute()
    {
        $variations = [];
        
        if ($this->size) {
            $variations[] = "Talla: {$this->size}";
        }
        
        if ($this->color) {
            $variations[] = "Color: {$this->color}";
        }
        
        return implode(' | ', $variations);
    }
}