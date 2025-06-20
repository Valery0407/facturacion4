<?php
$conexion = new mysqli("localhost", "root", " ", "facturacion");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener opciones para los filtros
$clientes = $conexion->query("SELECT id, nombre FROM clientes");
$metodos = $conexion->query("SELECT id, metodo FROM metodos_pago");

// Procesar filtros si se envió el formulario
$filtros = [];
$parametros = [];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_GET['cliente_id'])) {
        $filtros[] = "ventas.cliente_id = ?";
        $parametros[] = $_GET['cliente_id'];
    }

    if (!empty($_GET['metodo_pago_id'])) {
        $filtros[] = "ventas.metodo_pago_id = ?";
        $parametros[] = $_GET['metodo_pago_id'];
    }

    if (!empty($_GET['fecha_inicio']) && !empty($_GET['fecha_fin'])) {
        $filtros[] = "ventas.fecha BETWEEN ? AND ?";
        $parametros[] = $_GET['fecha_inicio'];
        $parametros[] = $_GET['fecha_fin'];
    }

    $sql = "
        SELECT ventas.id, productos.nombre AS producto, clientes.nombre AS cliente, metodos_pago.metodo, ventas.fecha
        FROM ventas
        JOIN productos ON ventas.producto_id = productos.id
        JOIN clientes ON ventas.cliente_id = clientes.id
        JOIN metodos_pago ON ventas.metodo_pago_id = metodos_pago.id
    ";

    if (!empty($filtros)) {
        $sql .= " WHERE " . implode(" AND ", $filtros);
    }

    $stmt = $conexion->prepare($sql);

    if (!empty($parametros)) {
        $types = str_repeat("i", count($parametros)); // asumiendo todos INT excepto fechas
        if (isset($_GET['fecha_inicio'])) $types = str_replace('ii', 'ss', $types); // si hay fechas
        $stmt->bind_param($types, ...$parametros);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Filtro de Ventas</title>
</head>
<body>
    <h2>Filtrar Ventas</h2>
    <form method="GET">
        <label>Cliente:</label>
        <select name="cliente_id">
            <option value="">-- Todos --</option>
            <?php while ($row = $clientes->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nombre']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Método de Pago:</label>
        <select name="metodo_pago_id">
            <option value="">-- Todos --</option>
            <?php while ($row = $metodos->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['metodo']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Fecha Inicio:</label>
        <input type="date" name="fecha_inicio">

        <label>Fecha Fin:</label>
        <input type="date" name="fecha_fin">

        <button type="submit">Filtrar</button>
    </form>

    <?php if (isset($resultado)): ?>
        <h3>Resultados:</h3>
        <table border="1">
            <tr>
                <th>ID Venta</th>
                <th>Producto</th>
                <th>Cliente</th>
                <th>Método de Pago</th>
                <th>Fecha</th>
            </tr>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $fila['id'] ?></td>
                    <td><?= htmlspecialchars($fila['producto']) ?></td>
                    <td><?= htmlspecialchars($fila['cliente']) ?></td>
                    <td><?= htmlspecialchars($fila['metodo']) ?></td>
                    <td><?= $fila['fecha'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</body>
</html>

<?php $conexion->close(); ?>
