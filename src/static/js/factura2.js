$(document).ready(function() {
    // Actualizar precio cuando se selecciona un producto
    $(document).on('change', '.producto-select', function() {
        const row = $(this).closest('.producto-row');
        const option = $(this).find('option:selected');
        const precio = parseFloat(option.data('precio')) || 0;

        row.find('.precio-unitario').text('$' + precio.toFixed(2));
        row.find('.precio-unitario-input').val(precio); // NUEVO: actualizar input oculto
        calcularSubtotal(row);
        calcularTotal();
    });

    // Actualizar subtotal cuando cambia la cantidad
    $(document).on('change', '.cantidad-input', function() {
        const row = $(this).closest('.producto-row');
        calcularSubtotal(row);
        calcularTotal();
    });

    // Calcular subtotal por fila
    function calcularSubtotal(row) {
        const precio = parseFloat(row.find('.producto-select option:selected').data('precio')) || 0;
        const cantidad = parseInt(row.find('.cantidad-input').val()) || 0;
        const subtotal = precio * cantidad;

        row.find('.subtotal').text('$' + subtotal.toFixed(2));
    }

    // Calcular total general de la factura
    function calcularTotal() {
        let total = 0;
        $('.subtotal').each(function() {
            const subtotal = parseFloat($(this).text().replace('$', '')) || 0;
            total += subtotal;
        });

        $('#total-factura').text('$' + total.toFixed(2));
        $('#monto-total-input').val(total);
    }

    // Agregar nueva fila de producto
    $('#agregar-producto').click(function() {
        const newRow = $('.producto-row').first().clone();
        newRow.find('select').val('');
        newRow.find('input').val(1);
        newRow.find('.precio-unitario').text('$0.00');
        newRow.find('.precio-unitario-input').val(0); // NUEVO: reiniciar input oculto
        newRow.find('.subtotal').text('$0.00');
        $('#productos-container').append(newRow);
        calcularTotal(); // NUEVO: recalcular el total después de agregar
    });

    // Eliminar fila de producto
    $(document).on('click', '.eliminar-producto', function() {
        if ($('.producto-row').length > 1) {
            $(this).closest('.producto-row').remove();
            calcularTotal();
        } else {
            alert('Debe haber al menos un producto en la factura');
        }
    });

    // Validar antes de enviar
    $('#invoice-form').submit(function(e) {
        let stockExcedido = false;
        let mensajeError = '';

        if (!$('#modo_pago').val()) {
            e.preventDefault();
            alert('Debe seleccionar un método de pago');
            return;
        }

        $('.producto-row').each(function() {
            const select = $(this).find('.producto-select');
            if (select.val()) {
                const cantidad = parseInt($(this).find('.cantidad-input').val()) || 0;
                const stock = parseInt(select.find('option:selected').data('stock')) || 0;
                const nombreProducto = select.find('option:selected').text();

                if (cantidad > stock) {
                    stockExcedido = true;
                    mensajeError += 'La cantidad solicitada excede el stock disponible para ' + nombreProducto + '\n';
                }
            }
        });

        if (stockExcedido) {
            e.preventDefault();
            alert(mensajeError);
        }
    });
});
