<?php
require 'conexion.php';

// Obtener todos los empleados
$sql = "SELECT * FROM empleados";
$resultado = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Empleados</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General */
/* General */
body {
    background: linear-gradient(to bottom, #ffeef5, #ffd6ea);
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    padding: 0;
    color: #4a004e;
}

.container {
    margin-top: 10px;
    background-color: #fff0f6;
    padding: 30px;
    padding-top: 10px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(255, 182, 193, 0.4);
}

/* Título */
.h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #d63384;
}

/* Contenedor del botón */
.btn-contenedor {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 25px;
}

/* Botón de registro */
.boton-registro {
    background: linear-gradient(135deg, #e83e8c, #ff69b4);
    color: white;
    font-weight: bold;
    border: none;
    padding: 10px 20px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, background 0.3s ease;
    text-decoration: none;
    font-size: 14px;
}

.boton-registro:hover {
    background: linear-gradient(135deg, #d63384, #ff5ca2);
    transform: scale(1.05);
    color: white;
}

/* Tabla */
.table {
    background-color: #fff;
    border-radius: 10px;
    overflow: hidden;
    width: 100%;
}

.table thead {
    background-color: #ffb6c1;
    color: white;
}

.table th, .table td {
    text-align: center;
    vertical-align: middle;
    padding: 10px;
    font-size: 14px;
}

/* Botones de acción */
.btn-sm {
    padding: 5px 10px;
    font-size: 13px;
    border-radius: 8px;
    margin: 0 2px;
}

.btn-warning {
    background-color: #f8bbd0;
    color: #4a004e;
    border: none;
}

.btn-warning:hover {
    background-color: #f48fb1;
}

.btn-danger {
    background-color: #ff1744;
    border: none;
}

.btn-danger:hover {
    background-color: #d50000;
}

    </style>
</head>
<body>
    <div class="container">
        <h2 class= h2 >Lista de Empleados</h2>
        <div class="btn-contenedor">
    <a href="template.php?page=reg_empleado" class=boton-registro>Registrar nuevo empleado</a>
</div>

        <?php if ($resultado->num_rows > 0): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['cedula']); ?></td>
                            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($row['correo']); ?></td>
                            <td><?php echo htmlspecialchars($row['rol']); ?></td>
                            <td>
                                
                                <a href="template.php?page=editar_empleado&amp;cedula=<?php echo urlencode($row['cedula']); ?>"class="btn btn-warning btn-sm">Editar</a>
                                <a href="template.php?page=eliminar_empleado&cedula=<?php echo urlencode($row['cedula']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este empleado?');">Eliminar</a> <!--cedula estaba escrito dos veces, "eliminar_empleado&cedula=cedula", por eso salía que no encontraba la cedula o había error allí-->
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No hay empleados registrados.</div>
        <?php endif; ?>
    </div>
    <?php $mysqli->close(); ?>
</body>
</html>