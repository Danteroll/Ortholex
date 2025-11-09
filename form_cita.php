<?php
include("conexion.php");

// Obtener lista de pacientes
$pacientes = $conexion->query("SELECT id_paciente, nombre FROM pacientes ORDER BY nombre");

// Obtener lista de tratamientos
$tratamientos = $conexion->query("SELECT id_tratamiento, nombre_tratamiento FROM tratamientos ORDER BY nombre_tratamiento");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Cita</title>
    <link rel="stylesheet" href="css/form_paciente.css">

</head>
<body>
  <h2>Registrar Cita</h2>
  <form method="POST" action="funciones/guardar_cita.php">
    <label>Paciente:</label>
    <select name="id_paciente" required>
      <option value="">Seleccionar paciente</option>
      <?php while($p = $pacientes->fetch_assoc()) { ?>
        <option value="<?= $p['id_paciente'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
      <?php } ?>
    </select><br>

    <label>Tratamiento (opcional):</label>
    <select name="id_tratamiento">
      <option value="">-- Ninguno --</option>
      <?php while($t = $tratamientos->fetch_assoc()) { ?>
        <option value="<?= $t['id_tratamiento'] ?>"><?= htmlspecialchars($t['nombre_tratamiento']) ?></option>
      <?php } ?>
    </select><br>

    <label>Fecha:</label>
    <input type="date" name="fecha" required><br>

    <label>Hora:</label>
    <input type="time" name="hora" required><br>

    <label>Motivo:</label>
    <textarea name="motivo"></textarea><br>

    <label>Estado:</label>
    <select name="estado">
      <option value="pendiente">Pendiente</option>
      <option value="realizada">Realizada</option>
      <option value="cancelada">Cancelada</option>
    </select><br>

    <button type="submit">Guardar cita</button>
  </form>
</body>
</html>
