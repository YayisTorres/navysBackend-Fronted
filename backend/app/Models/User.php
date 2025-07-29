<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB; // 🔥 Importante agregar DB

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'name',
        'lastName',
        'middleName',
        'numberPhone',
        'photo',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- RELACIONES ---

    /**
     * Un usuario tiene muchas órdenes.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Un usuario tiene muchos favoritos.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Un usuario tiene muchos items en el carrito.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    // --- LÓGICA DE ELIMINACIÓN EN CASCADA ---

    protected static function booted()
    {
        static::deleting(function ($user) {
            // Usamos una transacción para asegurar que todo se elimine correctamente
            DB::transaction(function () use ($user) {
                // 1. Eliminar los favoritos del usuario
                $user->favorites()->delete();

                // 2. Eliminar los items del carrito del usuario
                $user->cartItems()->delete();

                // 3. Eliminar las órdenes del usuario (y sus items)
                // Hacemos un loop para que se dispare el evento 'deleting' de cada orden
                $user->orders()->each(function ($order) {
                    $order->delete();
                });
            });
        });
    }

    // --- MÉTODOS AUXILIARES ---

    public function getFullNameAttribute()
    {
        return trim($this->name . ' ' . $this->middleName . ' ' . $this->lastName);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEmpleado()
    {
        return $this->role === 'empleado';
    }

    public function isCliente()
    {
        return $this->role === 'cliente';
    }
}