<?php 
include("conexion.php");

// 🗑️ Eliminar todas las citas
if (isset($_GET['eliminar_todo'])) {
    $conexion->query("DELETE FROM citas");
    echo "<script>alert('Todas las citas fueron eliminadas correctamente.'); window.location='form_cita.php';</script>";
    exit;
}

// 🗑️ Eliminar cita específica
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_cita'])) {
    $id_cita = intval($_POST['cita_id']);
    if ($id_cita > 0) {
        $conexion->query("DELETE FROM citas WHERE id_cita = $id_cita");
        echo "<script>alert('Cita eliminada correctamente.'); window.location='form_cita.php';</script>";
        exit;
    } else {
        echo "<script>alert('Seleccione una cita válida.');</script>";
    }
}

// 📝 Registrar nueva cita
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_cita'])) {
    $id_paciente = intval($_POST['id_paciente']);
    $id_tratamiento = !empty($_POST['id_tratamiento']) ? intval($_POST['id_tratamiento']) : null;
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $estado = $_POST['estado'] ?? 'pendiente';

    $stmt = $conexion->prepare("
        INSERT INTO citas (id_paciente, id_tratamiento, fecha, hora, estado)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iisss", $id_paciente, $id_tratamiento, $fecha, $hora, $estado);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Cita registrada correctamente.'); window.location='form_cita.php';</script>";
    exit;
}

// 📋 Obtener datos
$pacientes = $conexion->query("SELECT id_paciente, nombre FROM pacientes ORDER BY nombre");
$tratamientos = $conexion->query("SELECT id_tratamiento, nombre_tratamiento FROM tratamientos ORDER BY nombre_tratamiento");

// 📋 Obtener citas registradas
$res = $conexion->query("
    SELECT c.id_cita, p.nombre AS paciente, t.nombre_tratamiento AS tratamiento,
           c.fecha, c.hora, c.estado
    FROM citas c
    LEFT JOIN pacientes p ON c.id_paciente = p.id_paciente
    LEFT JOIN tratamientos t ON c.id_tratamiento = t.id_tratamiento
    ORDER BY c.fecha DESC, c.hora ASC
");

// 📅 Preparar eventos del calendario
$citas = [];
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $hora_final = date('H:i:s', strtotime($row['hora'] . ' +1 hour'));
        $color = match($row['estado']) {
            'realizada' => '#22c55e',
            'cancelada' => '#ef4444',
            default => '#a16976',
        };
        $citas[] = [
            "id" => $row['id_cita'],
            "title" => $row['paciente'] . " — " . ($row['tratamiento'] ?? 'N/A'),
            "start" => $row['fecha'] . "T" . $row['hora'],
            "end" => $row['fecha'] . "T" . $hora_final,
            "color" => $color
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestión de Citas</title>
<link rel="stylesheet" href="css/inicio.css">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales-all.global.min.js"></script>

<style>
/* ===== Campos del formulario ===== */
.form-box form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}
.input-group {
  display: flex;
  flex-direction: column;
}
.input-group label {
  font-weight: 600;
  color: #1d3557;
  margin-bottom: 6px;
}
.input-group input[type="date"],
.input-group input[type="time"],
.input-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccd;
  border-radius: 8px;
  background: #fff;
  font-size: 15px;
  transition: all 0.2s ease;
}
.input-group input:focus,
.input-group select:focus {
  outline: none;
  border-color: #a16976;
  box-shadow: 0 0 4px rgba(161, 105, 118, 0.4);
}

/* ===== Botones estilo Ortholex ===== */
.buttons {
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}
.btn-guardar,
.btn-cancelar {
  background-color: #a16976;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 8px 16px;
  font-size: 15px;
  font-weight: bold;
  cursor: pointer;
  font-family: 'Segoe UI', sans-serif;
  transition: 0.25s;
}
.btn-guardar:hover,
.btn-cancelar:hover {
  background-color: #814c59;
  transform: scale(1.03);
}

/* ===== Estilo del calendario ===== */
.fc-toolbar-title {
  color: #1d3557 !important;
  font-weight: bold;
}
.fc-button-primary {
  background-color: #a16976 !important;
  border: none !important;
  border-radius: 8px !important;
  font-weight: bold;
  text-transform: capitalize;
}
.fc-button-primary:hover {
  background-color: #814c59 !important;
}
.fc-daygrid-day-number {
  color: #1d3557 !important;
  font-weight: 500;
}
.fc-today {
  background-color: rgba(161,105,118,0.15) !important;
}
.fc {
  background: #fff !important;
  border-radius: 12px !important;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  padding: 15px;
}
</style>
</head>
<body>

<!-- Barra superior -->
<div class="topbar">
  <img src="imagenes/logo" alt="Logo" class="topbar-logo">
</div>

<!-- Sidebar -->
<div class="sidebar">
  <ul class="menu">
    <li><a href="form_cita.php" class="active">Citas</a></li>
    <li><a href="pacientes.php">Pacientes</a></li>
    <li><a href="form_expediente.php">Expedientes</a></li>
    <li><a href="form_inventario.php">Inventario</a></li>
    <li><a href="form_pago.php">Pagos</a></li>
    <li><a href="tratamientos.php">Tratamientos</a></li>
    <li><a href="index.php">Salir</a></li>
  </ul>
</div>

<!-- Contenido principal -->
<div class="main">
  <div class="inventario-container">
    <div class="inventario-header">
      <h2>Gestión de Citas</h2>
      <div style="display:flex;gap:10px;">
        <a href="form_paciente.php"><button class="btn-modificar">Nuevo paciente</button></a>
        <button class="btn-modificar" onclick="toggleForm()">Nueva cita</button>
        <button class="btn-eliminar" onclick="toggleEliminar()">Eliminar cita</button>
        <button class="btn-eliminar" onclick="if(confirm('¿Deseas eliminar todas las citas?')) window.location='form_cita.php?eliminar_todo=true';">Eliminar todas</button>
      </div>
    </div>

    <!-- 🗓️ Formulario nueva cita -->
    <div class="form-box" id="nuevaCita" style="display:none;">
      <form method="POST">
        <h3 style="color:#1d3557;">Registrar nueva cita</h3>

        <div class="input-group">
          <label>Paciente:</label>
          <select name="id_paciente" required>
            <option value="">Seleccione...</option>
            <?php while($p = $pacientes->fetch_assoc()) { ?>
              <option value="<?= $p['id_paciente'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="input-group">
          <label>Tratamiento:</label>
          <select name="id_tratamiento">
            <option value="">-- Ninguno --</option>
            <?php while($t = $tratamientos->fetch_assoc()) { ?>
              <option value="<?= $t['id_tratamiento'] ?>"><?= htmlspecialchars($t['nombre_tratamiento']) ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="input-group">
          <label>Fecha:</label>
          <input type="date" name="fecha" required>
        </div>

        <div class="input-group">
          <label>Hora:</label>
          <input type="time" name="hora" required>
        </div>

        <div class="input-group">
          <label>Estado:</label>
          <select name="estado">
            <option value="pendiente">Pendiente</option>
            <option value="realizada">Realizada</option>
            <option value="cancelada">Cancelada</option>
          </select>
        </div>

        <div class="buttons">
          <button type="submit" name="registrar_cita" class="btn-guardar">Guardar</button>
          <button type="button" class="btn-cancelar" onclick="cerrarFormCita()">Cancelar</button>
        </div>
      </form>
    </div>

    <!-- 🗑️ Formulario eliminar -->
    <div class="form-box" id="formEliminar" style="display:none;">
      <form method="POST">
        <h3 style="color:#1d3557;">Eliminar cita</h3>
        <div class="input-group">
          <label for="cita_id">Seleccione la cita:</label>
          <select id="cita_id" name="cita_id" required>
            <option value="">Seleccione...</option>
            <?php foreach ($citas as $c): ?>
              <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['title']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="buttons">
          <button type="submit" name="eliminar_cita" class="btn-guardar">Eliminar</button>
          <button type="button" class="btn-cancelar" onclick="cerrarFormEliminar()">Cancelar</button>
        </div>
      </form>
    </div>

    <!-- 📋 Tabla -->
    <div class="tabla-inventario">
      <table>
        <tr>
          <th>ID</th>
          <th>Paciente</th>
          <th>Tratamiento</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Estado</th>
        </tr>

        <?php if ($res && $res->num_rows > 0): ?>
          <?php $res->data_seek(0); while ($row = $res->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id_cita']; ?></td>
              <td><?= htmlspecialchars($row['paciente']); ?></td>
              <td><?= htmlspecialchars($row['tratamiento']); ?></td>
              <td><?= date('d/m/Y', strtotime($row['fecha'])); ?></td>
              <td><?= date('g:i A', strtotime($row['hora'])); ?></td>
              <td><?= ucfirst($row['estado']); ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6" style="text-align:center;padding:20px;color:#555;">No hay citas registradas.</td></tr>
        <?php endif; ?>
      </table>
    </div>

    <!-- 📅 Calendario Ortholex -->
    <div style="margin-top:40px;">
      <h3 style="text-align:center;color:#1d3557;">Calendario de Citas</h3>
      <div id="calendar"></div>
    </div>
  </div>
</div>

<script>
let citaAbierta = false;
let eliminarAbierta = false;

// === Mostrar / ocultar formularios ===
function toggleForm() {
  const form = document.getElementById('nuevaCita');
  if (!citaAbierta) {
    form.style.display = 'block';
    citaAbierta = true;
    eliminarAbierta = false;
    document.getElementById('formEliminar').style.display = 'none';
  }
}

function toggleEliminar() {
  const form = document.getElementById('formEliminar');
  if (!eliminarAbierta) {
    form.style.display = 'block';
    eliminarAbierta = true;
    citaAbierta = false;
    document.getElementById('nuevaCita').style.display = 'none';
  }
}

// ✅ Botones cancelar
function cerrarFormCita() {
  document.getElementById('nuevaCita').style.display = 'none';
  citaAbierta = false;
}
function cerrarFormEliminar() {
  document.getElementById('formEliminar').style.display = 'none';
  eliminarAbierta = false;
}

// === Calendario Ortholex ===
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    height: 'auto',
    events: <?= json_encode($citas, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>,
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    buttonText: { today: 'Hoy', month: 'Mes', week: 'Semana', day: 'Día' },
    eventDidMount: function(info) {
      info.el.style.borderRadius = '6px';
      info.el.style.padding = '4px 6px';
      info.el.style.fontSize = '13px';
      info.el.style.color = '#fff';
      info.el.style.background = `linear-gradient(135deg, ${info.event.backgroundColor}, #814c59)`;
      info.el.style.border = 'none';
    }
  });
  calendar.render();
});

// 🚫 Bloquear navegación con botones "Atrás" y "Adelante"
(function () {
  window.history.pushState(null, "", window.location.href);
  window.onpopstate = function () {
    window.history.pushState(null, "", window.location.href);
  };
})();
</script>

<?php $conexion->close(); ?>
</body>
</html>



