<?php
// dashboard.php (en facturacion4/src/includes/dashboard.php)
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
    WHERE MONTH(fecha_factura) = $mes_actual 
      AND YEAR(fecha_factura) = $anio_actual 
      AND estado = 'valida'
");
$ventas = $ventas_result->fetch_assoc()['total_ventas'] ?? 0;

$gastos = $ventas * 0.80;
$ganancia = $ventas - $gastos;
?>

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
