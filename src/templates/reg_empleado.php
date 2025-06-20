<?php
require 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empleados</title>
    <link rel="stylesheet" href="../static/style.css">
    <style>
        body {
    background-color: #fff0f5;
    font-family: 'Segoe UI', sans-serif;
    color: #333;
    margin: 0;
    padding: 0;
}

.form-container {
    max-width: 600px;
    margin: 50px auto;
    padding: 30px;
    background-color: #ffffff;
    border: 2px solid #f8c0d8;
    border-radius: 12px;
    box-shadow: 0 0 12px rgba(255, 182, 193, 0.3);
}

h2 {
    text-align: center;
    color: #cc2b5e;
    margin-bottom: 25px;
    font-weight: bold;
    font-size: 24px;
}

label {
    display: block;
    margin-bottom: 6px;
    color: #b03a67;
    font-weight: 500;
}

input, select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #e8a5b9;
    border-radius: 6px;
    background-color: #fffafc;
    color: #333;
    font-size: 15px;
}

input:focus, select:focus {
    outline: none;
    border-color: #ff7ba9;
    box-shadow: 0 0 4px #ffb6c1;
}

input[type="submit"] {
    background-color: #ff6f91;
    color: white;
    border: none;
    font-weight: bold;
    font-size: 16px;
    padding: 12px;
    border-radius: 6px;
    transition: background 0.3s ease;
}

input[type="submit"]:hover {
    background-color: #e05578;
}

a {
    display: inline-block;
    margin-top: 15px;
    color: #b03a67;
    text-decoration: none;
    font-weight: 500;
}

a:hover {
    text-decoration: underline;
}

@media (max-width: 600px) {
    .form-container {
        margin: 20px;
        padding: 20px;
    }
}

    </style>
</head>
<body>
    <div class="form-container">
        <h2>Registro de Empleados</h2>
        <form id="Registroformg" action="template.php?page=reg_empleado1" method="POST"><!--//aquí había un error que era reg_empleado1.php(es reg_empleado1 nada mas ya que así está la ruta en template.php y ya tiene el php $ruta_templates = "src/templates/{$page}.php";)-->
            <label for="cedula">Cédula:</label>
            <input type="number" id="cedula" name="cedula" required>

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required>

            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <label for="rol">Rol:</label>
            <select name="rol" id="rol" required>
                <option value="">Selecciona un rol</option>
                <option value="administrador">Administrador</option>
                <option value="cajero">Cajero</option>
            </select>
          
            <input type="submit" value="Registrar empleado">
        </form>
        <a href="template.php?page=listar_empleado.php">Ver lista de empleados</a>
    </div>
</body>
</html>


