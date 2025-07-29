<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Solo los administradores pueden acceder.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $totalProducts = Product::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalEmpleados = User::where('role', 'empleado')->count();
        $totalClientes = User::where('role', 'cliente')->count();

        $recentUsers = User::latest()->take(5)->get();
        $recentProducts = Product::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalProducts',
            'totalAdmins',
            'totalEmpleados',
            'totalClientes',
            'recentUsers',
            'recentProducts'
        ));
    }

    public function users()
    {
        $users = User::paginate(15);
        return view('admin.users', compact('users'));
    }

    public function products()
    {
        $products = Product::paginate(15);
        return view('admin.products', compact('products'));
    }
}
