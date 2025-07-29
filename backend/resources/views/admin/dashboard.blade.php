@extends('layouts.admin')

@section('title', 'Dashboard - Admin Panel')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-tachometer-alt mr-3"></i>Dashboard
            </h1>
            <div class="text-sm text-gray-600">
                <i class="fas fa-calendar mr-1"></i>
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Usuarios</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-box text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Productos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalProducts }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 rounded-full p-3">
                        <i class="fas fa-user-shield text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Admins</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalAdmins }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-user-tie text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Empleados</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalEmpleados }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-user text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Clientes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalClientes }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Data -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Users -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-users mr-2"></i>Usuarios Recientes
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($recentUsers as $user)
                            <div class="flex items-center space-x-4">
                                <div class="bg-gray-200 rounded-full w-10 h-10 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $user->name }} {{ $user->lastName }}</p>
                                    <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($user->role === 'admin') bg-red-100 text-red-800
                                    @elseif($user->role === 'empleado') bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Recent Products -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-box mr-2"></i>Productos Recientes
                    </h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($recentProducts as $product)
                            <div class="flex items-center space-x-4">
                                <div class="bg-green-100 rounded-full w-10 h-10 flex items-center justify-center">
                                    <i class="fas fa-box text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $product->code }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600">${{ $product->price }}</p>
                                    <p class="text-xs text-gray-500">Stock: {{ $product->quantity ?? 0 }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection