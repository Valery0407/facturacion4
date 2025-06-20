<?php
require 'conexion.php';

$mensaje = "";

if (isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];

    // Verificar si el empleado existe
    $sql_check = "SELECT 1 FROM empleados WHERE cedula = ?";
    $stmt_check = $mysqli->prepare($sql_check);
    $stmt_check->bind_param("s", $cedula);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // Eliminar el empleado
        $sql_delete = "DELETE FROM empleados WHERE cedula = ?";
        $stmt_delete = $mysqli->prepare($sql_delete);
        $stmt_delete->bind_param("s", $cedula);

        if ($stmt_delete->execute()) {
            $mensaje = "✅ ¡Empleado eliminado exitosamente!";
        } else {
            $mensaje = "❌ Error al eliminar: " . $stmt_delete->error;
        }
        $stmt_delete->close();
    } else {
        $mensaje = "❌ Empleado no encontrado.";
    }
    $stmt_check->close();
} else {
    $mensaje = "❌ No se proporcionó una cédula válida.";
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Empleado</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row" style="text-align:center; margin-top:20px;">
            <h3><?php echo $mensaje; ?></h3>
            <a href="template.php?page=listar_empleado" class="btn btn-primary">Volver a la lista</a><!--Cambié la ruta esa mal de listar_empleados.php-->
        </div>
    </div>
</body>
</html>