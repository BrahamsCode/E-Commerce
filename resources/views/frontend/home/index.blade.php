@extends('layouts.app')

@section('title', 'Inicio - Tu Tienda Online')

@section('content')
<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Bienvenido a {{ config('app.name') }}</h1>
                <p class="lead mb-4">Tu tienda online de confianza en Perú. Encuentra los mejores productos con envío a todo el país.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-shopping-bag"></i> Ver Productos
                    </a>
                    <a href="#ofertas" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-tag"></i> Ver Ofertas
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="https://via.placeholder.com/600x400" alt="Hero Image" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Características -->
<section class="features-section py-4 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-3">
                <div class="feature-box">
                    <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                    <h5>Envío Rápido</h5>
                    <p class="text-muted small">Entrega en 24-48 horas en Lima</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="feature-box">
                    <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                    <h5>Compra Segura</h5>
                    <p class="text-muted small">Protegemos tus datos</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="feature-box">
                    <i class="fab fa-whatsapp fa-3x text-success mb-3"></i>
                    <h5>Atención por WhatsApp</h5>
                    <p class="text-muted small">Respuesta inmediata</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="feature-box">
                    <i class="fas fa-undo fa-3x text-primary mb-3"></i>
                    <h5>Garantía</h5>
                    <p class="text-muted small">7 días para cambios</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categorías Principales -->
<section class="categories-section py-5">
    <div class="container">
        <h2 class="text-center mb-4">Explora por Categorías</h2>
        <div class="row g-4">
            @foreach($mainCategories as $category)
                <div class="col-md-4 col-lg-2">
                    <a href="{{ route('category.products', $category) }}" class="text-decoration-none">
                        <div class="category-card text-center p-4 rounded shadow-sm h-100">
                            <i class="fas fa-box fa-3x text-primary mb-3"></i>
                            <h6>{{ $category->name }}</h6>
                            <small class="text-muted">{{ $category->active_products_count }} productos</small>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Productos Destacados -->
<section class="featured-products py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Productos Destacados</h2>
            <a href="{{ route('products.index') }}?featured=1" class="btn btn-outline-primary">Ver Todos</a>
        </div>

        <div class="row g-4">
            @foreach($featuredProducts as $product)
                <div class="col-md-6 col-lg-3">
                    <div class="card product-card h-100 shadow-sm">
                        @if($product->hasDiscount())
                            <span class="badge badge-discount">-{{ $product->getDiscountPercentage() }}%</span>
                        @endif

                        <a href="{{ route('products.show', $product) }}">
                            @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->url }}"
                                     class="card-img-top"
                                     alt="{{ $product->name }}"
                                     style="height: 250px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center"
                                     style="height: 250px;">
                                    <i class="fas fa-image fa-4x text-muted"></i>
                                </div>
                            @endif
                        </a>

                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                                    {{ Str::limit($product->name, 50) }}
                                </a>
                            </h6>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if($product->hasDiscount())
                                        <small class="text-muted text-decoration-line-through">
                                            S/ {{ number_format($product->compare_price, 2) }}
                                        </small><br>
                                    @endif
                                    <strong class="text-primary">S/ {{ number_format($product->price, 2) }}</strong>
                                </div>
                                <button class="btn btn-primary btn-sm" onclick="addToCart({{ $product->id }}, 1)">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Ofertas Especiales -->
<section id="ofertas" class="sale-products py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-fire text-danger"></i> Ofertas Especiales</h2>
            <a href="{{ route('products.index') }}?ofertas=1" class="btn btn-outline-danger">Ver Todas</a>
        </div>

        <div class="row g-4">
            @foreach($saleProducts as $product)
                <div class="col-md-6 col-lg-3">
                    <div class="card product-card h-100 shadow-sm border-danger">
                        <span class="badge badge-discount">-{{ $product->getDiscountPercentage() }}%</span>

                        <a href="{{ route('products.show', $product) }}">
                            @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->url }}"
                                     class="card-img-top"
                                     alt="{{ $product->name }}"
                                     style="height: 250px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center"
                                     style="height: 250px;">
                                    <i class="fas fa-image fa-4x text-muted"></i>
                                </div>
                            @endif
                        </a>

                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                                    {{ Str::limit($product->name, 50) }}
                                </a>
                            </h6>

                            <div class="text-center">
                                <small class="text-muted text-decoration-line-through">
                                    S/ {{ number_format($product->compare_price, 2) }}
                                </small><br>
                                <strong class="text-danger h5">S/ {{ number_format($product->price, 2) }}</strong>
                            </div>

                            <div class="d-grid mt-3">
                                <button class="btn btn-danger" onclick="addToCart({{ $product->id }}, 1)">
                                    <i class="fas fa-cart-plus"></i> Agregar al Carrito
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Nuevos Productos -->
<section class="new-products py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-sparkles text-info"></i> Nuevos Productos</h2>
            <a href="{{ route('products.index') }}?ordenar=nuevo" class="btn btn-outline-info">Ver Todos</a>
        </div>

        <div class="row g-4">
            @foreach($newProducts->take(4) as $product)
                <div class="col-md-6 col-lg-3">
                    <div class="card product-card h-100 shadow-sm">
                        <span class="badge bg-info position-absolute top-0 start-0 m-2">Nuevo</span>

                        <a href="{{ route('products.show', $product) }}">
                            @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->url }}"
                                     class="card-img-top"
                                     alt="{{ $product->name }}"
                                     style="height: 250px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center"
                                     style="height: 250px;">
                                    <i class="fas fa-image fa-4x text-muted"></i>
                                </div>
                            @endif
                        </a>

                        <div class="card-body">
                            <h6 class="card-title">
                                <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                                    {{ Str::limit($product->name, 50) }}
                                </a>
                            </h6>

                            <div class="d-flex justify-content-between align-items-center">
                                <strong class="text-primary">S/ {{ number_format($product->price, 2) }}</strong>
                                <button class="btn btn-info btn-sm text-white" onclick="addToCart({{ $product->id }}, 1)">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Marcas -->
<section class="brands-section py-5">
    <div class="container">
        <h2 class="text-center mb-4">Marcas Populares</h2>
        <div class="row justify-content-center align-items-center">
            {{-- @foreach($popularBrands as $brand)
                <div class="col-6 col-md-3 col-lg-2 mb-3">
                    <a href="{{ route('brand.products', $brand) }}" class="d-block text-center brand-item">
                        @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}"
                                 alt="{{ $brand->name }}"
                                 class="img-fluid"
                                 style="max-height: 80px;">
                        @else
                            <div class="brand-name p-3 bg-light rounded">
                                <h5 class="mb-0">{{ $brand->name }}</h5>
                            </div>
                        @endif
                    </a>
                </div>
            @endforeach --}}
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="newsletter-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <h3 class="mb-4">Suscríbete a nuestro Newsletter</h3>
                <p class="mb-4">Recibe las mejores ofertas y novedades directamente en tu correo</p>
                <form class="newsletter-form">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Tu correo electrónico" required>
                        <button class="btn btn-light" type="submit">
                            <i class="fas fa-paper-plane"></i> Suscribirse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.category-card {
    background: white;
    transition: all 0.3s;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.1) !important;
}

.brand-item {
    opacity: 0.7;
    transition: opacity 0.3s;
}

.brand-item:hover {
    opacity: 1;
}

.feature-box {
    padding: 20px;
    transition: all 0.3s;
}

.feature-box:hover {
    transform: translateY(-5px);
}
</style>
@endpush
