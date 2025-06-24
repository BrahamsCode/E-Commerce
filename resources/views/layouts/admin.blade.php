<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') - {{ config('app.name') }}</title>

    <!-- Bootstrap 5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <style>
        :root {
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            width: var(--sidebar-width);
            background-color: #343a40;
        }

        .sidebar-sticky {
            position: sticky;
            top: 48px;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: #c2c7d0;
            padding: .5rem 1rem;
            margin-bottom: 2px;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, .1);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: #007bff;
        }

        .sidebar .nav-link i {
            margin-right: .5rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-heading {
            font-size: .75rem;
            text-transform: uppercase;
            color: #6c757d;
            padding: .5rem 1rem;
            margin-top: 1rem;
        }

        main {
            margin-left: var(--sidebar-width);
            padding-top: 48px;
        }

        .navbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            z-index: 101;
            background-color: #fff !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .1);
        }

        .content {
            padding: 2rem;
        }

        .stat-card {
            border-left: 4px solid;
            transition: transform .2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, .1);
        }

        .table-responsive {
            border-radius: .375rem;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform .3s;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            main {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <button class="navbar-toggler d-lg-none" type="button" onclick="toggleSidebar()">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Panel Admin
            </a>

            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('home') }}" target="_blank">
                            <i class="fas fa-external-link-alt"></i> Ver Tienda
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>

                <h6 class="sidebar-heading">Catálogo</h6>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}"
                       href="{{ route('admin.products.index') }}">
                        <i class="fas fa-box"></i> Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"
                       href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-folder"></i> Categorías
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}"
                       href="{{ route('admin.brands.index') }}">
                        <i class="fas fa-tags"></i> Marcas
                    </a>
                </li>

                <h6 class="sidebar-heading">Ventas</h6>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"
                       href="{{ route('admin.orders.index') }}">
                        <i class="fas fa-shopping-cart"></i> Pedidos
                        @php
                            $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
                        @endphp
                        @if($pendingOrders > 0)
                            <span class="badge bg-danger">{{ $pendingOrders }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"
                       href="{{ route('admin.customers.index') }}">
                        <i class="fas fa-users"></i> Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}"
                       href="{{ route('admin.coupons.index') }}">
                        <i class="fas fa-ticket-alt"></i> Cupones
                    </a>
                </li>

                <h6 class="sidebar-heading">Reportes</h6>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.sales') ? 'active' : '' }}"
                       href="{{ route('admin.reports.sales') }}">
                        <i class="fas fa-chart-line"></i> Reporte de Ventas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.products') ? 'active' : '' }}"
                       href="{{ route('admin.reports.products') }}">
                        <i class="fas fa-chart-bar"></i> Reporte de Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.customers') ? 'active' : '' }}"
                       href="{{ route('admin.reports.customers') }}">
                        <i class="fas fa-chart-pie"></i> Reporte de Clientes
                    </a>
                </li>

                @if(auth()->user()->isAdmin())
                    <h6 class="sidebar-heading">Configuración</h6>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
                           href="{{ route('admin.settings.index') }}">
                            <i class="fas fa-cog"></i> Configuración
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main role="main">
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Toggle sidebar en móvil
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }

        // Inicializar DataTables
        $(document).ready(function() {
            $('.data-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                },
                responsive: true,
                order: [[0, 'desc']]
            });
        });

        // Cerrar sidebar al hacer clic fuera
        $(document).click(function(e) {
            if (!$(e.target).closest('.sidebar, .navbar-toggler').length) {
                $('#sidebar').removeClass('show');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
