<?php
// Conexión a la base de datos
$host = "localhost";
$usuario = "root"; 
$contrasena = "";
$base_datos = "facturacion";

$conn = new mysqli($host, $usuario, $contrasena, $base_datos);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = "";

// Obtener las categorías para el <select>
$resultado = $conn->query("SELECT id_categoria, nombre FROM categoria ORDER BY nombre");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $precio = $_POST["precio"];
    $stock = $_POST["stock"];
    $categoria_id = $_POST["categoria_id"];

    // INSERTAR producto con stock
    $stmt = $conn->prepare("INSERT INTO producto (nombre, precio, stock, id_categoria) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdii", $nombre, $precio, $stock, $categoria_id);

    if ($stmt->execute()) {
        $mensaje = "✅ Producto registrado con éxito.";
    } else {
        $mensaje = "❌ Error al registrar el producto: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Producto</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <h1>Registrar Producto</h1>

    <?php if (!empty($mensaje)) : ?>
        <p><strong><?php echo $mensaje; ?></strong></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Precio:</label><br>
        <input type="number" name="precio" step="0.01" required><br><br>

        <label>Stock:</label><br>
        <input type="number" name="stock" min="0" required><br><br>

        <label>Categoría:</label><br>
        <select name="categoria_id" required>
            <option value="">Selecciona una categoría</option>
            <?php foreach ($resultado as $row): ?>
                <option value="<?php echo $row['id_categoria']; ?>">
                    <?php echo htmlspecialchars($row['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Registrar</button>
    </form>
</body>
</html>
