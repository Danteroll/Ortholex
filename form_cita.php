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

// ✏️ Actualizar hora o estado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar_cita'])) {
    $id_cita = intval($_POST['id_cita']);
    $hora = $_POST['hora'];
    $estado = $_POST['estado'];
    $fecha = $_POST['fecha'];
    $stmt = $conexion->prepare("UPDATE citas SET fecha=?, hora=?, estado=? WHERE id_cita=?");
    $stmt->bind_param("sssi", $fecha, $hora, $estado, $id_cita);
    $stmt->execute();
    $stmt->close();
    echo "<script>alert('Cita actualizada correctamente.'); window.location='form_cita.php';</script>";
    exit;
}

// 📋 Obtener datos para selects
$pacientes = $conexion->query("SELECT id_paciente, nombre FROM pacientes ORDER BY nombre");
$tratamientos = $conexion->query("SELECT id_tratamiento, nombre_tratamiento, costo FROM tratamientos ORDER BY nombre_tratamiento");

// 📋 Obtener citas registradas
$res = $conexion->query("
    SELECT c.id_cita, p.nombre AS paciente, t.nombre_tratamiento AS tratamiento, t.costo,
           c.fecha, c.hora, c.estado
    FROM citas c
    LEFT JOIN pacientes p ON c.id_paciente = p.id_paciente
    LEFT JOIN tratamientos t ON c.id_tratamiento = t.id_tratamiento
    ORDER BY c.fecha DESC, c.hora ASC
");

$citas = [];
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        // Calcular hora final (+1 hora por defecto)
$hora_final = date('H:i:s', strtotime($row['hora'] . ' +1 hour'));

$citas[] = [
    "id" => $row['id_cita'],
    "paciente" => $row['paciente'],
    "tratamiento" => $row['tratamiento'] ?? 'N/A',
    "precio" => $row['costo'] ?? 0,
    "fecha" => $row['fecha'],
    "hora" => $row['hora'],
    "estado" => $row['estado'] ?? 'pendiente',
    "title" => $row['paciente'] . " - " . ($row['tratamiento'] ?? ''),
    "start" => $row['fecha'] . "T" . $row['hora'],
    "end"   => $row['fecha'] . "T" . $hora_final   // ✅ duración de 1 hora
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

<!-- ✅ Scripts FullCalendar (versión global + locales en español) -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales-all.global.min.js"></script>

</head>
<body>

<!-- Barra superior -->
<div class="topbar">
  <img src="imagenes/logo" alt="Logo" class="topbar-logo">
</div>

<!-- Sidebar -->
  <div class="sidebar">
    <ul class="menu">
      <li><a href="form_cita.php">Citas</a></li>
      <li><a href="pacientes.php" class="active">Pacientes</a></li>
      <li><a href="form_expediente.php">Expedientes</a></li>
      <li><a href="form_inventario.php">Inventario</a></li>
      <li><a href="form_pago.php">Pagos</a></li>
      <li><a href="tratamientos.php">Tratamientos</a></li>
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
        <h3>Registrar nueva cita</h3>

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
              <option value="<?= $t['id_tratamiento'] ?>">
                <?= htmlspecialchars($t['nombre_tratamiento']) ?> — $<?= number_format($t['costo'], 2) ?>
              </option>
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
          <button type="button" class="btn-cancelar" onclick="toggleForm()">Cancelar</button>
        </div>
      </form>
    </div>

    <!-- 🗑️ Formulario eliminar -->
    <div class="form-box" id="formEliminar" style="display:none;">
      <form method="POST">
        <h3>Eliminar cita</h3>
        <div class="input-group">
          <label for="cita_id">Seleccione la cita:</label>
          <select id="cita_id" name="cita_id" required>
            <option value="">Seleccione...</option>
            <?php foreach ($citas as $c): ?>
              <option value="<?= $c['id']; ?>">
                <?= htmlspecialchars($c['paciente']." - ".$c['fecha']." ".$c['hora']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="buttons">
          <button type="submit" name="eliminar_cita" class="btn-guardar">Eliminar</button>
          <button type="button" class="btn-cancelar" onclick="toggleEliminar()">Cancelar</button>
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
          <th>Precio</th>
          <th>Fecha</th>
          <th>Hora</th>
          <th>Estado</th>
          <th>Acción</th>
        </tr>
        <?php if (!empty($citas)): ?>
          <?php foreach ($citas as $row): ?>
            <tr>
              <form method="POST">
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['paciente']); ?></td>
                <td><?= htmlspecialchars($row['tratamiento']); ?></td>
                <td>$<?= number_format($row['precio'],2); ?></td>
                <td>
                  <input type="date" name="fecha" value="<?= htmlspecialchars($row['fecha']); ?>" required>
                </td>
                <td>
                  <input type="time" name="hora" value="<?= htmlspecialchars($row['hora']); ?>" required>
                </td>
                <td>
                  <select name="estado">
                    <option value="pendiente" <?= $row['estado']=='pendiente'?'selected':''; ?>>Pendiente</option>
                    <option value="realizada" <?= $row['estado']=='realizada'?'selected':''; ?>>Realizada</option>
                    <option value="cancelada" <?= $row['estado']=='cancelada'?'selected':''; ?>>Cancelada</option>
                  </select>
                </td>
                <td>
                  <input type="hidden" name="id_cita" value="<?= $row['id']; ?>">
                  <button type="submit" name="actualizar_cita" class="btn-modificar">💾 Guardar</button>
                </td>
              </form>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="8" style="text-align:center;padding:20px;">No hay citas registradas.</td></tr>
        <?php endif; ?>
      </table>
    </div>

    <!-- 📅 Calendario -->
    <div style="margin-top:40px;">
      <h3 style="text-align:center;color:#1d3557;">Calendario</h3>
      <div id="calendar" style="max-width:900px;margin:30px auto;background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.1);padding:15px;"></div>
    </div>
  </div>
</div>

<script>
function toggleForm() {
  const form = document.getElementById('nuevaCita');
  form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}
function toggleEliminar() {
  const form = document.getElementById('formEliminar');
  form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
}

// Calendario

document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  if (!calendarEl) return;

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es', // 👈 fuerza idioma español
    height: 'auto',
    events: <?= json_encode($citas, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>,
    eventColor: '#a16976',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    buttonText: { // 👈 texto personalizado de botones
      today: 'Hoy',
      month: 'Mes',
      week: 'Semana',
      day: 'Día'
    }
  });

  calendar.render();
});


</script>

<style>
.input-group select, .tabla-inventario select {
  width: 100%;
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}
.tabla-inventario input[type="time"] {
  width: 110px;
  padding: 5px;
}
.btn-modificar {
  background-color: #a16976;
  color: #fff;
  border: none;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
}
.btn-modificar:hover { background-color: #814c59; }
</style>

<?php $conexion->close(); ?>
</body>
</html>
