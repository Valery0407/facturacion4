<?php
	require 'conexion.php';
	
	$where = "";
	
	if(!empty($_POST))
	{
		$valor = $_POST['campo'];
		if(!empty($valor)){
			$where = "WHERE nombre LIKE '%$valor'";
		}
	}
	$sql = "SELECT * FROM categoria $where";
	$resultado = $mysqli->query($sql);
	
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="../static/style.css">
	<style>
		/* Estilos generales */

/* 📌 Hacerlo más responsivo en pantallas pequeñas */
@media (max-width: 400px) {
    .formg-container {
        max-width: 90%;
    }
}
	</style>
</head>
<body>
    <div class="form-container">
        <h2>Categoría</h2>
        <form id="Registroformg" action="reg_categoria1.php" method="POST">

            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="descripcion">Descripción:</label>
            <input type="text" id="descripcion" name="descripcion" required>


            <input type="submit" value="Crear Categoría">
        </form>
    </div>
</body>
</html>