<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Mi Tienda'))</title>

    <!-- Bootstrap 5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #e74c3c;
            --success-color: #27ae60;
            --info-color: #3498db;
            --warning-color: #f39c12;
            --danger-color: #c0392b;
            --light-color: #ecf0f1;
            --dark-color: #34495e;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        .btn-whatsapp {
            background-color: #25d366;
            color: white;
            border: none;
        }

        .btn-whatsapp:hover {
            background-color: #128c7e;
            color: white;
        }

        .product-card {
            transition: transform 0.3s;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .badge-discount {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--danger-color);
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
        }

        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 40px 0;
            margin-top: 50px;
        }

        .whatsapp-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 40px;
            right: 40px;
            background-color: #25d366;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 100;
        }

        .whatsapp-float i {
            margin-top: 16px;
        }

        @media (max-width: 768px) {
            .whatsapp-float {
                bottom: 20px;
                right: 20px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-store"></i> {{ config('app.name', 'Mi Tienda') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link @if(request()->routeIs('home')) active @endif" href="{{ route('home') }}">Inicio</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            Categorías
                        </a>
                        <ul class="dropdown-menu">
                            @foreach(\App\Models\Category::active()->roots()->get() as $category)
                                <li><a class="dropdown-item" href="{{ route('category.products', $category) }}">{{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact') }}">Contacto</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <form class="d-flex me-3" action="{{ route('products.search') }}" method="GET">
                            <input class="form-control me-2" type="search" name="q" placeholder="Buscar productos..." value="{{ request('q') }}">
                            <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                            <i class="fas fa-shopping-cart"></i> Carrito
                            <span class="cart-badge" id="cart-count">0</span>
                        </a>
                    </li>
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(auth()->user()->isEmployee())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Panel Admin</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="{{ route('orders.index') }}">Mis Pedidos</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="min-vh-100">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>{{ config('app.name') }}</h5>
                    <p>Tu tienda de confianza en Perú. Realizamos envíos a todo el país.</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5>Enlaces Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('about') }}" class="text-white-50">Nosotros</a></li>
                        <li><a href="{{ route('terms') }}" class="text-white-50">Términos y Condiciones</a></li>
                        <li><a href="{{ route('privacy') }}" class="text-white-50">Política de Privacidad</a></li>
                        <li><a href="{{ route('contact') }}" class="text-white-50">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contacto</h5>
                    <p class="text-white-50">
                        <i class="fas fa-phone"></i> +51 999 999 999<br>
                        <i class="fas fa-envelope"></i> info@mitienda.pe<br>
                        <i class="fas fa-map-marker-alt"></i> Lima, Perú
                    </p>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/51999999999?text=Hola,%20tengo%20una%20consulta" class="whatsapp-float" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Actualizar contador del carrito
        function updateCartCount() {
            $.get('{{ route("cart.count") }}', function(data) {
                $('#cart-count').text(data.count);
            });
        }

        // Agregar al carrito
        function addToCart(productId, quantity = 1) {
            $.post('{{ route("cart.add") }}', {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                quantity: quantity
            }, function(data) {
                if (data.success) {
                    updateCartCount();
                    // Mostrar toast de confirmación
                    showToast('Producto agregado al carrito');
                }
            });
        }

        // Mostrar toast
        function showToast(message) {
            const toastHtml = `
                <div class="toast position-fixed bottom-0 end-0 m-3" role="alert">
                    <div class="toast-header">
                        <strong class="me-auto">Notificación</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">${message}</div>
                </div>
            `;
            $('body').append(toastHtml);
            const toast = new bootstrap.Toast($('.toast').last()[0]);
            toast.show();
            setTimeout(() => $('.toast').last().remove(), 5000);
        }

        // Formatear precio en soles
        function formatPrice(price) {
            return 'S/ ' + parseFloat(price).toFixed(2);
        }

        $(document).ready(function() {
            updateCartCount();
        });
    </script>

    @stack('scripts')
</body>
</html>
