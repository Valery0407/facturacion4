<?php
session_start();
$conexion = new mysqli("localhost", "root", "", "facturacion");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$facturas_result = $conexion->query("SELECT COUNT(*) AS total FROM factura WHERE estado = 'valida'");
$facturas = $facturas_result->fetch_assoc()['total'];

$mes_actual = date('m');
$anio_actual = date('Y');
$ventas_result = $conexion->query("
    SELECT SUM(monto) AS total_ventas 
    FROM factura 
    WHERE MONTH(fecha_factura) = $mes_actual AND YEAR(fecha_factura) = $anio_actual AND estado = 'valida'
");
$ventas = $ventas_result->fetch_assoc()['total_ventas'] ?? 0;

$gastos = $ventas * 0.80;
$ganancia = $ventas - $gastos;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }

        .sidebar {
            width: 220px;
            background: #2c3e50;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 0;
            color: white;
            z-index: 1000;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 22px;
        }

        .sidebar a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .topbar {
            height: 60px;
            background: #007bff;
            color: white;
            position: fixed;
            top: 0;
            left: 220px;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 999;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .topbar .title {
            font-size: 20px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar .user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .topbar .user i {
            font-size: 18px;
        }

        .main {
            margin-left: 220px;
            padding: 100px 30px 30px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            color: #333;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card .info h3 {
            margin-bottom: 5px;
            font-size: 18px;
        }

        .card .info p {
            font-size: 24px;
            font-weight: bold;
        }

        .card .icon {
            font-size: 32px;
            opacity: 0.3;
        }

        .blue { border-left: 5px solid #3498db; }
        .green { border-left: 5px solid #2ecc71; }
        .red { border-left: 5px solid #e74c3c; }
        .orange { border-left: 5px solid #f39c12; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Mi Facturación</h2>
    <a href="#"><i class="fas fa-home"></i> Dashboard</a>
    <a href="listar_empleado.php"><i class="fas fa-users"></i> Empleados</a>
    <a href="registrar_p.php"><i class="fas fa-box"></i> Productos</a>
    <a href="#"><i class="fas fa-receipt"></i> Facturas</a>
    <a href="#"><i class="fas fa-chart-line"></i> Reportes</a>
    <a href="#"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
</div>

<div class="topbar">
    <div class="title"><i class="fas fa-chart-pie"></i><h2> Bienvenido,Administrador </h2></div>
    <div class="user">
        <i class="fas fa-user-circle"></i>
        <span>Administrador</span>
    </div>
</div>

<div class="main">
    <div class="cards">
        <div class="card blue">
            <div class="info">
                <h3>Total Facturas</h3>
                <p><?php echo $facturas; ?></p>
            </div>
            <div class="icon"><i class="fas fa-file-invoice"></i></div>
        </div>
        <div class="card green">
            <div class="info">
                <h3>Ventas del Mes</h3>
                <p>$<?php echo number_format($ventas, 0, ',', '.'); ?></p>
            </div>
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
        </div>
        <div class="card red">
            <div class="info">
                <h3>Gastos del Mes</h3>
                <p>$<?php echo number_format($gastos, 0, ',', '.'); ?></p>
            </div>
            <div class="icon"><i class="fas fa-coins"></i></div>
        </div>
        <div class="card orange">
            <div class="info">
                <h3>Ganancia Neta</h3>
                <p>$<?php echo number_format($ganancia, 0, ',', '.'); ?></p>
            </div>
            <div class="icon"><i class="fas fa-chart-bar"></i></div>
        </div>
    </div>
</div>

</body>
</html>
