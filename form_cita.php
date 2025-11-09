<?php
include("conexion.php");

// 🗑️ Eliminar todas las citas
if (isset($_GET['eliminar_todo'])) {
    $conexion->query("DELETE FROM citas");
    echo "<script>alert('Todas las citas fueron eliminadas correctamente.'); window.location='inicio.php?page=citas';</script>";
    exit;
}

// 🗑️ Eliminar cita específica
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_cita'])) {
    $id_cita = intval($_POST['cita_id']);
    if ($id_cita > 0) {
        $conexion->query("DELETE FROM citas WHERE id_cita = $id_cita");
        echo "<script>alert('Cita eliminada correctamente.'); window.location='inicio.php?page=citas';</script>";
        exit;
    } else {
        echo "<script>alert('Seleccione una cita válida.');</script>";
    }
}

// 📝 Registrar nueva cita
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_cita'])) {
    $id_paciente = intval($_POST['id_paciente']);
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $motivo = $_POST['motivo'];

    $stmt = $conexion->prepare("INSERT INTO citas (id_paciente, fecha, hora, motivo, estado) VALUES (?, ?, ?, ?, 'pendiente')");
    $stmt->bind_param("isss", $id_paciente, $fecha, $hora, $motivo);

    if ($stmt->execute()) {
        echo "<script>alert('Cita registrada correctamente.'); window.location='inicio.php?page=citas';</script>";
    } else {
        echo "<script>alert('Error al registrar la cita.');</script>";
    }
    $stmt->close();
}

?>

<div class="inventario-container">
  <div class="inventario-header">
    <h2>Gestión de Citas</h2>
    <div style="display:flex;gap:10px;">
      <a href="form_paciente.php"><button class="btn-modificar">Nuevo paciente</button></a>
      <button class="btn-modificar" onclick="toggleForm()">Nueva cita</button>
      <button class="btn-eliminar" onclick="toggleEliminar()">Eliminar cita</button>
      <button class="btn-eliminar" onclick="if(confirm('¿Deseas eliminar todas las citas?')) window.location='inicio.php?page=citas&eliminar_todo=true';">Eliminar todas</button>
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

      <div class="buttons">
        <button type="submit" name="registrar_cita" class="btn-guardar">Guardar</button>
        <button type="button" class="btn-cancelar" onclick="toggleForm()">Cancelar</button>
      </div>
    </form>
  </div>

  <!-- 🗑️ Formulario para eliminar cita -->
  <div class="form-box" id="formEliminar" style="display:none;">
    <form method="POST">
      <h3>Eliminar cita</h3>

      <div class="input-group">
        <label for="cita_id">Seleccione la cita:</label>
        <select id="cita_id" name="cita_id" required>
          <option value="">Seleccione...</option>
          <?php
          if (!empty($citas)):
            foreach ($citas as $c):
          ?>
            <option value="<?php echo $c['id']; ?>">
              <?php echo htmlspecialchars($c['paciente'] . " - " . $c['fecha'] . " " . $c['hora']); ?>
            </option>
          <?php endforeach; endif; ?>
        </select>
      </div>

      <div class="buttons">
        <button type="submit" name="eliminar_cita" class="btn-guardar">Eliminar</button>
        <button type="button" class="btn-cancelar" onclick="toggleEliminar()">Cancelar</button>
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
      </tr>

      <?php if (!empty($citas)): ?>
        <?php foreach ($citas as $row): ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['paciente']); ?></td>
            <td><?php echo htmlspecialchars($row['fecha']); ?></td>
            <td><?php echo htmlspecialchars($row['hora']); ?></td>
            <td><?php echo htmlspecialchars($row['motivo']); ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" style="text-align:center;padding:20px;color:#555;">
            No hay citas registradas.
          </td>
        </tr>
      <?php endif; ?>
    </table>
  </div>

  <!-- 📅 Calendario -->
  <div style="margin-top:40px;">
    <h3 style="text-align:center;color:#1d3557;">Calendario</h3>
    <div id="calendar" style="max-width:900px;margin:30px auto;background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.1);padding:15px;"></div>
  </div>
</div>

<!-- ✅ FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.js"></script>

<script>
function toggleForm() {
  const form = document.getElementById('nuevaCita');
  form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}

function toggleEliminar() {
  const form = document.getElementById('formEliminar');
  form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}

// Inicializar calendario con las citas
document.addEventListener('DOMContentLoaded', function() {
  const citas = <?php echo json_encode($citas); ?>;
  const calendarEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    height: 'auto',
    events: citas,
    eventColor: '#a16976',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    }
  });
  calendar.render();
});
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

<?php $conexion->close(); ?>

