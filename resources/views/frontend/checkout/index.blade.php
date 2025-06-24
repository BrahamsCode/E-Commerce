@extends('layouts.app')

@section('title', 'Finalizar Compra')

@section('content')
<div class="container py-5">
    <h1 class="mb-4"><i class="fas fa-credit-card"></i> Finalizar Compra</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <!-- Datos del Cliente -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Datos del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nombre Completo *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Teléfono/WhatsApp *</label>
                                <div class="input-group">
                                    <span class="input-group-text">+51</span>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}"
                                           placeholder="999 999 999" required>
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="document_type" class="form-label">Tipo Documento</label>
                                <select class="form-select" id="document_type" name="document_type">
                                    <option value="">Seleccionar</option>
                                    <option value="dni" {{ old('document_type') == 'dni' ? 'selected' : '' }}>DNI</option>
                                    <option value="ruc" {{ old('document_type') == 'ruc' ? 'selected' : '' }}>RUC</option>
                                    <option value="ce" {{ old('document_type') == 'ce' ? 'selected' : '' }}>CE</option>
                                    <option value="passport" {{ old('document_type') == 'passport' ? 'selected' : '' }}>Pasaporte</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="document_number" class="form-label">Número</label>
                                <input type="text" class="form-control" id="document_number" name="document_number"
                                       value="{{ old('document_number') }}">
                            </div>
                        </div>

                        <div class="row" id="company-fields" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">Razón Social</label>
                                <input type="text" class="form-control" id="company_name" name="company_name"
                                       value="{{ old('company_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="company_ruc" class="form-label">RUC</label>
                                <input type="text" class="form-control" id="company_ruc" name="company_ruc"
                                       value="{{ old('company_ruc') }}" maxlength="11">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dirección de Facturación -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Dirección de Facturación</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="billing_address" class="form-label">Dirección Completa *</label>
                            <textarea class="form-control @error('billing_address') is-invalid @enderror"
                                      id="billing_address" name="billing_address" rows="2" required>{{ old('billing_address') }}</textarea>
                            @error('billing_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="billing_reference" class="form-label">Referencia</label>
                            <input type="text" class="form-control" id="billing_reference" name="billing_reference"
                                   value="{{ old('billing_reference') }}" placeholder="Entre calles, cerca de...">
                        </div>
                    </div>
                </div>

                <!-- Dirección de Envío -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-truck"></i> Dirección de Envío</h5>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="same_address" checked>
                            <label class="form-check-label" for="same_address">
                                Usar la misma dirección de facturación
                            </label>
                        </div>
                    </div>
                    <div class="card-body" id="shipping-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Dirección Completa *</label>
                            <textarea class="form-control @error('shipping_address') is-invalid @enderror"
                                      id="shipping_address" name="shipping_address" rows="2">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="shipping_reference" class="form-label">Referencia</label>
                            <input type="text" class="form-control" id="shipping_reference" name="shipping_reference"
                                   value="{{ old('shipping_reference') }}" placeholder="Entre calles, cerca de...">
                        </div>
                    </div>
                </div>

                <!-- Notas -->
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-comment"></i> Notas del Pedido</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="Instrucciones especiales para tu pedido...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Resumen del Pedido -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Resumen del Pedido</h5>
                    </div>
                    <div class="card-body">
                        @foreach($items as $item)
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <small>{{ $item['product']->name }}</small>
                                    @if($item['variant'])
                                        <small class="text-muted d-block">{{ $item['variant']->name }}</small>
                                    @endif
                                    <small class="text-muted">x{{ $item['quantity'] }}</small>
                                </div>
                                <div>
                                    <small>S/ {{ number_format($item['subtotal'], 2) }}</small>
                                </div>
                            </div>
                        @endforeach

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>S/ {{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2" id="discount-row" style="display: none;">
                            <span>Descuento:</span>
                            <span class="text-success" id="discount-amount">-S/ 0.00</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>IGV (18%):</span>
                            <span>S/ {{ number_format($tax_amount, 2) }}</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <strong>Total a Pagar:</strong>
                            <strong class="text-primary">S/ {{ number_format($total, 2) }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Método de Pago -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-wallet"></i> Método de Pago</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Coordinaremos el pago por WhatsApp después de confirmar tu pedido.
                        </div>

                        <div class="payment-methods">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" checked>
                                <label class="form-check-label" for="cash">
                                    <i class="fas fa-money-bill-wave text-success"></i> Efectivo contra entrega
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="transfer" value="transfer">
                                <label class="form-check-label" for="transfer">
                                    <i class="fas fa-university text-info"></i> Transferencia bancaria
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="yape" value="yape">
                                <label class="form-check-label" for="yape">
                                    <i class="fas fa-mobile-alt text-primary"></i> Yape / Plin
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cupón -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="input-group">
                            <input type="text" class="form-control" name="coupon_code" id="coupon_code"
                                   placeholder="Código de cupón" value="{{ old('coupon_code', request('coupon_code')) }}">
                            <button class="btn btn-outline-secondary" type="button" id="validate-coupon">
                                <i class="fas fa-tag"></i> Validar
                            </button>
                        </div>
                        <small class="text-muted" id="coupon-message"></small>
                    </div>
                </div>

                <!-- Botón de Confirmación -->
                <button type="submit" class="btn btn-success btn-lg w-100" id="confirm-order">
                    <i class="fab fa-whatsapp"></i> Confirmar Pedido por WhatsApp
                </button>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fas fa-lock"></i> Tu información está segura<br>
                        <i class="fas fa-truck"></i> Envío a todo Lima Metropolitana
                    </small>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Mostrar/ocultar campos de empresa
    $('#document_type').change(function() {
        if ($(this).val() === 'ruc') {
            $('#company-fields').slideDown();
        } else {
            $('#company-fields').slideUp();
        }
    });

    // Copiar dirección de facturación a envío
    $('#same_address').change(function() {
        if ($(this).is(':checked')) {
            $('#shipping-fields').slideUp();
            $('#shipping_address').val($('#billing_address').val());
            $('#shipping_reference').val($('#billing_reference').val());
        } else {
            $('#shipping-fields').slideDown();
        }
    });

    // Sincronizar direcciones si está marcado
    $('#billing_address, #billing_reference').on('input', function() {
        if ($('#same_address').is(':checked')) {
            $('#shipping_address').val($('#billing_address').val());
            $('#shipping_reference').val($('#billing_reference').val());
        }
    });

    // Validar cupón
    $('#validate-coupon').click(function() {
        const code = $('#coupon_code').val();
        if (!code) {
            $('#coupon-message').text('Ingresa un código de cupón').removeClass('text-success').addClass('text-danger');
            return;
        }

        // Aquí deberías hacer una llamada AJAX para validar el cupón
        // Por ahora solo mostraremos un mensaje de ejemplo
        $.post('/api/validate-coupon', {
            _token: '{{ csrf_token() }}',
            code: code,
            subtotal: {{ $subtotal }}
        }, function(data) {
            if (data.valid) {
                $('#coupon-message').text('Cupón válido: ' + data.discount_text).removeClass('text-danger').addClass('text-success');
                $('#discount-row').show();
                $('#discount-amount').text('-S/ ' + data.discount_amount);
                // Actualizar total
            } else {
                $('#coupon-message').text(data.message).removeClass('text-success').addClass('text-danger');
                $('#discount-row').hide();
            }
        }).fail(function() {
            $('#coupon-message').text('Error al validar el cupón').removeClass('text-success').addClass('text-danger');
        });
    });

    // Validar formulario antes de enviar
    $('#checkout-form').submit(function(e) {
        e.preventDefault();

        // Validar campos requeridos
        let isValid = true;
        const requiredFields = ['name', 'phone', 'billing_address'];

        requiredFields.forEach(function(field) {
            const input = $('#' + field);
            if (!input.val().trim()) {
                input.addClass('is-invalid');
                isValid = false;
            } else {
                input.removeClass('is-invalid');
            }
        });

        // Si no está marcado "misma dirección", validar dirección de envío
        if (!$('#same_address').is(':checked')) {
            if (!$('#shipping_address').val().trim()) {
                $('#shipping_address').addClass('is-invalid');
                isValid = false;
            }
        } else {
            // Copiar dirección de facturación a envío
            $('#shipping_address').val($('#billing_address').val());
            $('#shipping_reference').val($('#billing_reference').val());
        }

        // Validar teléfono (9 dígitos)
        const phone = $('#phone').val().replace(/\s/g, '');
        if (!/^\d{9}$/.test(phone)) {
            $('#phone').addClass('is-invalid');
            isValid = false;
        }

        // Validar email si se proporciona
        const email = $('#email').val();
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#email').addClass('is-invalid');
            isValid = false;
        }

        if (isValid) {
            // Deshabilitar botón y mostrar loading
            $('#confirm-order').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

            // Enviar formulario
            this.submit();
        } else {
            showToast('Por favor completa todos los campos requeridos', 'error');
        }
    });

    // Formatear teléfono
    $('#phone').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 9) {
            value = value.substr(0, 9);
        }
        if (value.length >= 6) {
            value = value.substr(0, 3) + ' ' + value.substr(3, 3) + ' ' + value.substr(6);
        } else if (value.length >= 3) {
            value = value.substr(0, 3) + ' ' + value.substr(3);
        }
        $(this).val(value);
    });

    // Formatear RUC
    $('#company_ruc, #document_number').on('input', function() {
        const isRuc = $(this).attr('id') === 'company_ruc' || $('#document_type').val() === 'ruc';
        let value = $(this).val().replace(/\D/g, '');

        if (isRuc && value.length > 11) {
            value = value.substr(0, 11);
        } else if (!isRuc && value.length > 8) {
            value = value.substr(0, 8);
        }

        $(this).val(value);
    });
});

function showToast(message, type = 'success') {
    const bgClass = type === 'error' ? 'bg-danger' : 'bg-success';
    const toastHtml = `
        <div class="toast position-fixed bottom-0 end-0 m-3" role="alert">
            <div class="toast-header ${bgClass} text-white">
                <strong class="me-auto">Notificación</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">${message}</div>
        </div>
    `;
    $('body').append(toastHtml);
    const toast = new bootstrap.Toast($('.toast').last()[0]);
    toast.show();
    setTimeout(() => $('.toast').last().remove(), 5000);
}
</script>
@endpush
