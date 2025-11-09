<?php
include("../conexion.php");

$id = intval($_GET['id']);
$consulta = $conexion->query("SELECT * FROM tratamientos WHERE id_tratamiento=$id");
if ($consulta->num_rows == 0) {
  die("Tratamiento no encontrado.");
}
$t = $consulta->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Tratamiento</title>
    <link rel="stylesheet" href="../css/form_paciente.css">
</head>
<body>
  <h2>Editar tratamiento</h2>

  <form action="actualizar_tratamiento.php" method="POST">
    <input type="hidden" name="id_tratamiento" value="<?= $t['id_tratamiento'] ?>">

    <label>Nombre:</label>
    <input type="text" name="nombre_tratamiento" value="<?= htmlspecialchars($t['nombre_tratamiento']) ?>" required><br>

    <label>Descripción:</label>
    <textarea name="descripcion"><?= htmlspecialchars($t['descripcion']) ?></textarea><br>

    <label>Costo:</label>
    <input type="number" step="0.01" name="costo" value="<?= $t['costo'] ?>" required><br>

    <button type="submit">Actualizar</button>
  </form>
</body>
</html>
