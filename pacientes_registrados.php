<?php
include("conexion.php");

// 🗑️ Eliminar paciente seleccionado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_paciente'])) {
    $id = intval($_POST['paciente_id']);
    if ($id > 0) {
        $conexion->query("DELETE FROM pacientes WHERE id_paciente = $id");
        echo "<script>alert('Paciente eliminado correctamente.'); window.location='inicio.php';</script>";
        exit;
    } else {
        echo "<script>alert('Seleccione un paciente válido.');</script>";
    }
}

// 🔎 Obtener pacientes registrados
$res = $conexion->query("SELECT * FROM pacientes ORDER BY fecha_registro DESC");
?>

<div class="inventario-container">
  <div class="inventario-header">
    <h2>Pacientes</h2>
    <div style="display:flex;gap:10px;">
      <a href="form_paciente.php">
        <button class="btn-modificar">Nuevo paciente</button>
      </a>
      <button class="btn-eliminar" onclick="toggleEliminar()">Eliminar paciente</button>
    </div>
  </div>

  <!-- 🗑️ Formulario para eliminar paciente -->
  <div class="form-box" id="formEliminar" style="display:none;">
    <form method="POST">
      <h3>Eliminar paciente</h3>

      <div class="input-group">
        <label for="paciente_id">Seleccione el paciente:</label>
        <select id="paciente_id" name="paciente_id" required>
          <option value="">Seleccione...</option>
          <?php
          $pacientes = $conexion->query("SELECT id_paciente, nombre FROM pacientes ORDER BY nombre ASC");
          if ($pacientes && $pacientes->num_rows > 0):
            while ($p = $pacientes->fetch_assoc()):
          ?>
            <option value="<?php echo $p['id_paciente']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
          <?php endwhile; endif; ?>
        </select>
      </div>

      <div class="buttons">
        <button type="submit" name="eliminar_paciente" class="btn-guardar">Eliminar</button>
        <button type="button" class="btn-cancelar" onclick="toggleEliminar()">Cancelar</button>
      </div>
    </form>
  </div>

  <!-- 📋 Tabla de pacientes -->
  <div class="tabla-inventario">
    <table>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Edad</th>
        <th>Celular</th>
        <th>Profesión</th>
        <th>Expediente</th>
      </tr>

      <?php if ($res && $res->num_rows > 0): ?>
        <?php while ($row = $res->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['id_paciente']; ?></td>
            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
            <td><?php echo htmlspecialchars($row['edad']); ?></td>
            <td><?php echo htmlspecialchars($row['celular']); ?></td>
            <td><?php echo htmlspecialchars($row['profesion']); ?></td>
            <td>
              <a href="historial_paciente.php?id=<?php echo $row['id_paciente']; ?>">
                <button class="btn-modificar">Ver expediente</button>
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align:center;padding:20px;color:#555;">
            No se encontraron pacientes.
          </td>
        </tr>
      <?php endif; ?>
    </table>
  </div>
</div>

<script>
function toggleEliminar() {
  const form = document.getElementById('formEliminar');
  form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}
</script>

<style>
/* --- Asegura que el select se vea igual que los inputs del sistema Ortholex --- */
.input-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
  font-family: 'Segoe UI', sans-serif;
  color: #333;
  background-color: #fff;
  transition: border-color 0.3s, box-shadow 0.3s;
  appearance: none;
}

.input-group select:focus {
  border-color: #a16976;
  box-shadow: 0 0 4px rgba(161,105,118,0.4);
  outline: none;
}
</style>

<?php
$conexion->close();
?>




