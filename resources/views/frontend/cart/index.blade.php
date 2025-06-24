@extends('layouts.app')

@section('title', 'Carrito de Compras')

@section('content')
<div class="container py-5">
    <h1 class="mb-4"><i class="fas fa-shopping-cart"></i> Carrito de Compras</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(count($items) > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr data-id="{{ $item['id'] }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item['product']->primaryImage)
                                                        <img src="{{ $item['product']->primaryImage->url }}"
                                                             alt="{{ $item['name'] }}"
                                                             class="img-thumbnail me-3"
                                                             style="width: 80px; height: 80px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center me-3"
                                                             style="width: 80px; height: 80px;">
                                                            <i class="fas fa-image"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $item['name'] }}</h6>
                                                        <small class="text-muted">SKU: {{ $item['variant']?->sku ?? $item['product']->sku }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                S/ {{ number_format($item['price'], 2) }}
                                            </td>
                                            <td class="align-middle">
                                                <div class="input-group" style="width: 120px;">
                                                    <button class="btn btn-sm btn-outline-secondary btn-decrease" type="button">-</button>
                                                    <input type="number"
                                                           class="form-control text-center quantity-input"
                                                           value="{{ $item['quantity'] }}"
                                                           min="1"
                                                           max="{{ $item['variant']?->stock ?? $item['product']->stock }}">
                                                    <button class="btn btn-sm btn-outline-secondary btn-increase" type="button">+</button>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <strong class="item-subtotal">S/ {{ number_format($item['subtotal'], 2) }}</strong>
                                            </td>
                                            <td class="align-middle">
                                                <button class="btn btn-sm btn-danger btn-remove" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Seguir Comprando
                    </a>
                    <a href="{{ route('cart.clear') }}" class="btn btn-outline-danger"
                       onclick="return confirm('¿Estás seguro de vaciar el carrito?')">
                        <i class="fas fa-trash"></i> Vaciar Carrito
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Resumen del Pedido</h5>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="cart-subtotal">S/ {{ number_format($total, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>IGV (18%):</span>
                            <span id="cart-tax">S/ {{ number_format($total * 0.18, 2) }}</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="cart-total">S/ {{ number_format($total * 1.18, 2) }}</strong>
                        </div>

                        <form action="{{ route('checkout.index') }}" method="GET">
                            <div class="mb-3">
                                <label for="coupon_code" class="form-label">Código de Cupón</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="coupon_code" name="coupon_code" placeholder="Ingresa tu cupón">
                                    <button class="btn btn-outline-secondary" type="button" id="apply-coupon">Aplicar</button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-check-circle"></i> Proceder al Checkout
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-lock"></i> Compra segura
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">Aceptamos</h6>
                        <div class="d-flex justify-content-around">
                            <i class="fas fa-money-bill-wave fa-2x text-success" title="Efectivo"></i>
                            <i class="fas fa-exchange-alt fa-2x text-info" title="Transferencia"></i>
                            <i class="fab fa-whatsapp fa-2x text-success" title="Pago por WhatsApp"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
            <h3>Tu carrito está vacío</h3>
            <p class="text-muted">Agrega algunos productos para comenzar</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                <i class="fas fa-shopping-bag"></i> Ver Productos
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Actualizar cantidad
    $('.btn-decrease, .btn-increase').click(function() {
        const row = $(this).closest('tr');
        const input = row.find('.quantity-input');
        let quantity = parseInt(input.val());
        const max = parseInt(input.attr('max'));

        if ($(this).hasClass('btn-decrease')) {
            quantity = Math.max(1, quantity - 1);
        } else {
            quantity = Math.min(max, quantity + 1);
        }

        input.val(quantity);
        updateCart(row.data('id'), quantity);
    });

    $('.quantity-input').change(function() {
        const row = $(this).closest('tr');
        const quantity = parseInt($(this).val());
        const max = parseInt($(this).attr('max'));

        if (quantity < 1 || quantity > max) {
            $(this).val(1);
            return;
        }

        updateCart(row.data('id'), quantity);
    });

    // Eliminar del carrito
    $('.btn-remove').click(function() {
        if (confirm('¿Eliminar este producto del carrito?')) {
            const row = $(this).closest('tr');
            removeFromCart(row.data('id'));
        }
    });

    function updateCart(id, quantity) {
        $.post('{{ route("cart.update") }}', {
            _token: '{{ csrf_token() }}',
            id: id,
            quantity: quantity
        }, function(data) {
            if (data.success) {
                location.reload();
            }
        });
    }

    function removeFromCart(id) {
        $.post('{{ route("cart.remove") }}', {
            _token: '{{ csrf_token() }}',
            id: id
        }, function(data) {
            if (data.success) {
                location.reload();
            }
        });
    }

    // Aplicar cupón
    $('#apply-coupon').click(function() {
        const code = $('#coupon_code').val();
        if (code) {
            // Aquí podrías validar el cupón via AJAX
            showToast('Cupón aplicado: ' + code);
        }
    });
});
</script>
@endpush
