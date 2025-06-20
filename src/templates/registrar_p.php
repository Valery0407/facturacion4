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
    <style>
        <style>
/* === Estilos solo para el formulario de registro de productos === */
h1 {
    font-size: 24px;
    color: #d63384;
    text-align: center;
    margin-bottom: 20px;
    font-family: 'Segoe UI', sans-serif;
}

form {
    max-width: 420px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff0f5;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(255, 192, 203, 0.3);
    font-family: 'Segoe UI', sans-serif;
    color: #4a004e;
}

label {
    font-weight: 500;
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
}

input[type="text"],
input[type="number"],
select {
    width: 100%;
    padding: 10px;
    border: 1px solid #f5b6d2;
    border-radius: 8px;
    margin-bottom: 15px;
    background-color: #fff9fb;
    font-size: 14px;
}

button[type="submit"] {
    width: 100%;
    padding: 10px;
    background: linear-gradient(to right, #ff7eb9, #ff65a3);
    color: white;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    font-size: 15px;
    cursor: pointer;
    transition: background 0.3s ease;
}

button[type="submit"]:hover {
    background: linear-gradient(to right, #ff5d9e, #ff4d88);
}

p strong {
    display: block;
    text-align: center;
    background-color: #fce4ec;
    padding: 10px;
    border-radius: 8px;
    color: #b8005d;
    margin: 10px auto;
    max-width: 400px;
}
</style>

    </style>
</head>
<body>
    

    <?php if (!empty($mensaje)) : ?>
        <p><strong><?php echo $mensaje; ?></strong></p>
    <?php endif; ?>

    <form method="POST" action="">
        <h2>Registrar Producto</h1>

        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Precio:</label>
        <input type="number" name="precio" step="0.01" required>

        <label>Stock:</label>
        <input type="number" name="stock" min="0" required>

        <label>Categoría:</label>
        <select name="categoria_id" required>
            <option value="">Selecciona una categoría</option>
            <?php foreach ($resultado as $row): ?>
                <option value="<?php echo $row['id_categoria']; ?>">
                    <?php echo htmlspecialchars($row['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Registrar</button>
    </form>
</body>
</html>
