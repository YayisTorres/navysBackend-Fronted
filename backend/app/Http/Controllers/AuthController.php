<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $imageUploadService;

    public function __construct(ImageUploadService $imageUploadService)
    {
        $this->imageUploadService = $imageUploadService;
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'name' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'middleName' => 'nullable|string|max:255',
                'numberPhone' => 'nullable|string|max:20',
                'photoFile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'role' => 'required|in:admin,empleado,cliente'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userData = $request->only(['email', 'name', 'lastName', 'middleName', 'numberPhone', 'role']);
            $userData['password'] = Hash::make($request->password);

            // Manejar la foto de perfil con Firebase fallback
            if ($request->hasFile('photoFile')) {
                try {
                    $photoName = time() . '_' . $request->file('photoFile')->getClientOriginalName();
                    $photoPath = $this->imageUploadService->upload($request->file('photoFile'), 'img/imgPerfil', $photoName);
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
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas'
                ], 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            // 🔥 INICIO DEL CÓDIGO DE DIAGNÓSTICO 🔥
            $firebaseCreds = env('FIREBASE_CREDENTIALS');
            $debugMessage = '';

            if (!empty($firebaseCreds)) {
                $debugMessage = '✅ ¡ÉXITO! La variable de entorno FIREBASE_CREDENTIALS fue encontrada.';
            } else {
                $debugMessage = '❌ ¡ERROR! La variable de entorno FIREBASE_CREDENTIALS está VACÍA o no existe.';
            }
            // 🔥 FIN DEL CÓDIGO DE DIAGNÓSTICO 🔥

            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    // Añadimos el mensaje de debug a la respuesta
                    '_debug_firebase_check' => $debugMessage
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout exitoso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function me(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $request->user()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
