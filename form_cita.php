<?php
include("conexion.php");

// Registrar nueva cita
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $paciente = $_POST['paciente'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $motivo = $_POST['motivo'];
    $observaciones = $_POST['observaciones'];

    $stmt = $conexion->prepare("INSERT INTO citas (paciente, fecha, hora, motivo, observaciones, fecha_registro)
                                VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $paciente, $fecha, $hora, $motivo, $observaciones);

    if ($stmt->execute()) {
        echo "<script>alert('Cita registrada correctamente.'); window.location='inicio.php?page=citas';</script>";
    } else {
        echo "<script>alert('Error al registrar la cita.');</script>";
    }
    $stmt->close();
}

// Obtener citas registradas
$res = $conexion->query("SELECT * FROM citas ORDER BY fecha DESC, hora ASC");
?>

<div class="inventario-container">
  <div class="inventario-header">
    <h2>Gestión de Citas</h2>
    <div style="display:flex;gap:10px;">
      <a href="form_paciente.php"><button class="btn-modificar">Nuevo paciente</button></a>
      <button class="btn-modificar" onclick="document.getElementById('nuevaCita').style.display='block'">Nueva cita</button>
    </div>
  </div>

  <!-- 🗓️ Formulario de nueva cita -->
  <div class="form-box" id="nuevaCita" style="display:none;">
    <form method="POST">
      <h3>Registrar nueva cita</h3>
      <div class="input-group">
        <label for="paciente">Nombre del paciente:</label>
        <input type="text" id="paciente" name="paciente" required>
      </div>

      <div class="input-group">
        <label for="fecha">Fecha de la cita:</label>
        <input type="date" id="fecha" name="fecha" required>
      </div>

      <div class="input-group">
        <label for="hora">Hora:</label>
        <input type="time" id="hora" name="hora" required>
      </div>

      <div class="input-group">
        <label for="motivo">Motivo:</label>
        <input type="text" id="motivo" name="motivo" required>
      </div>

      <div class="input-group">
        <label for="observaciones">Observaciones:</label>
        <textarea id="observaciones" name="observaciones" rows="3"></textarea>
      </div>

      <div class="buttons">
        <button type="submit" class="btn-guardar">Guardar</button>
        <button type="button" class="btn-cancelar" onclick="document.getElementById('nuevaCita').style.display='none'">Cancelar</button>
      </div>
    </form>
  </div>

  <!-- 📋 Tabla de citas registradas -->
  <div class="tabla-inventario">
    <table>
      <tr>
        <th>ID</th>
        <th>Paciente</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Motivo</th>
        <th>Observaciones</th>
      </tr>

      <?php if ($res && $res->num_rows > 0): ?>
        <?php while ($row = $res->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['id_cita']; ?></td>
            <td><?php echo $row['paciente']; ?></td>
            <td><?php echo $row['fecha']; ?></td>
            <td><?php echo $row['hora']; ?></td>
            <td><?php echo $row['motivo']; ?></td>
            <td><?php echo $row['observaciones']; ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align:center;padding:20px;color:#555;">
            No hay citas registradas.
          </td>
        </tr>
      <?php endif; ?>
    </table>
  </div>
</div>

<script>
// Mostrar/ocultar formulario de nueva cita
function toggleForm() {
  const form = document.getElementById('nuevaCita');
  form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}
</script>

<?php $conexion->close(); ?>

