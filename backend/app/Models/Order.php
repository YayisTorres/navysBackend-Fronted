<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'tax',
        'shipping',
        'discount',
        'total',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'payment_method',
        'payment_status',
        'payment_reference',
        'payment_date',
        'notes',
        'admin_notes',
        'confirmed_at',
        'shipped_at',
        'delivered_at'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->id)) {
                $order->id = Str::uuid();
            }

            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
        });
        // 🔥 NUEVO: Lógica para eliminar los items de la orden
        static::deleting(function ($order) {
            // Elimina todos los OrderItem asociados a esta orden
            $order->items()->delete();
        });
    }

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Métodos auxiliares
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pendiente',
            'confirmed' => 'Confirmado',
            'processing' => 'En Proceso',
            'shipped' => 'Enviado',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pendiente',
            'paid' => 'Pagado',
            'failed' => 'Fallido',
            'refunded' => 'Reembolsado'
        ];

        return $labels[$this->payment_status] ?? $this->payment_status;
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function canBeShipped()
    {
        return $this->status === 'confirmed' && $this->payment_status === 'paid';
    }
}