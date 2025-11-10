<?php
include("conexion.php");
date_default_timezone_set('America/Mexico_City');

// 🧾 Registrar nuevo pago
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_pago'])) {
    $id_cita = intval($_POST['id_cita']);
    $monto = floatval($_POST['monto']);
    $fecha_pago = $_POST['fecha_pago'];
    $metodo = $_POST['metodo_pago'];

    $query_cita = $conexion->prepare("
        SELECT id_paciente, id_tratamiento 
        FROM citas 
        WHERE id_cita = ?
    ");
    $query_cita->bind_param("i", $id_cita);
    $query_cita->execute();
    $result = $query_cita->get_result();
    $cita = $result->fetch_assoc();
    $query_cita->close();

    $id_paciente = $cita['id_paciente'] ?? null;
    $id_tratamiento = $cita['id_tratamiento'] ?? null;

    $stmt = $conexion->prepare("
        INSERT INTO pagos (id_paciente, id_tratamiento, id_cita, monto, metodo_pago, fecha_pago)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiidss", $id_paciente, $id_tratamiento, $id_cita, $monto, $metodo, $fecha_pago);

    if ($stmt->execute()) {
        echo "<script>alert('Pago registrado correctamente.'); window.location='form_pago.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error al registrar el pago.');</script>";
    }
    $stmt->close();
}

// 🗑️ Eliminar pago específico
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_pago'])) {
    $id_pago = intval($_POST['id_pago']);
    if ($id_pago > 0) {
        $conexion->query("DELETE FROM pagos WHERE id_pago = $id_pago");
        echo "<script>alert('Pago eliminado correctamente.'); window.location='form_pago.php';</script>";
        exit;
    } else {
        echo "<script>alert('Seleccione un pago válido.');</script>";
    }
}

// 📋 Consultar pagos existentes
$res = $conexion->query("
  SELECT p.id_pago, pa.nombre AS paciente, t.nombre_tratamiento AS tratamiento,
         p.monto, p.metodo_pago, p.fecha_pago, p.id_cita
  FROM pagos p
  LEFT JOIN pacientes pa ON p.id_paciente = pa.id_paciente
  LEFT JOIN tratamientos t ON p.id_tratamiento = t.id_tratamiento
  ORDER BY p.fecha_pago DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ortholex — Pagos</title>
  <link rel="stylesheet" href="css/inicio.css">
</head>
<body>

<div class="topbar">
  <img src="imagenes/logo" alt="Logo" class="topbar-logo">
</div>

<div class="sidebar">
  <ul class="menu">
    <li><a href="form_cita.php">Citas</a></li>
    <li><a href="pacientes.php">Pacientes</a></li>
    <li><a href="form_expediente.php">Expedientes</a></li>
    <li><a href="form_inventario.php">Inventario</a></li>
    <li><a href="form_pago.php" class="active">Pagos</a></li>
    <li><a href="tratamientos.php">Tratamientos</a></li>
    <li><a href="index.php">Salir</a></li>
  </ul>
</div>

<div class="main">
  <div class="content">

    <div class="inventario-container">
      <div class="inventario-header">
        <h2>Historial de Pagos</h2>
        <div style="display:flex;gap:10px;">
          <button id="btnNuevoPago" class="btn-modificar" onclick="togglePago()">Nuevo pago</button>
          <button id="btnEliminarPago" class="btn-eliminar" onclick="toggleEliminar()">Eliminar pago</button>
        </div>
      </div>

      <!-- 💳 Formulario de registro de pago -->
      <div class="form-box" id="nuevoPago" style="display:none;">
        <form method="POST">
          <h3>Registrar nuevo pago</h3>

          <div class="input-group">
            <label for="id_cita">Seleccionar cita:</label>
            <select id="id_cita" name="id_cita" required onchange="rellenarCampos()">
              <option value="">Seleccione una cita...</option>
              <?php
              $citas = $conexion->query("
                SELECT c.id_cita, p.nombre AS paciente, 
                       t.nombre_tratamiento AS tratamiento, 
                       t.costo AS precio_tratamiento,
                       c.fecha, c.hora
                FROM citas c
                JOIN pacientes p ON c.id_paciente = p.id_paciente
                LEFT JOIN tratamientos t ON c.id_tratamiento = t.id_tratamiento
                WHERE c.estado IN ('pendiente','realizada')
                ORDER BY c.fecha DESC, c.hora ASC
              ");
              if ($citas->num_rows > 0) {
                while ($c = $citas->fetch_assoc()) {
                  $tratamiento = $c['tratamiento'] ?? 'Sin tratamiento';
                  $precio = $c['precio_tratamiento'] ?? '';
                  echo "<option value='{$c['id_cita']}'
                                data-paciente='".htmlspecialchars($c['paciente'])."'
                                data-tratamiento='".htmlspecialchars($tratamiento)."'
                                data-precio='".htmlspecialchars($precio)."'>
                        {$c['paciente']} — {$tratamiento} ({$c['fecha']} {$c['hora']})
                      </option>";
                }
              } else {
                echo "<option value=''>No hay citas disponibles</option>";
              }
              ?>
            </select>
          </div>

          <div class="input-group">
            <label>Paciente:</label>
            <input type="text" id="paciente" name="paciente" readonly required>
          </div>

          <div class="input-group">
            <label>Tratamiento:</label>
            <input type="text" id="tratamiento" name="tratamiento" readonly required>
          </div>

          <div class="input-group">
            <label>Monto ($):</label>
            <input type="number" id="monto" name="monto" step="0.01" required>
          </div>

          <div class="input-group">
            <label>Fecha de pago:</label>
            <input type="date" id="fecha_pago" name="fecha_pago" value="<?= date('Y-m-d') ?>" required>
          </div>

          <div class="input-group">
            <label>Método de pago:</label>
            <select id="metodo" name="metodo_pago" required>
              <option value="">Seleccione...</option>
              <option value="Efectivo">Efectivo</option>
              <option value="Tarjeta">Tarjeta</option>
              <option value="Transferencia">Transferencia</option>
            </select>
          </div>

          <div class="buttons">
            <button type="submit" name="registrar_pago" class="btn-guardar">Guardar</button>
            <button type="button" class="btn-cancelar" onclick="cerrarPago()">Cancelar</button>
          </div>
        </form>
      </div>

      <!-- 🗑️ Formulario para eliminar pago -->
      <div class="form-box" id="formEliminar" style="display:none;">
        <form method="POST">
          <h3>Eliminar pago</h3>

          <div class="input-group">
            <label for="id_pago">Seleccione el pago:</label>
            <select id="id_pago" name="id_pago" required>
              <option value="">Seleccione...</option>
              <?php
              $pagos = $conexion->query("
                SELECT p.id_pago, pa.nombre AS paciente, t.nombre_tratamiento AS tratamiento,
                       p.monto, p.fecha_pago
                FROM pagos p
                LEFT JOIN pacientes pa ON p.id_paciente = pa.id_paciente
                LEFT JOIN tratamientos t ON p.id_tratamiento = t.id_tratamiento
                ORDER BY p.fecha_pago DESC
              ");
              if ($pagos && $pagos->num_rows > 0) {
                while ($p = $pagos->fetch_assoc()) {
                  echo "<option value='{$p['id_pago']}'>
                          {$p['paciente']} — {$p['tratamiento']} — $".number_format($p['monto'],2)." — ".date('d/m/Y', strtotime($p['fecha_pago']))."
                        </option>";
                }
              } else {
                echo "<option value=''>No hay pagos registrados</option>";
              }
              ?>
            </select>
          </div>

          <div class="buttons">
            <button type="submit" name="eliminar_pago" class="btn-guardar">Eliminar</button>
            <button type="button" class="btn-cancelar" onclick="cerrarEliminar()">Cancelar</button>
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
            <th>Cita</th>
          </tr>

          <?php if ($res && $res->num_rows > 0): ?>
            <?php while ($row = $res->fetch_assoc()): ?>
              <tr>
                <td><?= $row['id_pago'] ?></td>
                <td><?= htmlspecialchars($row['paciente']) ?></td>
                <td><?= htmlspecialchars($row['tratamiento']) ?></td>
                <td>$<?= number_format($row['monto'], 2) ?></td>
                <td><?= date('d-m-Y', strtotime($row['fecha_pago'])) ?></td>
                <td><?= htmlspecialchars($row['metodo_pago']) ?></td>
                <td>
                  <?php if (!empty($row['id_cita'])): ?>
                    <a href="form_cita.php?id=<?= $row['id_cita'] ?>">#<?= $row['id_cita'] ?></a>
                  <?php else: ?>
                    —
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="7" style="text-align:center;padding:20px;color:#555;">No hay pagos registrados.</td></tr>
          <?php endif; ?>
        </table>
      </div>

    </div>
  </div>
</div>

<script>
let pagoAbierto = false;
let eliminarAbierto = false;

function togglePago() {
  const form = document.getElementById('nuevoPago');
  if (!pagoAbierto) {
    form.style.display = 'block';
    pagoAbierto = true;
    eliminarAbierto = false;
    document.getElementById('formEliminar').style.display = 'none';
  }
}

function toggleEliminar() {
  const form = document.getElementById('formEliminar');
  if (!eliminarAbierto) {
    form.style.display = 'block';
    eliminarAbierto = true;
    pagoAbierto = false;
    document.getElementById('nuevoPago').style.display = 'none';
  }
}

function cerrarPago() {
  document.getElementById('nuevoPago').style.display = 'none';
  pagoAbierto = false;
}

function cerrarEliminar() {
  document.getElementById('formEliminar').style.display = 'none';
  eliminarAbierto = false;
}

function rellenarCampos() {
  const select = document.getElementById('id_cita');
  const option = select.options[select.selectedIndex];
  if (!option) return;
  document.getElementById('paciente').value = option.getAttribute('data-paciente') || '';
  document.getElementById('tratamiento').value = option.getAttribute('data-tratamiento') || '';
  document.getElementById('monto').value = option.getAttribute('data-precio') || '';
}

// 🚫 Bloquear navegación con botones "Atrás" y "Adelante"
(function () {
  window.history.pushState(null, "", window.location.href);
  window.onpopstate = function () {
    window.history.pushState(null, "", window.location.href);
  };
})();
</script>

<style>
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
}
.input-group select:focus {
  border-color: #a16976;
  box-shadow: 0 0 4px rgba(161,105,118,0.4);
  outline: none;
}
table th, table td { text-align: center; }
</style>

<?php $conexion->close(); ?>
</body>
</html>


