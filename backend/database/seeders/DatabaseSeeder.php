<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Crear usuario admin por defecto
        User::create([
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'name' => 'Super',
            'lastName' => 'Admin',
            'middleName' => 'Pro',
            'numberPhone' => '+1234567890',
            'role' => 'admin',
        ]);

        // Crear usuario empleado de prueba
        User::create([
            'email' => 'empleado@test.com',
            'password' => Hash::make('empleado123'),
            'name' => 'Juan',
            'lastName' => 'Pérez',
            'middleName' => 'Carlos',
            'numberPhone' => '+0987654321',
            'role' => 'empleado',
        ]);

        // Crear usuario cliente de prueba
        User::create([
            'email' => 'cliente@test.com',
            'password' => Hash::make('cliente123'),
            'name' => 'María',
            'lastName' => 'González',
            'middleName' => 'Elena',
            'numberPhone' => '+1122334455',
            'role' => 'cliente',
        ]);

        // Crear algunos productos de prueba
        Product::create([
            'id' => Str::uuid(),
            'code' => 'PROD001',
            'name' => 'Camiseta Básica',
            'images' => json_encode([
                'rojo' => 'img/imgProducts/camiseta_roja.jpg',
                'azul' => 'img/imgProducts/camiseta_azul.jpg'
            ]),
            'sizes' => ['S', 'M', 'L', 'XL'],
            'size2' => [10, 12, 14, 16],
            'type' => 'Ropa',
            'price' => 25.99,
            'category' => 'Camisetas',
            'quantity' => 100,
            'description' => 'Camiseta básica de algodón 100%',
            'supplier' => 'Textiles SA',
            'purchasePrice' => 15.00,
            'publicPrice' => 25.99,
        ]);

        Product::create([
            'id' => Str::uuid(),
            'code' => 'PROD002',
            'name' => 'Pantalón Jeans',
            'images' => json_encode([
                'azul' => 'img/imgProducts/jeans_azul.jpg',
                'negro' => 'img/imgProducts/jeans_negro.jpg'
            ]),
            'sizes' => ['28', '30', '32', '34', '36'],
            'size2' => [28, 30, 32, 34, 36],
            'type' => 'Ropa',
            'price' => 45.99,
            'category' => 'Pantalones',
            'quantity' => 50,
            'description' => 'Pantalón jeans clásico',
            'supplier' => 'Denim Co',
            'purchasePrice' => 30.00,
            'publicPrice' => 45.99,
        ]);
    }
}
