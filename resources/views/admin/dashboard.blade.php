@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Dashboard</h1>
    <div>
        <span class="text-muted">{{ now()->format('l, d \d\e F Y') }}</span>
    </div>
</div>

<!-- Estadísticas -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card stat-card border-start-primary">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">Total Pedidos</h6>
                        <h3 class="mb-0">{{ number_format($stats['total_orders']) }}</h3>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card stat-card border-start-warning">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">Pedidos Pendientes</h6>
                        <h3 class="mb-0">{{ number_format($stats['pending_orders']) }}</h3>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card stat-card border-start-success">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">Ingresos Totales</h6>
                        <h3 class="mb-0">S/ {{ number_format($stats['total_revenue'], 2) }}</h3>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-dollar-sign fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card stat-card border-start-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-muted mb-2">Total Clientes</h6>
                        <h3 class="mb-0">{{ number_format($stats['total_customers']) }}</h3>
                    </div>
                    <div class="ms-3">
                        <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Gráfico de Ventas -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Ventas de los Últimos 7 Días</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Productos Más Vendidos -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Top 5 Productos</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($topProducts as $product)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{ Str::limit($product->name, 30) }}</h6>
                                <small class="text-muted">{{ $product->category->name }}</small>
                            </div>
                            <span class="badge bg-primary">{{ $product->order_items_count }} vendidos</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">No hay datos disponibles</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pedidos Recientes -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Pedidos de Hoy</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">Ver Todos</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Hora</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayOrders as $order)
                            <tr>
                                <td>{{ $order->formatted_order_number }}</td>
                                <td>{{ $order->customer->name }}</td>
                                <td>S/ {{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    @switch($order->status)
                                    @case('pending')
                                    <span class="badge bg-warning">Pendiente</span>
                                    @break
                                    @case('confirmed')
                                    <span class="badge bg-info">Confirmado</span>
                                    @break
                                    @case('processing')
                                    <span class="badge bg-primary">Procesando</span>
                                    @break
                                    @case('shipped')
                                    <span class="badge bg-secondary">Enviado</span>
                                    @break
                                    @case('delivered')
                                    <span class="badge bg-success">Entregado</span>
                                    @break
                                    @case('cancelled')
                                    <span class="badge bg-danger">Cancelado</span>
                                    @break
                                    @endswitch
                                </td>
                                <td>{{ $order->created_at->format('H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No hay pedidos hoy
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos con Bajo Stock -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle"></i> Productos con Bajo Stock
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @forelse($lowStockProducts as $product)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{ Str::limit($product->name, 25) }}</h6>
                                <small class="text-muted">SKU: {{ $product->sku }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-danger">{{ $product->stock }} und.</span>
                                <a href="{{ route('admin.products.edit', $product) }}"
                                    class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">Todos los productos tienen stock suficiente</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Accesos Rápidos -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Accesos Rápidos</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus"></i> Nuevo Producto
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.orders.index') }}?status=pending"
                            class="btn btn-outline-warning w-100">
                            <i class="fas fa-clock"></i> Pedidos Pendientes
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-chart-line"></i> Reporte de Ventas
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-users"></i> Ver Clientes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Datos para el gráfico
    const salesData = @json($salesData);

    // Configurar el gráfico
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('es-PE', { weekday: 'short', day: 'numeric' });
            }),
            datasets: [{
                label: 'Ventas (S/)',
                data: salesData.map(item => item.revenue),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4
            }, {
                label: 'Pedidos',
                data: salesData.map(item => item.orders),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    ticks: {
                        callback: function(value) {
                            return 'S/ ' + value.toFixed(2);
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });
</script>
@endpush
