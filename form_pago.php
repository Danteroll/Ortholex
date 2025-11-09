<?php
include("conexion.php");

// Registrar nuevo pago
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $paciente = $_POST['paciente'];
    $tratamiento = $_POST['tratamiento'];
    $monto = $_POST['monto'];
    $fecha_pago = $_POST['fecha_pago'];
    $metodo = $_POST['metodo'];

    $stmt = $conexion->prepare("INSERT INTO pagos (paciente, tratamiento, monto, fecha_pago, metodo, fecha_registro)
                                VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssdss", $paciente, $tratamiento, $monto, $fecha_pago, $metodo);

    if ($stmt->execute()) {
        echo "<script>alert('Pago registrado correctamente.'); window.location='inicio.php?page=pago';</script>";
    } else {
        echo "<script>alert('Error al registrar el pago.');</script>";
    }
    $stmt->close();
}

// Consultar pagos existentes
$res = $conexion->query("SELECT * FROM pagos ORDER BY fecha_pago DESC");
?>

<div class="inventario-container">
  <div class="inventario-header">
    <h2>Gestión de Pagos</h2>
    <button class="btn-modificar" onclick="togglePago()">Nuevo pago</button>
  </div>

  <!-- 💳 Formulario de registro de pago -->
  <div class="form-box" id="nuevoPago" style="display:none;">
    <form method="POST">
      <h3>Registrar nuevo pago</h3>

      <div class="input-group">
        <label for="paciente">Nombre del paciente:</label>
        <input type="text" id="paciente" name="paciente" required>
      </div>

      <div class="input-group">
        <label for="tratamiento">Tratamiento:</label>
        <input type="text" id="tratamiento" name="tratamiento" required>
      </div>

      <div class="input-group">
        <label for="monto">Monto ($):</label>
        <input type="number" id="monto" name="monto" step="0.01" required>
      </div>

      <div class="input-group">
        <label for="fecha_pago">Fecha de pago:</label>
        <input type="date" id="fecha_pago" name="fecha_pago" required>
      </div>

      <div class="input-group">
        <label for="metodo">Método de pago:</label>
        <select id="metodo" name="metodo" required>
          <option value="">Seleccione...</option>
          <option value="Efectivo">Efectivo</option>
          <option value="Tarjeta">Tarjeta</option>
          <option value="Transferencia">Transferencia</option>
        </select>
      </div>

      <div class="buttons">
        <button type="submit" class="btn-guardar">Guardar</button>
        <button type="button" class="btn-cancelar" onclick="togglePago()">Cancelar</button>
      </div>
    </form>
  </div>

  <!-- 📋 Tabla de pagos -->
  <div class="tabla-inventario">
    <table>
      <tr>
        <th>ID</th>
        <th>Paciente</th>
        <th>Tratamiento</th>
        <th>Monto</th>
        <th>Fecha de pago</th>
        <th>Método</th>
      </tr>

      <?php if ($res && $res->num_rows > 0): ?>
        <?php while ($row = $res->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['id_pago']; ?></td>
            <td><?php echo htmlspecialchars($row['paciente']); ?></td>
            <td><?php echo htmlspecialchars($row['tratamiento']); ?></td>
            <td>$<?php echo number_format($row['monto'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['fecha_pago']); ?></td>
            <td><?php echo htmlspecialchars($row['metodo']); ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="6" style="text-align:center;padding:20px;color:#555;">
            No hay pagos registrados.
          </td>
        </tr>
      <?php endif; ?>
    </table>
  </div>
</div>

<script>
function togglePago() {
  const form = document.getElementById('nuevoPago');
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
  appearance: none; /* quita la flecha predeterminada del sistema */
}

.input-group select:focus {
  border-color: #a16976;
  box-shadow: 0 0 4px rgba(161,105,118,0.4);
  outline: none;
}
</style>

<?php $conexion->close(); ?>
