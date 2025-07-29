<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    @auth
        <nav class="bg-blue-600 text-white p-4">
            <div class="container mx-auto flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-bold">🚀 Admin Panel</h1>
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('admin.dashboard') }}" class="hover:bg-blue-700 px-3 py-2 rounded">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('admin.users') }}" class="hover:bg-blue-700 px-3 py-2 rounded">
                            <i class="fas fa-users mr-2"></i>Usuarios
                        </a>
                        <a href="{{ route('admin.products') }}" class="hover:bg-blue-700 px-3 py-2 rounded">
                            <i class="fas fa-box mr-2"></i>Productos
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm">
                        <i class="fas fa-user-shield mr-1"></i>
                        {{ Auth::user()->name }} {{ Auth::user()->lastName }}
                    </span>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-3 py-2 rounded text-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i>Salir
                        </button>
                    </form>
                </div>
            </div>
        </nav>
    @endauth

    <main class="container mx-auto mt-8 px-4">
        @yield('content')
    </main>

    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function () {
            const alerts = document.querySelectorAll('.alert-auto-hide');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>

</html>