<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $users = User::orderBy('created_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Usuarios obtenidos exitosamente',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener usuarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Usuario obtenido exitosamente',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'middleName' => 'nullable|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'numberPhone' => 'nullable|string|max:20',
                'role' => 'required|in:admin,empleado,cliente',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = $request->only([
                'name',
                'lastName',
                'middleName',
                'email',
                'numberPhone',
                'role'
            ]);

            $userData['password'] = Hash::make($request->password);

            // Handle photo upload with Firebase fallback
            if ($request->hasFile('photo')) {
                try {
                    $filename = time() . '_' . uniqid() . '.' . $request->file('photo')->getClientOriginalExtension();
                    $photoPath = $this->imageUploadService->upload($request->file('photo'), 'img/imgPerfil', $filename);
                    $userData['photo'] = $photoPath;
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error al subir la foto de perfil',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            $user = User::create($userData);

            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente',
                'data' => $user
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'lastName' => 'sometimes|required|string|max:255',
                'middleName' => 'nullable|string|max:255',
                'email' => [
                    'sometimes',
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id)
                ],
                'password' => 'nullable|string|min:6',
                'numberPhone' => 'nullable|string|max:20',
                'role' => 'sometimes|required|in:admin,empleado,cliente',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = $request->only([
                'name',
                'lastName',
                'middleName',
                'email',
                'numberPhone',
                'role'
            ]);

            // Update password only if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            // Handle photo upload with Firebase fallback
            if ($request->hasFile('photo')) {
                // Delete old photo using the service (handles both local and Firebase)
                $this->imageUploadService->delete($user->photo);

                try {
                    $filename = time() . '_' . uniqid() . '.' . $request->file('photo')->getClientOriginalExtension();
                    $photoPath = $this->imageUploadService->upload($request->file('photo'), 'img/imgPerfil', $filename);
                    $userData['photo'] = $photoPath;
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error al subir la foto de perfil',
                        'error' => $e->getMessage()
                    ], 500);
                }
            }

            $user->update($userData);

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'data' => $user->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent self-deletion
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propia cuenta'
                ], 403);
            }

            // Delete photo using the service (handles both local and Firebase)
            $this->imageUploadService->delete($user->photo);

            // Al llamar a delete(), se disparará el evento 'deleting' en el modelo User
            // y se borrarán automáticamente favoritos, carrito y órdenes.
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuario y todos sus datos asociados han sido eliminados exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
