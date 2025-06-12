<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root";
$password = "";
$database = "facturacion";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Procesar login
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST["correo"];
    $contrasena = $_POST["contrasena"];

    $stmt = $conn->prepare("SELECT * FROM empleados WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        if ($usuario["contrasena"] === $contrasena) {
            // Redirigir según el rol
            if ($usuario["rol"] === "administrador") {
                header("Location: template.php");
                exit;
            } elseif ($usuario["rol"] === "cajero") {
                header("Location: cajero.php");
                exit;
            } else {
                $mensaje = "Rol desconocido.";
            }
        } else {
            $mensaje = "Contraseña incorrecta.";
        }
    } else {
        $mensaje = "Correo no encontrado.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Iniciar Sesión</title>
    <style>
        body { font-family: Arial; background-color: #f0f0f0; padding: 40px; }
        .form-container {
            background: #fff;
            padding: 20px 30px;
            border-radius: 10px;
            width: 300px;
            margin: auto;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; }
        label, input { width: 100%; margin-top: 10px; }
        input[type="submit"] {
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
        }
        .error { color: red; margin-top: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Iniciar Sesión</h2>
        <?php if ($mensaje): ?>
            <div class="error"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <form method="POST">
            <label>Correo:</label>
            <input type="email" name="correo" required>
            <label>Contraseña:</label>
            <input type="password" name="contrasena" required>
            <input type="submit" value="Ingresar">
        </form>
    </div>
</body>
</html>
