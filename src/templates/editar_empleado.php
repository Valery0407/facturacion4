<?php
require 'conexion.php';

$mensaje = "";
$empleado = null;

// Verificar si se recibió la cédula
if (isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];

    // Obtener los datos del empleado
    $sql = "SELECT * FROM empleados WHERE cedula = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $empleado = $resultado->fetch_assoc();
    $stmt->close();

    if (!$empleado) {
        $mensaje = "❌ Empleado no encontrado.";
    }
}

// Procesar el formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cedula'], $_POST['nombre'], $_POST['correo'], $_POST['rol'])) {
    $cedula = trim($_POST['cedula']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $rol = trim($_POST['rol']);
    $contrasena = !empty($_POST['contrasena']) ? password_hash(trim($_POST['contrasena']), PASSWORD_DEFAULT) : null;

    // Validar formato de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "❌ Correo electrónico inválido.";
    } else {
        // Verificar si el nombre o correo ya existen (excluyendo el empleado actual)
        $sql_check = "SELECT 1 FROM empleados WHERE (nombre = ? OR correo = ?) AND cedula != ?";
        $stmt_check = $mysqli->prepare($sql_check);
        $stmt_check->bind_param("sss", $nombre, $correo, $cedula);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $mensaje = "❌ El nombre o correo ya están registrados.";
        } else {
            // Actualizar el empleado
            if ($contrasena) {
                $sql_update = "UPDATE empleados SET nombre = ?, correo = ?, contrasena = ?, rol = ? WHERE cedula = ?";
                $stmt_update = $mysqli->prepare($sql_update);
                $stmt_update->bind_param("sssss", $nombre, $correo, $contrasena, $rol, $cedula);
            } else {
                $sql_update = "UPDATE empleados SET nombre = ?, correo = ?, rol = ? WHERE cedula = ?";
                $stmt_update = $mysqli->prepare($sql_update);
                $stmt_update->bind_param("ssss", $nombre, $correo, $rol, $cedula);
            }

            if ($stmt_update->execute()) {
                $mensaje = "✅ ¡Empleado actualizado exitosamente!";
            } else {
                $mensaje = "❌ Error al actualizar: " . $stmt_update->error;
            }
            $stmt_update->close();
        }
        $stmt_check->close();
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 500px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container label {
            display: block;
            margin-bottom: 5px;
        }
        .form-container input, .form-container select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-container input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Editar Empleado</h2>
        <?php if ($mensaje): ?>
            <div class="alert alert-info"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <?php if ($empleado): ?>
            <form action="editar_empleado.php" method="POST">
                <input type="hidden" name="cedula" value="<?php echo htmlspecialchars($empleado['cedula']); ?>">
                
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($empleado['nombre']); ?>" required>

                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($empleado['correo']); ?>" required>

                <label for="contrasena">Nueva contraseña (dejar en blanco para no cambiar):</label>
                <input type="password" id="contrasena" name="contrasena">

                <label for="rol">Rol:</label>
                <select name="rol" id="rol" required>
                    <option value="administrador" <?php echo $empleado['rol'] == 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                    <option value="cajero" <?php echo $empleado['rol'] == 'cajero' ? 'selected' : ''; ?>>Cajero</option>
                </select>
                
                <input type="submit" value="Actualizar empleado">
            </form>
        <?php else: ?>
            <div class="alert alert-danger">No se encontró el empleado.</div>
        <?php endif; ?>
        <a href="listar_empleados.php" class="btn btn-secondary mt-3">Volver a la lista</a>
    </div>
</body>
</html>