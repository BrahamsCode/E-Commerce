<?php
// routes/web.php
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\WhatsAppController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\SettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/productos', [ProductController::class, 'index'])->name('products.index');
Route::get('/productos/buscar', [ProductController::class, 'search'])->name('products.search');
Route::get('/producto/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/categoria/{category}', [ProductController::class, 'category'])->name('category.products');
Route::get('/marca/{brand}', [ProductController::class, 'brand'])->name('brand.products');

// Cart Routes
Route::prefix('carrito')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/agregar', [CartController::class, 'add'])->name('cart.add');
    Route::post('/actualizar', [CartController::class, 'update'])->name('cart.update');
    Route::post('/eliminar', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/vaciar', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cantidad', [CartController::class, 'count'])->name('cart.count');
});

// Checkout Routes
Route::prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/procesar', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/confirmacion/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
});

// WhatsApp Routes
Route::post('/whatsapp/enviar-pedido', [WhatsAppController::class, 'sendOrder'])->name('whatsapp.send');

// Static Pages
Route::view('/nosotros', 'pages.about')->name('about');
Route::view('/terminos', 'pages.terms')->name('terms');
Route::view('/privacidad', 'pages.privacy')->name('privacy');
Route::view('/contacto', 'pages.contact')->name('contact');

// Auth Routes (usando Laravel Breeze o similar)
// require __DIR__.'/auth.php';

// Customer Routes (authenticated)
Route::middleware(['auth'])->group(function () {
    Route::get('/mis-pedidos', [OrderController::class, 'myOrders'])->name('orders.index');
    Route::get('/pedido/{order}', [OrderController::class, 'show'])->name('orders.show');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'employee'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Resources
    Route::resource('categorias', CategoryController::class)->names('admin.categories');
    Route::resource('marcas', BrandController::class)->names('admin.brands');
    Route::resource('productos', AdminProductController::class)->names('admin.products');
    Route::resource('pedidos', OrderController::class)->names('admin.orders');
    Route::resource('clientes', CustomerController::class)->names('admin.customers');
    Route::resource('cupones', CouponController::class)->names('admin.coupons');

    // Product Images
    Route::post('/productos/{product}/imagenes', [AdminProductController::class, 'uploadImage'])->name('admin.products.images.upload');
    Route::delete('/productos/imagenes/{image}', [AdminProductController::class, 'deleteImage'])->name('admin.products.images.delete');

    // Order Status
    Route::post('/pedidos/{order}/estado', [OrderController::class, 'updateStatus'])->name('admin.orders.status');
    Route::post('/pedidos/{order}/whatsapp', [OrderController::class, 'sendWhatsApp'])->name('admin.orders.whatsapp');

    // Settings (solo admin)
    Route::middleware(['admin'])->group(function () {
        Route::get('/configuracion', [SettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/configuracion', [SettingController::class, 'update'])->name('admin.settings.update');
    });

    // Reports
    Route::get('/reportes/ventas', [DashboardController::class, 'salesReport'])->name('admin.reports.sales');
    Route::get('/reportes/productos', [DashboardController::class, 'productsReport'])->name('admin.reports.products');
    Route::get('/reportes/clientes', [DashboardController::class, 'customersReport'])->name('admin.reports.customers');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile/edit', function() {
        return view('profile.edit');
    })->name('profile.edit');
});
