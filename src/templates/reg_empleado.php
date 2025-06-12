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
        .form-container {
            max-width: 500px;
            margin: 0 auto;
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
        .form-container a {
            display: inline-block;
            margin-top: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .form-container a:hover {
            text-decoration: underline;
        }
        @media (max-width: 400px) {
            .form-container {
                max-width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Registro de Empleados</h2>
        <form id="Registroformg" action="template.php?page=reg_empleado1.php" method="POST">
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


