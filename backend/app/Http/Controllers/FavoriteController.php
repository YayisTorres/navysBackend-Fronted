<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    /**
     * Obtener todos los favoritos del usuario autenticado
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

            $favorites = Favorite::with(['product'])
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Procesar las imágenes de los productos
            $favorites->each(function ($favorite) {
                if ($favorite->product && $favorite->product->images) {
                    if (is_string($favorite->product->images)) {
                        $favorite->product->images = json_decode($favorite->product->images, true);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Favoritos obtenidos exitosamente',
                'data' => $favorites,
                'count' => $favorites->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener favoritos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agregar un producto a favoritos
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
                'product_id' => 'required|string|exists:products,id'
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

            // Verificar si ya existe en favoritos
            $existingFavorite = Favorite::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($existingFavorite) {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto ya está en tus favoritos'
                ], 409);
            }

            // Crear el favorito
            $favorite = Favorite::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);

            // Cargar la relación del producto
            $favorite->load('product');

            // Procesar imágenes del producto
            if ($favorite->product && $favorite->product->images) {
                if (is_string($favorite->product->images)) {
                    $favorite->product->images = json_decode($favorite->product->images, true);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Producto agregado a favoritos exitosamente',
                'data' => $favorite
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar a favoritos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un producto de favoritos
     */
    public function destroy($productId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $userId = $user->id;

            $favorite = Favorite::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if (!$favorite) {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto no está en tus favoritos'
                ], 404);
            }

            $favorite->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado de favoritos exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar de favoritos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar si un producto está en favoritos
     */
    public function check($productId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $userId = $user->id;

            $isFavorite = Favorite::where('user_id', $userId)
                ->where('product_id', $productId)
                ->exists();

            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar favorito',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el conteo de favoritos del usuario
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

            $count = Favorite::where('user_id', $user->id)->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener conteo de favoritos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
