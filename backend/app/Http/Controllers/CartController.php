<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Obtener todos los items del carrito del usuario autenticado
     */
    public function index()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $cartItems = CartItem::with(['product'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Procesar las imágenes de los productos y calcular totales
            $total = 0;
            $cartItems->each(function ($item) use (&$total) {
                if ($item->product && $item->product->images) {
                    if (is_string($item->product->images)) {
                        $item->product->images = json_decode($item->product->images, true);
                    }
                }
                $total += $item->subtotal;
            });

            return response()->json([
                'success' => true,
                'message' => 'Carrito obtenido exitosamente',
                'data' => [
                    'items' => $cartItems,
                    'total' => number_format($total, 2),
                    'count' => $cartItems->sum('quantity')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener carrito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agregar un producto al carrito
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'product_id' => 'required|string|exists:products,id',
                'quantity' => 'required|integer|min:1|max:10',
                'size' => 'nullable|string|max:50',
                'color' => 'nullable|string|max:50'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = $user->id;
            $productId = $request->product_id;
            $quantity = $request->quantity;
            $size = $request->size;
            $color = $request->color;

            // Obtener el producto para verificar stock y precio
            $product = Product::find($productId);
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Producto no encontrado'
                ], 404);
            }

            // Verificar stock disponible
            if ($product->quantity < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente. Disponible: ' . $product->quantity
                ], 400);
            }

            // Verificar si ya existe el mismo item en el carrito
            $existingItem = CartItem::where('user_id', $userId)
                ->where('product_id', $productId)
                ->where('size', $size)
                ->where('color', $color)
                ->first();

            if ($existingItem) {
                // Actualizar cantidad
                $newQuantity = $existingItem->quantity + $quantity;

                if ($product->quantity < $newQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock insuficiente. Disponible: ' . $product->quantity . ', en carrito: ' . $existingItem->quantity
                    ], 400);
                }

                $existingItem->update(['quantity' => $newQuantity]);
                $cartItem = $existingItem;
            } else {
                // Crear nuevo item
                $cartItem = CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'size' => $size,
                    'color' => $color,
                    'price' => $product->price
                ]);
            }

            // Cargar la relación del producto
            $cartItem->load('product');

            // Procesar imágenes del producto
            if ($cartItem->product && $cartItem->product->images) {
                if (is_string($cartItem->product->images)) {
                    $cartItem->product->images = json_decode($cartItem->product->images, true);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado al carrito exitosamente',
                'data' => $cartItem
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar al carrito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar cantidad de un item del carrito
     */
    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1|max:10'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $cartItem = CartItem::where('user_id', $user->id)->find($id);

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item del carrito no encontrado'
                ], 404);
            }

            // Verificar stock disponible
            $product = $cartItem->product;
            if ($product->quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente. Disponible: ' . $product->quantity
                ], 400);
            }

            $cartItem->update(['quantity' => $request->quantity]);

            return response()->json([
                'success' => true,
                'message' => 'Cantidad actualizada exitosamente',
                'data' => $cartItem->fresh(['product'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar item del carrito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un item del carrito
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $cartItem = CartItem::where('user_id', $user->id)->find($id);

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item del carrito no encontrado'
                ], 404);
            }

            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item eliminado del carrito exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar item del carrito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Limpiar todo el carrito
     */
    public function clear()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            CartItem::where('user_id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Carrito limpiado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al limpiar carrito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el conteo de items en el carrito
     */
    public function count()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $count = CartItem::where('user_id', $user->id)->sum('quantity');

            return response()->json([
                'success' => true,
                'count' => $count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener conteo del carrito',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
