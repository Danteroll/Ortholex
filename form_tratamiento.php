<?php
include("conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Tratamiento</title>
  <link rel="stylesheet" href="css/form_paciente.css">
</head>
<body>
  <h2>Registrar nuevo tratamiento</h2>

  <form action="funciones/guardar_tratamiento.php" method="POST">
    <label>Nombre del tratamiento:</label>
    <input type="text" name="nombre_tratamiento" required><br>

    <label>Descripción:</label>
    <textarea name="descripcion" rows="3"></textarea><br>

    <label>Costo:</label>
    <input type="number" step="0.01" name="costo" required><br>

    <button type="submit">Guardar tratamiento</button>
  </form>
</body>
</html>
