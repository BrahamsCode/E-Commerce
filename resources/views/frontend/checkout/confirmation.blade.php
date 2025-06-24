@extends('layouts.app')

@section('title', 'Pedido Confirmado')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                    <h1 class="mt-4 mb-3">¡Pedido Confirmado!</h1>
                    <p class="lead">Tu pedido <strong>{{ $order->formatted_order_number }}</strong> ha sido registrado exitosamente.</p>

                    <div class="alert alert-info my-4">
                        <i class="fab fa-whatsapp"></i> Hemos enviado los detalles de tu pedido por WhatsApp al número registrado.
                    </div>

                    <div class="row text-start my-5">
                        <div class="col-md-6">
                            <h5>Información del Pedido</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td>Número de Pedido:</td>
                                    <td><strong>{{ $order->formatted_order_number }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Fecha:</td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td>Total:</td>
                                    <td><strong>S/ {{ number_format($order->total_amount, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td>Estado:</td>
                                    <td><span class="badge bg-warning">Pendiente</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Información de Entrega</h5>
                            <p class="mb-1"><strong>{{ $order->customer->name }}</strong></p>
                            <p class="mb-1">{{ $order->customer->phone }}</p>
                            <p class="text-muted">
                                {{ $order->shipping_address['address'] }}<br>
                                @if(isset($order->shipping_address['reference']))
                                    <small>Ref: {{ $order->shipping_address['reference'] }}</small>
                                @endif
                            </p>
                        </div>
                    </div>

                    <h5>Productos Ordenados</h5>
                    <div class="table-responsive mb-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>{{ $item->product_name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>S/ {{ number_format($item->unit_price, 2) }}</td>
                                        <td>S/ {{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end">Subtotal:</td>
                                    <td>S/ {{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                    <tr>
                                        <td colspan="3" class="text-end">Descuento:</td>
                                        <td class="text-success">-S/ {{ number_format($order->discount_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end">IGV (18%):</td>
                                    <td>S/ {{ number_format($order->tax_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>S/ {{ number_format($order->total_amount, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Próximos Pasos:</h6>
                        <ol class="text-start mb-0">
                            <li>Recibirás un mensaje por WhatsApp con las opciones de pago disponibles.</li>
                            <li>Una vez confirmado el pago, procederemos con el envío de tu pedido.</li>
                            <li>El tiempo estimado de entrega es de 24 a 48 horas en Lima Metropolitana.</li>
                        </ol>
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home"></i> Volver al Inicio
                        </a>
                        @auth
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye"></i> Ver Detalles del Pedido
                            </a>
                        @endauth
                        <a href="https://wa.me/{{ config('services.whatsapp.number') }}?text=Hola,%20acabo%20de%20realizar%20el%20pedido%20{{ $order->order_number }}"
                           class="btn btn-success" target="_blank">
                            <i class="fab fa-whatsapp"></i> Contactar por WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-info-circle"></i> ¿Necesitas ayuda?</h6>
                    <p class="mb-0">Si tienes alguna pregunta sobre tu pedido, no dudes en contactarnos:</p>
                    <ul class="list-unstyled mb-0">
                        <li><i class="fas fa-phone"></i> Teléfono: {{ config('services.whatsapp.number') }}</li>
                        <li><i class="fas fa-envelope"></i> Email: {{ config('mail.from.address') }}</li>
                        <li><i class="fab fa-whatsapp"></i> WhatsApp: {{ config('services.whatsapp.number') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-enviar a WhatsApp después de 3 segundos
    setTimeout(function() {
        @if(!$order->whatsapp_sent_at)
            // Marcar como enviado
            $.post('/api/orders/{{ $order->id }}/mark-whatsapp-sent', {
                _token: '{{ csrf_token() }}'
            });

            // Abrir WhatsApp
            window.open('{{ app(App\Http\Controllers\Frontend\WhatsAppController::class)->generateWhatsAppLink($order->customer->phone, app(App\Http\Controllers\Frontend\WhatsAppController::class)->formatOrderMessage($order)) }}', '_blank');
        @endif
    }, 3000);
</script>
@endpush
