<?php
// Conexión a la base de datos
$host = "localhost";
$usuario = "root"; // Cambia según tu configuración
$contrasena = "";
$base_datos = "facturacion"; // Cambia por el nombre real

$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $direccion = $_POST['direccion'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    // Validar edad
    $fecha_actual = new DateTime();
    $fecha_nac = new DateTime($fecha_nacimiento);
    $edad = $fecha_actual->diff($fecha_nac)->y;

    if ($edad < 18) {
        $mensaje = "❌ Debes tener al menos 18 años para registrarte.";
    } else {
        // Insertar en base de datos
        $sql = "INSERT INTO cliente (nombre, apellido, direccion, fecha_nacimiento, telefono, email)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssis", $nombre, $apellido, $direccion, $fecha_nacimiento, $telefono, $email);

        if ($stmt->execute()) {
            $mensaje = "✅ Registro exitoso. Ahora puedes iniciar sesión.";
        } else {
            $mensaje = "❌ Error al registrar: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Cliente</title>
    <link rel="stylesheet" href="">
</head>
<body>
    <h2>Registro de Cliente</h2>

    <?php if (!empty($mensaje)) : ?>
        <p><strong><?php echo $mensaje; ?></strong></p>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="apellido">Apellido:</label><br>
        <input type="text" id="apellido" name="apellido" required><br><br>

        <label for="direccion">Dirección:</label><br>
        <input type="text" id="direccion" name="direccion" required><br><br>

        <label for="fecha_nacimiento">Fecha de Nacimiento:</label><br>
        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required><br><br>

        <label for="telefono">Teléfono:</label><br>
        <input type="text" id="telefono" name="telefono" required pattern="\d{7,15}"><br><br>

        <label for="email">Correo electrónico:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <input type="submit" value="Registrarse">
    </form>
</body>
</html>
