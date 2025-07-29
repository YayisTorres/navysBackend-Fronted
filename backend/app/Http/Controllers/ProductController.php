<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function index(Request $request)
    {
        $query = Product::query();

        // Filtros opcionales
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // 🔥 Decodificar imágenes si están en JSON
        if ($product->images && is_string($product->images)) {
            $product->images = json_decode($product->images, true);
        }

        // 🔥 Decodificar sizes si están en JSON
        if ($product->sizes && is_string($product->sizes)) {
            $product->sizes = json_decode($product->sizes, true);
        }

        // 🔥 Decodificar size2 si están en JSON
        if ($product->size2 && is_string($product->size2)) {
            $product->size2 = json_decode($product->size2, true);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:products',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'supplier' => 'nullable|string|max:255',
            'purchasePrice' => 'nullable|numeric|min:0',
            'publicPrice' => 'nullable|numeric|min:0',
            'sizes' => 'nullable|array',
            'size2' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $productData = $request->only([
            'code',
            'name',
            'price',
            'category',
            'type',
            'quantity',
            'description',
            'supplier',
            'purchasePrice',
            'publicPrice'
        ]);

        $productData['id'] = Str::uuid();

        // 🔥 Procesar sizes - convertir array a JSON string
        if ($request->has('sizes') && is_array($request->sizes)) {
            $productData['sizes'] = json_encode($request->sizes);
        }

        // 🔥 Procesar size2 - convertir array a JSON string
        if ($request->has('size2') && is_array($request->size2)) {
            $productData['size2'] = json_encode($request->size2);
        }

        // 🔥 Manejar imágenes - Intentar guardar localmente, fallback a Firebase
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $color => $image) {
                try {
                    $imageName = time() . '_' . $color . '_' . $image->getClientOriginalName();
                    $imagePath = $this->imageUploadService->upload($image, 'img/imgProducts', $imageName);
                    $images[$color] = $imagePath;
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error al subir imagen para color ' . $color,
                        'error' => $e->getMessage()
                    ], 500);
                }
            }
        }
        $productData['images'] = json_encode($images);

        $product = Product::create($productData);

        return response()->json([
            'success' => true,
            'message' => 'Producto creado exitosamente',
            'data' => $product
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|string|unique:products,code,' . $id,
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'category' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'supplier' => 'nullable|string|max:255',
            'purchasePrice' => 'nullable|numeric|min:0',
            'publicPrice' => 'nullable|numeric|min:0',
            'sizes' => 'nullable|array',
            'size2' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'keep_images' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only([
            'code',
            'name',
            'price',
            'category',
            'type',
            'quantity',
            'description',
            'supplier',
            'purchasePrice',
            'publicPrice'
        ]);

        // 🔥 Procesar sizes - convertir array a JSON string
        if ($request->has('sizes') && is_array($request->sizes)) {
            $updateData['sizes'] = json_encode($request->sizes);
        }

        // 🔥 Procesar size2 - convertir array a JSON string
        if ($request->has('size2') && is_array($request->size2)) {
            $updateData['size2'] = json_encode($request->size2);
        }

        // 🔥 NUEVA LÓGICA: Manejo inteligente de imágenes con Firebase fallback
        $currentImages = $product->images;
        if (is_string($currentImages)) {
            $currentImages = json_decode($currentImages, true) ?? [];
        }
        if (!is_array($currentImages)) {
            $currentImages = [];
        }

        $finalImages = [];

        // 1️⃣ Conservar imágenes existentes que se quieren mantener
        if ($request->has('keep_images') && is_array($request->input('keep_images'))) {
            foreach ($request->input('keep_images') as $newKey => $originalKey) {
                if (isset($currentImages[$originalKey])) {
                    $finalImages[$newKey] = $currentImages[$originalKey];
                    // Remover de currentImages para no eliminarla después
                    unset($currentImages[$originalKey]);
                }
            }
        }

        // 2️⃣ Agregar nuevas imágenes usando el servicio (local + Firebase fallback)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $color => $image) {
                try {
                    $imageName = time() . '_' . $color . '_' . $image->getClientOriginalName();
                    $imagePath = $this->imageUploadService->upload($image, 'img/imgProducts', $imageName);
                    $finalImages[$color] = $imagePath;
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error al subir imagen para color ' . $color,
                        'error' => $e->getMessage()
                    ], 500);
                }
            }
        }

        // 3️⃣ Eliminar solo las imágenes que ya no se usan (usando el servicio)
        foreach ($currentImages as $unusedImage) {
            $this->imageUploadService->delete($unusedImage);
        }

        // 4️⃣ Solo actualizar imágenes si hay cambios
        if ($request->hasFile('images') || $request->has('keep_images')) {
            $updateData['images'] = json_encode($finalImages);
        }

        $product->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado exitosamente',
            'data' => $product->fresh()
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // 🔥 Eliminar imágenes del servidor usando el servicio (local + Firebase)
        $images = $product->images;
        if (is_string($images)) {
            $images = json_decode($images, true);
        }

        if (is_array($images)) {
            foreach ($images as $image) {
                $this->imageUploadService->delete($image);
            }
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado exitosamente'
        ]);
    }
}
