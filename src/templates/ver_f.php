<?php
$host = "localhost";
$usuario = "root"; 
$contrasena = "";
$base_datos = "facturacion";

$conn = new mysqli($host, $usuario, $contrasena, $base_datos);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = "";

// Validar y preparar el ID de factura
$id_factura = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_factura <= 0) {
    die("ID de factura inválido.");
}

// Obtener datos de la factura y el cliente
$stmt_factura = $conn->prepare("SELECT f.*, c.nombre, c.apellido FROM factura f
                                JOIN cliente c ON f.id_cliente = c.id_cliente
                                WHERE f.id_factura = ?");
$stmt_factura->bind_param("i", $id_factura);
$stmt_factura->execute();
$result_factura = $stmt_factura->get_result();
$factura = $result_factura->fetch_assoc();

if (!$factura) {
    die("Factura no encontrada.");
}

// Obtener detalles de los productos
$stmt_detalles = $conn->prepare("SELECT df.*, p.nombre FROM detalle_factura df
                                 JOIN producto p ON df.id_producto = p.id_producto
                                 WHERE df.id_factura = ?");
$stmt_detalles->bind_param("i", $id_factura);
$stmt_detalles->execute();
$detalles = $stmt_detalles->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Factura #<?php echo $id_factura; ?></title>
    <style>
        body { font-family: Arial; margin: 40px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; }
    </style>
</head>
<body onload="window.print()">

<h2>Factura #<?php echo $id_factura; ?></h2>
<p><strong>Cliente:</strong> <?php echo htmlspecialchars($factura['nombre'] . " " . $factura['apellido']); ?></p>
<p><strong>Fecha:</strong> <?php echo htmlspecialchars($factura['fecha_factura']); ?></p>
<p><strong>Método de pago:</strong> <?php echo htmlspecialchars($factura['num_pago']); ?></p>

<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $detalles->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
            <td><?php echo (int)$row['cantidad']; ?></td>
            <td>$<?php echo number_format($row['subtotal'], 2); ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<p><strong>Total:</strong> $<?php echo number_format($factura['monto'], 2); ?></p>

</body>
</html>
