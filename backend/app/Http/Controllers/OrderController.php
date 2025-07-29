<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Obtener todos los pedidos del usuario autenticado
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $query = Order::with(['items.product'])
                ->where('user_id', $user->id)
                ->recent();

            // Filtro por estado
            if ($request->has('status')) {
                $query->byStatus($request->status);
            }

            $orders = $query->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Pedidos obtenidos exitosamente',
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pedidos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un nuevo pedido desde el carrito
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
                'shipping_name' => 'required|string|max:255',
                'shipping_email' => 'required|email|max:255',
                'shipping_phone' => 'required|string|max:20',
                'shipping_address' => 'required|string',
                'shipping_city' => 'required|string|max:100',
                'shipping_state' => 'required|string|max:100',
                'shipping_postal_code' => 'required|string|max:10',
                'shipping_country' => 'nullable|string|max:100',
                'payment_method' => 'required|in:cash,card,transfer,paypal',
                'notes' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Obtener items del carrito
            $cartItems = CartItem::with('product')
                ->where('user_id', $user->id)
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El carrito está vacío'
                ], 400);
            }

            // Verificar stock disponible
            foreach ($cartItems as $cartItem) {
                if ($cartItem->product->quantity < $cartItem->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente para {$cartItem->product->name}. Disponible: {$cartItem->product->quantity}"
                    ], 400);
                }
            }

            DB::beginTransaction();

            // Calcular totales
            $subtotal = $cartItems->sum('subtotal');
            $tax = 0; // Puedes agregar lógica de impuestos aquí
            $shipping = 0; // Puedes agregar lógica de envío aquí
            $discount = 0; // Puedes agregar lógica de descuentos aquí
            $total = $subtotal + $tax + $shipping - $discount;

            // Crear el pedido
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'discount' => $discount,
                'total' => $total,
                'shipping_name' => $request->shipping_name,
                'shipping_email' => $request->shipping_email,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_country' => $request->shipping_country ?? 'México',
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'notes' => $request->notes
            ]);

            // Crear los items del pedido
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;

                // Obtener imagen principal del producto
                $productImage = null;
                if ($product->images) {
                    $images = is_string($product->images) ? json_decode($product->images, true) : $product->images;
                    if (is_array($images) && !empty($images)) {
                        $productImage = array_values($images)[0]; // Primera imagen
                    }
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_code' => $product->code,
                    'product_description' => $product->description,
                    'product_image' => $productImage,
                    'size' => $cartItem->size,
                    'color' => $cartItem->color,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $cartItem->price,
                    'total_price' => $cartItem->subtotal
                ]);

                // Reducir stock del producto
                $product->decrement('quantity', $cartItem->quantity);
            }

            // Limpiar el carrito
            CartItem::where('user_id', $user->id)->delete();

            DB::commit();

            // Cargar relaciones para la respuesta
            $order->load(['items.product', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Pedido creado exitosamente',
                'data' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear pedido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un pedido específico
     */
    public function show($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $order = Order::with(['items.product', 'user'])
                ->where('user_id', $user->id)
                ->find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pedido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancelar un pedido (solo si está pendiente o confirmado)
     */
    public function cancel($id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $order = Order::with('items.product')
                ->where('user_id', $user->id)
                ->find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido no encontrado'
                ], 404);
            }

            if (!$order->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pedido no puede ser cancelado'
                ], 400);
            }

            DB::beginTransaction();

            // Restaurar stock de los productos
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('quantity', $item->quantity);
                }
            }

            // Actualizar estado del pedido
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'refunded'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pedido cancelado exitosamente',
                'data' => $order->fresh(['items.product'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cancelar pedido',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Métodos para administradores
     */

    /**
     * Obtener todos los pedidos (solo admin/empleado)
     */
    public function adminIndex(Request $request)
    {
        try {
            $query = Order::with(['items.product', 'user'])
                ->recent();

            // Filtros
            if ($request->has('status')) {
                $query->byStatus($request->status);
            }

            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('shipping_name', 'like', "%{$search}%")
                        ->orWhere('shipping_email', 'like', "%{$search}%");
                });
            }

            $orders = $query->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Pedidos obtenidos exitosamente',
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pedidos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar estado de un pedido (solo admin/empleado)
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
                'payment_status' => 'nullable|in:pending,paid,failed,refunded',
                'admin_notes' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $order = Order::with(['items.product'])->find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pedido no encontrado'
                ], 404);
            }

            $updateData = ['status' => $request->status];

            // Actualizar fechas según el estado
            switch ($request->status) {
                case 'confirmed':
                    $updateData['confirmed_at'] = now();
                    break;
                case 'shipped':
                    $updateData['shipped_at'] = now();
                    break;
                case 'delivered':
                    $updateData['delivered_at'] = now();
                    break;
            }

            if ($request->has('payment_status')) {
                $updateData['payment_status'] = $request->payment_status;
                if ($request->payment_status === 'paid') {
                    $updateData['payment_date'] = now();
                }
            }

            if ($request->has('admin_notes')) {
                $updateData['admin_notes'] = $request->admin_notes;
            }

            $order->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Estado del pedido actualizado exitosamente',
                'data' => $order->fresh(['items.product', 'user'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado del pedido',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
