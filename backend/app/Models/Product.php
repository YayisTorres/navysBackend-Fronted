<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'code',
        'name',
        'images',
        'sizes',
        'size2',
        'type',
        'price',
        'category',
        'quantity',
        'description',
        'supplier',
        'purchasePrice',
        'publicPrice',
    ];

    protected $casts = [
        'images' => 'array',
        'sizes' => 'array',
        'size2' => 'array',
        'price' => 'decimal:2',
        'purchasePrice' => 'decimal:2',
        'publicPrice' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function getImagesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setImagesAttribute($value)
    {
        $this->attributes['images'] = is_array($value) ? json_encode($value) : $value;
    }
}
