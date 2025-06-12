<?php
require 'conexion.php';


// Obtener todos los empleados
$sql = "SELECT * FROM factura";
$resultado = $mysqli->query($sql);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Facturas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../static/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #f0f8ff, #cceeff);
            font-family: 'Segoe UI', sans-serif;
            color: #003344;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-top: 40px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #007acc;
        }

        .btn-contenedor {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
        }

        .boton-registro {
            background: linear-gradient(135deg, #007acc, #00bfff);
            color: white;
            font-weight: bold;
            border: none;
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 14px;
        }

        .boton-registro:hover {
            background: linear-gradient(135deg, #005f99, #0099cc);
            transform: scale(1.05);
            color: white;
        }

        .table {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            width: 100%;
        }

        .table thead {
            background-color: #0099cc;
            color: white;
        }

        .table th, .table td {
            text-align: center;
            vertical-align: middle;
            padding: 10px;
            font-size: 14px;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 13px;
            border-radius: 8px;
            margin: 0 2px;
        }

        .btn-danger {
            background-color: #f44336;
            border: none;
        }

        .btn-danger:hover {
            background-color: #c62828;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Lista de Facturas</h2>
        <div class="btn-contenedor">
            <a href="template.php?page=factura2" class="boton-registro">Registrar nueva factura</a>
        </div>

        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID Factura</th>
                        <th>Fecha</th>
                        <th>ID Cliente</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id_factura']); ?></td>
                            <td><?php echo htmlspecialchars($row['fecha_factura']); ?></td>
                            <td><?php echo htmlspecialchars($row['id_cliente']); ?></td>
                            <td><?php echo htmlspecialchars($row['total']); ?></td>
                            <td>
                                <a href="eliminar_factura.php?id=<?php echo urlencode($row['id_factura']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta factura?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No hay facturas registradas.</div>
        <?php endif; ?>
    </div>
    <?php $mysqli->close(); ?>
</body>
</html>
