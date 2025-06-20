<?php
require 'conexion.php'; // Asegúrate de que este archivo conecta a la base de datos correctamente

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cedula'], $_POST['nombre'], $_POST['correo'], $_POST['contrasena'], $_POST['rol'])) {
    
    $cedula = trim($_POST['cedula']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);
    $rol = trim($_POST['rol']);
    $mensaje = "";

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "❌ Correo electrónico inválido.";
    } else {
        // Verificar si ya existe el empleado
        $sql_check = "SELECT 1 FROM empleados WHERE cedula = ? OR nombre = ?";
        $stmt = $mysqli->prepare($sql_check); //aquí estaba $conn pero en conexion.php no se usaba eso, sino $mysql, por eso tuve que cambiarlo a mysql que es la conexión que se creó.
        $stmt->bind_param("ss", $cedula, $nombre);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = "❌ La cédula o el nombre ya existen.";
        } else {
            // Insertar el nuevo empleado
            $sql_insert = "INSERT INTO empleados (cedula, nombre, correo, contrasena, rol) VALUES (?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($sql_insert);//aquí estaba $conn pero en conexion.php no se usaba eso, sino $mysql, por eso tuve que cambiarlo a mysql que es la conexión que se creó.
            $stmt->bind_param("sssss", $cedula, $nombre, $correo, $contrasena, $rol);

            if ($stmt->execute()) {
                $mensaje = "✅ Empleado registrado exitosamente.";
            } else {
                $mensaje = "❌ Error al registrar el empleado: " . $conn->error;
            }
        }
        $stmt->close();
    }

    echo "<script>alert('$mensaje'); window.location.href='template.php?page=listar_empleado';</script>";//Cambié page=empleados por page=listar_empleado, ya que así está en template.php y así se llama la página de listar empleados.
}
?>
