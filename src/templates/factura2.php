<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "facturacion");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$cliente = null;
$clientes = [];
$busqueda_realizada = false;

// Procesar búsqueda de cliente
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['busqueda_cliente'])) {
    $busqueda = $conn->real_escape_string($_POST['busqueda']);
    $sql = "SELECT * FROM cliente 
            WHERE nombre LIKE '%$busqueda%' 
               OR apellido LIKE '%$busqueda%' 
               OR email LIKE '%$busqueda%'";
    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $clientes[] = $row;
        }
    }
    $busqueda_realizada = true;
}

// Cliente seleccionado
if (isset($_GET['id_cliente'])) {
    $id_cliente = (int)$_GET['id_cliente'];
    $res = $conn->query("SELECT * FROM cliente WHERE id_cliente = $id_cliente");
    if ($res && $res->num_rows > 0) {
        $cliente = $res->fetch_assoc();
    }
}

// Procesar factura
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['crear_factura'])) {
    $id_cliente = $_POST['id_cliente'];
    $productos = $_POST['producto_id'];
    $cantidades = $_POST['cantidad'];
    $precios = $_POST['precio_unitario'];
    $monto_total = $_POST['monto_total'];
    $modo_pago_nombre = $_POST['modo_pago'];
    $fecha = date('Y-m-d');

    $stmt_pago = $conn->prepare("SELECT num_pago FROM modo_pago WHERE nombre = ?");
    $stmt_pago->bind_param("s", $modo_pago_nombre);
    $stmt_pago->execute();
    $stmt_pago->bind_result($num_pago);
    $stmt_pago->fetch();
    $stmt_pago->close();

    $stmt_factura = $conn->prepare("INSERT INTO factura (monto, fecha_factura, num_pago, id_cliente) VALUES (?, ?, ?, ?)");
    $stmt_factura->bind_param("dsii", $monto_total, $fecha, $num_pago, $id_cliente);
    $stmt_factura->execute();
    $id_factura = $stmt_factura->insert_id;
    $stmt_factura->close();

    for ($i = 0; $i < count($productos); $i++) {
        $producto_id = $productos[$i];
        $cantidad = $cantidades[$i];
        $precio_unitario = $precios[$i];
        $subtotal = $precio_unitario * $cantidad;

        $stmt_detalle = $conn->prepare("INSERT INTO detalle_factura (id_factura, id_producto, cantidad, subtotal) VALUES (?, ?, ?, ?)");
        $stmt_detalle->bind_param("iiid", $id_factura, $producto_id, $cantidad, $subtotal);
        $stmt_detalle->execute();
        $stmt_detalle->close();

        $conn->query("UPDATE producto SET stock = stock - $cantidad WHERE id_producto = $producto_id");
    }

    echo "<script>alert('Factura guardada con éxito'); window.location.href='factura1.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Facturación</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        table { border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 8px 12px; border: 1px solid #aaa; }
        input[type="text"] { padding: 5px; width: 220px; }
        .producto-row { margin-bottom: 10px; }
        .producto-row select, .producto-row input { margin-right: 10px; }
        .subtotal, .precio-unitario { display: inline-block; width: 80px; }
    </style>
</head>
<body>

<h2>Sistema de Facturación</h2>

<!-- Buscador de Cliente -->
<h3>Buscar Cliente</h3>
<form method="POST">
    <input type="text" name="busqueda" placeholder="Nombre, apellido o email" required>
    <button type="submit" name="busqueda_cliente">Buscar</button>
</form>

<?php if ($busqueda_realizada): ?>
    <h3>Resultados de la Búsqueda</h3>
    <?php if (count($clientes) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Acción</th>
            </tr>
            <?php foreach ($clientes as $c): ?>
                <tr>
                    <td><?= $c['id_cliente'] ?></td>
                    <td><?= $c['nombre'] ?></td>
                    <td><?= $c['apellido'] ?></td>
                    <td><?= $c['email'] ?></td>
                    <td><?= $c['telefono'] ?></td>
                    <td>
                        <form method="GET">
                            <input type="hidden" name="id_cliente" value="<?= $c['id_cliente'] ?>">
                            <button type="submit">Seleccionar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No se encontraron clientes.</p>
    <?php endif; ?>
<?php endif; ?>

<?php if ($cliente): ?>
    <h3>Cliente Seleccionado</h3>
    <p><strong>Nombre:</strong> <?= $cliente['nombre'] . ' ' . $cliente['apellido'] ?></p>
    <p><strong>Email:</strong> <?= $cliente['email'] ?></p>
    <p><strong>Teléfono:</strong> <?= $cliente['telefono'] ?></p>
    <form method="GET">
        <button type="submit">Cambiar Cliente</button>
    </form>

    <!-- Formulario de Factura -->
    <h3>Crear Factura</h3>
    <form method="POST" id="invoice-form">
        <input type="hidden" name="id_cliente" value="<?= $cliente['id_cliente'] ?>">
        <input type="hidden" name="crear_factura" value="1">

        <div id="productos-container">
            <div class="producto-row">
                <select class="producto-select" name="producto_id[]" required>
                    <option value="">Seleccione un producto</option>
                    <?php
                    $res = $conn->query("SELECT id_producto, nombre, precio, stock FROM producto");
                    while ($row = $res->fetch_assoc()) {
                        echo "<option value='{$row['id_producto']}' data-precio='{$row['precio']}' data-stock='{$row['stock']}'>
                                {$row['nombre']} - Precio: {$row['precio']} - Stock: {$row['stock']}
                              </option>";
                    }
                    ?>
                </select>
                <input type="number" class="cantidad-input" name="cantidad[]" value="1" min="1" required>
                <span class="precio-unitario">$0.00</span>
                <input type="hidden" class="precio-unitario-input" name="precio_unitario[]" value="0">
                <span class="subtotal">$0.00</span>
                <button type="button" class="eliminar-producto">X</button>
            </div>
        </div>

        <button type="button" id="agregar-producto">Agregar Producto</button><br><br>

        <label for="modo_pago">Método de Pago:</label>
        <select id="modo_pago" name="modo_pago" required>
            <option value="">Seleccione</option>
            <?php
            $res = $conn->query("SELECT nombre FROM modo_pago");
            while ($row = $res->fetch_assoc()) {
                echo "<option value='{$row['nombre']}'>{$row['nombre']}</option>";
            }
            ?>
        </select><br><br>

        <strong>Total:</strong> <span id="total-factura">$0.00</span>
        <input type="hidden" name="monto_total" id="monto-total-input" value="0"><br><br>

        <button type="submit">Guardar Factura</button>
    </form>
<?php endif; ?>

<script>
$(document).ready(function() {
    $(document).on('change', '.producto-select', function() {
        const row = $(this).closest('.producto-row');
        const option = $(this).find('option:selected');
        const precio = parseFloat(option.data('precio')) || 0;
        row.find('.precio-unitario').text('$' + precio.toFixed(2));
        row.find('.precio-unitario-input').val(precio);
        calcularSubtotal(row);
        calcularTotal();
    });

    $(document).on('change', '.cantidad-input', function() {
        const row = $(this).closest('.producto-row');
        calcularSubtotal(row);
        calcularTotal();
    });

    function calcularSubtotal(row) {
        const precio = parseFloat(row.find('.producto-select option:selected').data('precio')) || 0;
        const cantidad = parseInt(row.find('.cantidad-input').val()) || 0;
        const subtotal = precio * cantidad;
        row.find('.subtotal').text('$' + subtotal.toFixed(2));
    }

    function calcularTotal() {
        let total = 0;
        $('.subtotal').each(function() {
            const subtotal = parseFloat($(this).text().replace('$', '')) || 0;
            total += subtotal;
        });
        $('#total-factura').text('$' + total.toFixed(2));
        $('#monto-total-input').val(total);
    }

    $('#agregar-producto').click(function() {
        const newRow = $('.producto-row').first().clone();
        newRow.find('select').val('');
        newRow.find('input').val(1);
        newRow.find('.precio-unitario').text('$0.00');
        newRow.find('.precio-unitario-input').val(0);
        newRow.find('.subtotal').text('$0.00');
        $('#productos-container').append(newRow);
        calcularTotal();
    });

    $(document).on('click', '.eliminar-producto', function() {
        if ($('.producto-row').length > 1) {
            $(this).closest('.producto-row').remove();
            calcularTotal();
        } else {
            alert('Debe haber al menos un producto');
        }
    });

    $('#invoice-form').submit(function(e) {
        let stockExcedido = false;
        let mensajeError = '';

        $('.producto-row').each(function() {
            const select = $(this).find('.producto-select');
            if (select.val()) {
                const cantidad = parseInt($(this).find('.cantidad-input').val()) || 0;
                const stock = parseInt(select.find('option:selected').data('stock')) || 0;
                const nombre = select.find('option:selected').text();
                if (cantidad > stock) {
                    stockExcedido = true;
                    mensajeError += `La cantidad de "${nombre}" excede el stock disponible.\n`;
                }
            }
        });

        if (stockExcedido) {
            e.preventDefault();
            alert(mensajeError);
        }
    });
});
</script>

</body>
</html>
