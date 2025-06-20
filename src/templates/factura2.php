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
    body {
        background: linear-gradient(to bottom, #ffe6f0, #ffd6ea);
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        color: #4a004e;
    }

    h2 {
        text-align: center;
        color:rgb(255, 255, 255);
        margin-bottom: 10px;
    }

    h3 {
        text-align: center;
        color: #d63384;
        margin-top: 20px;
    }


    form {
        text-align: center;
        margin-bottom: 20px;
    }

    input[type="text"], input[type="number"], select {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 10px;
        margin: 5px;
        width: 220px;
    }

    button {
        background: linear-gradient(135deg, #e83e8c, #ff69b4);
        color: white;
        font-weight: bold;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        cursor: pointer;
        transition: transform 0.2s ease, background 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin: 5px;
    }

    button:hover {
        background: linear-gradient(135deg, #d63384, #ff5ca2);
        transform: scale(1.05);
    }
    

    table {
        border-collapse: collapse;
        width: 90%;
        margin: 0 auto 20px auto;
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(255, 105, 135, 0.2);
    }

    th {
        background-color: #ffb6c1;
        color: white;
        padding: 10px;
    }

    td {
        text-align: center;
        padding: 10px;
        font-size: 14px;
        border-bottom: 1px solid #f0f0f0;
    }

    .producto-row {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }

    .producto-row select, .producto-row input {
        margin: 5px;
    }

    .subtotal, .precio-unitario {
        display: inline-block;
        width: 90px;
        font-weight: bold;
        color: #e83e8c;
    }

    #agregar-producto {
        margin-top: 10px;
        background: linear-gradient(135deg, #ffa0c2, #ff69b4);
    }

    #agregar-producto:hover {
        background: linear-gradient(135deg, #ff6fa5, #ff4d91);
    }

    #total-factura {
        font-size: 18px;
        color: #b8005d;
        font-weight: bold;
    }

    .alert {
        background-color: #fce4ec;
        color: #880e4f;
        border: 1px solid #f8bbd0;
        padding: 10px;
        width: 80%;
        margin: 10px auto;
        border-radius: 10px;
        text-align: center;
    }

    @media (max-width: 600px) {
        input[type="text"], input[type="number"], select {
            width: 90%;
        }

        .producto-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .subtotal, .precio-unitario {
            width: auto;
            margin-top: 5px;
        }
    }
    .cliente-card-horizontal {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    background-color: #fff7fa;
    border: 1px solid #f7c7d4;
    border-radius: 12px;
    padding: 15px 20px;
    margin-top: 15px;
    box-shadow: 0 2px 8px rgba(255, 182, 193, 0.2);
    color: #4a004e;
    font-family: 'Segoe UI', sans-serif;
    align-items: center;
}

.cliente-card-horizontal div {
    font-size: 15px;
    white-space: nowrap;
}

.form-cambiar-cliente {
    margin-left: auto;
}

.form-cambiar-cliente button {
    background-color: #ffb6c1;
    color: #fff;
    padding: 8px 14px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.form-cambiar-cliente {
    margin-left: auto;
    display: flex;
    align-items: center;
}


</style>

</head>
<body>

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
                        <form action="template.php" method="GET">
                            <!--Aquí agregué al action template.php para mantener la página ahí, no entendí muy bien, esto dijo chatgpt:
            En el formulario de selección de cliente, estás usando method="GET" pero no estás reteniendo el parámetro page en la URL, así que al hacer submit, el navegador solo pone ?id_cliente=... y te saca del contexto de template.php?page=factura2.-->
                            <input type="hidden" name="id_cliente" value="<?= $c['id_cliente'] ?>">
                            <button type="submit">Seleccionar</button>
                            <input type="hidden" name="page" value="factura2">
            
            <!--Agrega manualmente el parámetro page=factura2 en la acción del formulario GET. Con eso, cuando des clic en "Seleccionar", seguirás en la página factura2 con el cliente seleccionado correctamente.-->
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
    <!--cambié el orden que había para que los datos del cliente se muestren de manera organizada en una fila-->
    <h3>Cliente Seleccionado</h3>
<div class="cliente-card-horizontal">
    <div><strong>ID:</strong> <?= htmlspecialchars($cliente['id_cliente']) ?></div>
    <div><strong>Nombre:</strong> <?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></div>
    <div><strong>Email:</strong> <?= htmlspecialchars($cliente['email']) ?></div>
    <div><strong>Teléfono:</strong> <?= htmlspecialchars($cliente['telefono']) ?></div>
    <form action="template.php" method="GET" class="form-cambiar-cliente">
        <input type="hidden" name="id_cliente" value="">
        <input type="hidden" name="page" value="factura2">
        <button type="submit">Cambiar Cliente</button>
    </form>
</div>

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
