@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
            <li class="breadcrumb-item active">Productos</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Sidebar de Filtros -->
        <div class="col-lg-3">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.index') }}" method="GET" id="filter-form">
                        <!-- Categorías -->
                        <div class="mb-4">
                            <h6>Categorías</h6>
                            @foreach($categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="categoria"
                                           id="cat-{{ $category->slug }}" value="{{ $category->slug }}"
                                           {{ request('categoria') == $category->slug ? 'checked' : '' }}
                                           onchange="this.form.submit()">
                                    <label class="form-check-label" for="cat-{{ $category->slug }}">
                                        {{ $category->name }}
                                    </label>
                                </div>
                                @if($category->children->count() > 0 && request('categoria') == $category->slug)
                                    <div class="ms-3">
                                        @foreach($category->children as $child)
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="categoria"
                                                       id="cat-{{ $child->slug }}" value="{{ $child->slug }}"
                                                       {{ request('categoria') == $child->slug ? 'checked' : '' }}
                                                       onchange="this.form.submit()">
                                                <label class="form-check-label" for="cat-{{ $child->slug }}">
                                                    {{ $child->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <!-- Marcas -->
                        <div class="mb-4">
                            <h6>Marcas</h6>
                            <select class="form-select form-select-sm" name="marca" onchange="this.form.submit()">
                                <option value="">Todas las marcas</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->slug }}" {{ request('marca') == $brand->slug ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Rango de Precio -->
                        <div class="mb-4">
                            <h6>Precio</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm"
                                           name="precio_min" placeholder="Mín"
                                           value="{{ request('precio_min') }}"
                                           min="0">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm"
                                           name="precio_max" placeholder="Máx"
                                           value="{{ request('precio_max') }}"
                                           min="0">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary mt-2 w-100">
                                Aplicar Precio
                            </button>
                        </div>

                        <!-- Stock -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="en_stock"
                                       id="en_stock" value="1"
                                       {{ request('en_stock') ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <label class="form-check-label" for="en_stock">
                                    Solo productos en stock
                                </label>
                            </div>
                        </div>

                        <!-- Limpiar Filtros -->
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-times"></i> Limpiar Filtros
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista de Productos -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Productos <small class="text-muted">({{ $products->total() }} resultados)</small></h1>

                <div class="d-flex align-items-center">
                    <label class="me-2">Ordenar por:</label>
                    <select class="form-select form-select-sm" style="width: auto;"
                            onchange="window.location.href='{{ route('products.index') }}?ordenar=' + this.value + '{{ request()->has('categoria') ? '&categoria=' . request('categoria') : '' }}{{ request()->has('marca') ? '&marca=' . request('marca') : '' }}'">
                        <option value="relevancia" {{ request('ordenar') == 'relevancia' ? 'selected' : '' }}>Relevancia</option>
                        <option value="precio_asc" {{ request('ordenar') == 'precio_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
                        <option value="precio_desc" {{ request('ordenar') == 'precio_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                        <option value="nombre" {{ request('ordenar') == 'nombre' ? 'selected' : '' }}>Nombre: A-Z</option>
                        <option value="nuevo" {{ request('ordenar') == 'nuevo' ? 'selected' : '' }}>Más Recientes</option>
                    </select>
                </div>
            </div>

            <div class="row g-4">
                @forelse($products as $product)
                    <div class="col-md-6 col-lg-4">
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

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                                        {{ $product->name }}
                                    </a>
                                </h5>

                                <p class="card-text text-muted small mb-2">
                                    {{ Str::limit($product->short_description, 80) }}
                                </p>

                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            @if($product->hasDiscount())
                                                <span class="text-muted text-decoration-line-through">
                                                    S/ {{ number_format($product->compare_price, 2) }}
                                                </span><br>
                                            @endif
                                            <span class="h5 text-primary mb-0">
                                                S/ {{ number_format($product->price, 2) }}
                                            </span>
                                        </div>
                                        <div>
                                            @if($product->stock_status == 'in_stock')
                                                <span class="badge bg-success">En Stock</span>
                                            @elseif($product->stock_status == 'low_stock')
                                                <span class="badge bg-warning">Pocas unidades</span>
                                            @else
                                                <span class="badge bg-danger">Agotado</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> Ver Detalles
                                        </a>
                                        @if($product->stock_status != 'out_of_stock')
                                            <button type="button" class="btn btn-primary btn-sm"
                                                    onclick="addToCart({{ $product->id }}, 1)">
                                                <i class="fas fa-cart-plus"></i> Agregar al Carrito
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> No se encontraron productos con los filtros seleccionados.
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            <div class="mt-5">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // El script addToCart ya está definido en el layout principal
</script>
@endpush
