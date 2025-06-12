<?php
// template.php (ubicado en facturacion4/template.php)
include 'src/templates/conexion.php';

// Recibimos el parámetro “page” por GET (por ejemplo: ?page=listar_empleado).
$page  = $_GET['page'] ?? 'dashboard';
$title = ucfirst($page) . " | Mi Facturación";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>

    <!-- FontAwesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- BLOQUE DE ESTILOS (copiado tal cual de tu admin.php original) -->
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }

        /* ===== Sidebar ===== */
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
            font-size: 1.5em;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            font-size: 1em;
            transition: background 0.3s;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .sidebar a:hover {
            background: #34495e;
        }

        /* ===== Header ===== */
        header {
            margin-left: 220px;
            height: 60px;
            background: #2980b9;
            display: flex;
            align-items: center;
            padding: 0 20px;
            color: white;
        }
        header h1 {
            font-size: 1.4em;
        }
        header .usuario {
            margin-left: auto;
        }

        /* ===== Contenido Principal ===== */
        main {
            margin-left: 220px;
            margin-top: 60px;
            padding: 20px;
        }

        /* ===== Cards ===== */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .card .info h3 {
            font-size: 1em;
            color: #777;
            margin-bottom: 5px;
        }
        .card .info p {
            font-size: 2em;
            font-weight: bold;
            color: #333;
        }
        .card .icon {
            font-size: 2.5em;
            color: #ccc;
        }
        .blue   { border-left: 5px solid #3498db; }
        .green  { border-left: 5px solid #2ecc71; }
        .red    { border-left: 5px solid #e74c3c; }
        .orange { border-left: 5px solid #f39c12; }
    </style>
</head>
<body>

    <!-- ===== Sidebar (menú lateral) ===== -->
    <div class="sidebar">
        <h2>Mi Facturación</h2>
        <a href="template.php?page=dashboard"><i class="fas fa-home"></i> Dashboard</a>
        <a href="template.php?page=listar_empleado"><i class="fas fa-users"></i> Empleados</a>
        <a href="template.php?page=registrar_p"><i class="fas fa-box"></i> Productos</a>
        <a href="template.php?page=listar_factura"><i class="fas fa-receipt"></i> Facturas</a>
        <a href="template.php?page=reportes"><i class="fas fa-chart-line"></i> Reportes</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>

    <!-- ===== Header (barra superior) ===== -->
    <header>
        <h1>Bienvenido, Administrador</h1>
        <div class="usuario">
            <i class="fas fa-user-circle"></i> Admin
        </div>
    </header>

    <!-- ===== Contenido Dinámico ===== -->
    <main>
        <?php
        // 1) Primero intentamos incluir desde src/includes/{$page}.php
        $ruta_includes  = "src/includes/{$page}.php";
        // 2) Si no existe allí, probamos src/templates/{$page}.php
        $ruta_templates = "src/templates/{$page}.php";

        if (file_exists($ruta_includes)) {
            include $ruta_includes;
        }
        elseif (file_exists($ruta_templates)) {
            include $ruta_templates;
        }
        else {
            echo "<div style='padding:20px;'><h2>Página no encontrada</h2></div>";
        }
        ?>
    </main>

</body>
</html>
