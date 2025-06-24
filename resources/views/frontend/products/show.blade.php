@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Productos</a></li>
            <li class="breadcrumb-item"><a href="{{ route('category.products', $product->category) }}">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Galería de Imágenes -->
        <div class="col-lg-6">
            <div class="product-gallery">
                @if($product->images->count() > 0)
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($product->images as $index => $image)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ $image->url }}" class="d-block w-100" alt="{{ $image->alt_text ?? $product->name }}">
                                </div>
                            @endforeach
                        </div>
                        @if($product->images->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        @endif
                    </div>

                    @if($product->images->count() > 1)
                        <div class="row mt-3">
                            @foreach($product->images as $index => $image)
                                <div class="col-3">
                                    <img src="{{ $image->url }}"
                                         class="img-thumbnail cursor-pointer"
                                         alt="{{ $image->alt_text ?? $product->name }}"
                                         onclick="$('#productCarousel').carousel({{ $index }})"
                                         style="cursor: pointer;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 500px;">
                        <i class="fas fa-image fa-5x text-muted"></i>
                    </div>
                @endif
            </div>
        </div>

        <!-- Información del Producto -->
        <div class="col-lg-6">
            <h1 class="h2 mb-3">{{ $product->name }}</h1>

            @if($product->brand)
                <p class="text-muted mb-3">
                    Marca: <a href="{{ route('brand.products', $product->brand) }}" class="text-decoration-none">{{ $product->brand->name }}</a>
                </p>
            @endif

            <div class="mb-3">
                <span class="text-muted">SKU: {{ $product->sku }}</span>
            </div>

            <!-- Precio -->
            <div class="price-section mb-4">
                @if($product->hasDiscount())
                    <span class="h4 text-muted text-decoration-line-through">
                        S/ {{ number_format($product->compare_price, 2) }}
                    </span>
                    <span class="badge bg-danger ms-2">-{{ $product->getDiscountPercentage() }}%</span>
                    <br>
                @endif
                <span class="h2 text-primary">S/ {{ number_format($product->price, 2) }}</span>
            </div>

            <!-- Stock Status -->
            <div class="mb-4">
                @if($product->stock_status == 'in_stock')
                    <span class="badge bg-success"><i class="fas fa-check"></i> En Stock ({{ $product->stock }} unidades)</span>
                @elseif($product->stock_status == 'low_stock')
                    <span class="badge bg-warning"><i class="fas fa-exclamation-triangle"></i> Últimas {{ $product->stock }} unidades</span>
                @else
                    <span class="badge bg-danger"><i class="fas fa-times"></i> Agotado</span>
                @endif
            </div>

            <!-- Descripción Corta -->
            @if($product->short_description)
                <div class="mb-4">
                    <p>{{ $product->short_description }}</p>
                </div>
            @endif

            <!-- Variantes -->
            @if($product->variants->count() > 0)
                <div class="mb-4">
                    <label class="form-label fw-bold">Opciones disponibles:</label>
                    <select class="form-select" id="variant-select">
                        <option value="">Selecciona una opción</option>
                        @foreach($product->variants as $variant)
                            <option value="{{ $variant->id }}"
                                    data-price="{{ $variant->price }}"
                                    data-stock="{{ $variant->stock }}"
                                    {{ $variant->stock == 0 ? 'disabled' : '' }}>
                                {{ $variant->name }} - S/ {{ number_format($variant->price, 2) }}
                                {{ $variant->stock == 0 ? '(Agotado)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Cantidad y Botones -->
            <form id="add-to-cart-form" class="mb-4">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label class="form-label fw-bold mb-0">Cantidad:</label>
                    </div>
                    <div class="col-auto">
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" id="btn-decrease">-</button>
                            <input type="number" class="form-control text-center" id="quantity"
                                   value="1" min="1" max="{{ $product->stock }}" style="width: 60px;">
                            <button class="btn btn-outline-secondary" type="button" id="btn-increase">+</button>
                        </div>
                    </div>
                    <div class="col-12 col-sm">
                        @if($product->stock_status != 'out_of_stock')
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-cart-plus"></i> Agregar al Carrito
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary btn-lg w-100" disabled>
                                <i class="fas fa-times"></i> No Disponible
                            </button>
                        @endif
                    </div>
                </div>
            </form>

            <!-- Botones de Acción -->
            <div class="d-grid gap-2 d-md-block mb-4">
                <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text=Hola,%20me%20interesa%20el%20producto:%20{{ urlencode($product->name) }}%20({{ urlencode(url()->current()) }})"
                   class="btn btn-success" target="_blank">
                    <i class="fab fa-whatsapp"></i> Consultar por WhatsApp
                </a>
                <button type="button" class="btn btn-outline-secondary" onclick="shareProduct()">
                    <i class="fas fa-share-alt"></i> Compartir
                </button>
            </div>

            <!-- Características -->
            @if($product->specifications)
                <div class="specifications mb-4">
                    <h5>Características principales:</h5>
                    <ul class="list-unstyled">
                        @foreach($product->specifications as $key => $value)
                            <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Información adicional -->
            <div class="additional-info">
                @if($product->weight)
                    <p><i class="fas fa-weight"></i> Peso: {{ $product->weight }} kg</p>
                @endif
                @if($product->dimensions)
                    <p><i class="fas fa-ruler"></i> Dimensiones: {{ $product->dimensions }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabs de Información -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                            data-bs-target="#description" type="button">
                        Descripción
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="shipping-tab" data-bs-toggle="tab"
                            data-bs-target="#shipping" type="button">
                        Envío y Entrega
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="warranty-tab" data-bs-toggle="tab"
                            data-bs-target="#warranty" type="button">
                        Garantía
                    </button>
                </li>
            </ul>
            <div class="tab-content border border-top-0 p-4" id="productTabContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    @if($product->description)
                        {!! nl2br(e($product->description)) !!}
                    @else
                        <p>No hay descripción disponible para este producto.</p>
                    @endif
                </div>
                <div class="tab-pane fade" id="shipping" role="tabpanel">
                    <h6>Opciones de Envío</h6>
                    <ul>
                        <li>Envío estándar: 24-48 horas en Lima Metropolitana</li>
                        <li>Envío express: Mismo día (pedidos antes de las 2pm)</li>
                        <li>Envío a provincias: 3-5 días hábiles</li>
                    </ul>
                    <p><strong>Envío gratis</strong> en compras mayores a S/ 150</p>
                </div>
                <div class="tab-pane fade" id="warranty" role="tabpanel">
                    <p>Todos nuestros productos cuentan con garantía del fabricante.</p>
                    <ul>
                        <li>Garantía contra defectos de fábrica</li>
                        <li>7 días para cambios y devoluciones</li>
                        <li>Conserva tu boleta de compra</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos Relacionados -->
    @if($relatedProducts->count() > 0)
        <div class="related-products mt-5">
            <h3 class="mb-4">Productos Relacionados</h3>
            <div class="row g-4">
                @foreach($relatedProducts as $related)
                    <div class="col-md-6 col-lg-3">
                        <div class="card product-card h-100">
                            <a href="{{ route('products.show', $related) }}">
                                @if($related->primaryImage)
                                    <img src="{{ $related->primaryImage->url }}"
                                         class="card-img-top"
                                         alt="{{ $related->name }}"
                                         style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                         style="height: 200px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="card-body">
                                <h6 class="card-title">
                                    <a href="{{ route('products.show', $related) }}" class="text-decoration-none text-dark">
                                        {{ $related->name }}
                                    </a>
                                </h6>
                                <p class="card-text">
                                    <strong class="text-primary">S/ {{ number_format($related->price, 2) }}</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Cambiar precio según variante
    $('#variant-select').change(function() {
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price');
        const stock = selectedOption.data('stock');

        if (price) {
            $('.h2.text-primary').text('S/ ' + parseFloat(price).toFixed(2));
            $('#quantity').attr('max', stock).val(1);
        }
    });

    // Botones de cantidad
    $('#btn-decrease').click(function() {
        const input = $('#quantity');
        const value = parseInt(input.val());
        if (value > 1) {
            input.val(value - 1);
        }
    });

    $('#btn-increase').click(function() {
        const input = $('#quantity');
        const value = parseInt(input.val());
        const max = parseInt(input.attr('max'));
        if (value < max) {
            input.val(value + 1);
        }
    });

    // Agregar al carrito
    $('#add-to-cart-form').submit(function(e) {
        e.preventDefault();

        const quantity = $('#quantity').val();
        const variantId = $('#variant-select').val();

        if ($('#variant-select').length > 0 && !variantId) {
            showToast('Por favor selecciona una opción', 'error');
            return;
        }

        $.post('{{ route("cart.add") }}', {
            _token: '{{ csrf_token() }}',
            product_id: {{ $product->id }},
            variant_id: variantId,
            quantity: quantity
        }, function(data) {
            if (data.success) {
                updateCartCount();
                showToast('Producto agregado al carrito');

                // Opcional: mostrar modal de confirmación
                $('#addedToCartModal').modal('show');
            }
        }).fail(function() {
            showToast('Error al agregar el producto', 'error');
        });
    });
});

// Compartir producto
function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $product->name }}',
            text: '¡Mira este producto!',
            url: window.location.href
        }).catch(err => console.log('Error sharing:', err));
    } else {
        // Fallback: copiar URL
        const dummy = document.createElement('input');
        document.body.appendChild(dummy);
        dummy.value = window.location.href;
        dummy.select();
        document.execCommand('copy');
        document.body.removeChild(dummy);
        showToast('URL copiada al portapapeles');
    }
}
</script>
@endpush

@push('styles')
<style>
.product-gallery img {
    max-height: 500px;
    object-fit: contain;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color: rgba(0,0,0,0.5);
    border-radius: 50%;
}
</style>
@endpush

<!-- Modal de Confirmación -->
<div class="modal fade" id="addedToCartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Producto Agregado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                <p>El producto se agregó correctamente al carrito</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Seguir Comprando</button>
                <a href="{{ route('cart.index') }}" class="btn btn-primary">Ir al Carrito</a>
            </div>
        </div>
    </div>
</div>
