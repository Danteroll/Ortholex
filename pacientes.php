<?php
include("conexion.php");
date_default_timezone_set('America/Mexico_City');

/* ===================== ACCIONES ===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? '';

  // Guardar expediente
  if ($accion === 'guardar_expediente') {
    $id_p = intval($_POST['id_paciente']);
    $desc = $_POST['descripcion'];
    $fecha_actual = date('Y-m-d H:i:s');

    if (!is_dir("uploads")) mkdir("uploads");
    $nombreArchivo = $_FILES['archivo']['name'];
    $ruta = "uploads/" . time() . "_" . basename($nombreArchivo);
    move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta);

    $stmt = $conexion->prepare("INSERT INTO expedientes (id_paciente, descripcion, archivo, fecha_subida) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $id_p, $desc, $ruta, $fecha_actual);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Expediente guardado correctamente'); window.location='pacientes.php?id_paciente=$id_p';</script>";
    exit;
  }

  // 🗑️ Eliminar paciente
  if ($accion === 'eliminar_paciente') {
    $id_p = intval($_POST['id_paciente']);
    if ($id_p > 0) {
      $archivos = $conexion->query("SELECT archivo FROM expedientes WHERE id_paciente=$id_p");
      while ($a = $archivos->fetch_assoc()) {
        if (file_exists($a['archivo'])) unlink($a['archivo']);
      }
      $conexion->query("DELETE FROM historia_clinica WHERE id_paciente=$id_p");
      $conexion->query("DELETE FROM citas WHERE id_paciente=$id_p");
      $conexion->query("DELETE FROM pagos WHERE id_paciente=$id_p");
      $conexion->query("DELETE FROM expedientes WHERE id_paciente=$id_p");
      $conexion->query("DELETE FROM pacientes WHERE id_paciente=$id_p");
      echo "<script>alert('Paciente eliminado correctamente'); window.location='pacientes.php';</script>";
      exit;
    }
  }

  // 🗑️ Eliminar expediente individual
  if ($accion === 'eliminar_expediente') {
    $id_exp = intval($_POST['id_expediente']);
    $res = $conexion->query("SELECT archivo, id_paciente FROM expedientes WHERE id_expediente=$id_exp");
    if ($res && $r = $res->fetch_assoc()) {
      if (file_exists($r['archivo'])) unlink($r['archivo']);
      $conexion->query("DELETE FROM expedientes WHERE id_expediente=$id_exp");
      echo "<script>alert('Expediente eliminado correctamente'); window.location='pacientes.php?id_paciente={$r['id_paciente']}';</script>";
      exit;
    }
  }
}

/* ===================== DATOS ===================== */
$id_paciente_sel = isset($_GET['id_paciente']) ? intval($_GET['id_paciente']) : 0;
$pacientes = $conexion->query("SELECT id_paciente, nombre, celular FROM pacientes ORDER BY nombre ASC");

$paciente_info = null;
$historia = null;
$citas = null;
$pagos = null;
$expedientes = null;

if ($id_paciente_sel > 0) {
  $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE id_paciente=?");
  $stmt->bind_param("i", $id_paciente_sel);
  $stmt->execute();
  $paciente_info = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  $historia = $conexion->query("SELECT * FROM historia_clinica WHERE id_paciente=$id_paciente_sel ORDER BY id_historia DESC LIMIT 1")->fetch_assoc();

  $citas = $conexion->query("
    SELECT c.id_cita, c.fecha, c.hora, c.estado, t.nombre_tratamiento
    FROM citas c
    LEFT JOIN tratamientos t ON c.id_tratamiento = t.id_tratamiento
    WHERE c.id_paciente = $id_paciente_sel
    ORDER BY c.fecha DESC, c.hora DESC
  ");
  $pagos = $conexion->query("
    SELECT p.id_pago, p.fecha_pago, p.monto, p.metodo_pago, t.nombre_tratamiento AS tratamiento, c.id_cita
    FROM pagos p
    LEFT JOIN tratamientos t ON p.id_tratamiento = t.id_tratamiento
    LEFT JOIN citas c ON p.id_cita = c.id_cita
    WHERE p.id_paciente = $id_paciente_sel
    ORDER BY p.fecha_pago DESC
  ");
  $expedientes = $conexion->query("
    SELECT * FROM expedientes WHERE id_paciente=$id_paciente_sel ORDER BY fecha_subida DESC
  ");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ortholex — Pacientes</title>
<link rel="stylesheet" href="css/inicio.css">
<style>
form.visual {
  background:#fff;
  padding:25px;
  border-radius:10px;
  box-shadow:0 3px 6px rgba(0,0,0,0.1);
  margin-bottom:40px;
}
form.visual label {
  font-weight:bold;
  display:block;
  margin-top:10px;
  color:#1d3557;
}
form.visual input, form.visual select, form.visual textarea {
  width:100%;
  padding:8px;
  margin-top:5px;
  border:1px solid #ccc;
  border-radius:6px;
  background:#f9fafb;
  color:#333;
}
form.visual input[readonly], form.visual textarea[readonly] {
  background:#f1f5f9;
  color:#222;
}
.section-title {
  margin-top:25px;
  color:#a16976;
  font-size:18px;
  border-bottom:1px solid #a16976;
  padding-bottom:4px;
}
.btn-ver {
  background:#a16976;
  color:white;
  border:none;
  padding:6px 12px;
  border-radius:6px;
  font-size:14px;
  cursor:pointer;
  transition:background 0.3s, transform 0.2s;
}
.btn-ver:hover {
  background:#8b5564;
  transform:scale(1.05);
}
</style>
</head>
<body>
<div class="topbar">
  <img src="imagenes/logo" alt="Logo" class="topbar-logo">
</div>

<div class="sidebar">
  <ul class="menu">
    <li><a href="form_cita.php">Citas</a></li>
    <li><a href="pacientes.php" class="active">Pacientes</a></li>
    <li><a href="form_inventario.php">Inventario</a></li>
    <li><a href="form_pago.php">Pagos</a></li>
    <li><a href="tratamientos.php">Tratamientos</a></li>
    <li><a href="index.php">Salir</a></li>
  </ul>
</div>

<div class="main">
  <div class="inventario-container">
    <div class="inventario-header">
      <h2>Pacientes registrados</h2>
      <a href="form_paciente.php"><button class="btn-modificar">Nuevo paciente</button></a>
    </div>

    <div class="tabla-inventario">
      <table>
        <tr><th>ID</th><th>Nombre</th><th>Celular</th><th>Ver</th></tr>
        <?php if ($pacientes && $pacientes->num_rows > 0): while($p = $pacientes->fetch_assoc()): ?>
        <tr>
          <td><?= $p['id_paciente'] ?></td>
          <td><?= htmlspecialchars($p['nombre']) ?></td>
          <td><?= htmlspecialchars($p['celular']) ?></td>
          <td><a href="pacientes.php?id_paciente=<?= $p['id_paciente'] ?>"><button class="btn-modificar">Abrir</button></a></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="4" style="text-align:center;">No hay pacientes registrados</td></tr>
        <?php endif; ?>
      </table>
    </div>
  </div>

  <?php if ($id_paciente_sel > 0 && $paciente_info): ?>
  <div class="inventario-container">
    <div class="inventario-header">
      <h2>Paciente: <?= htmlspecialchars($paciente_info['nombre']) ?></h2>
      <div style="display:flex;gap:10px;">
        <button class="btn-modificar" onclick="toggle('formExpediente')">Nuevo expediente</button>

        <form method="POST" onsubmit="return confirm('¿Eliminar paciente y todos sus datos?');">
          <input type="hidden" name="accion" value="eliminar_paciente">
          <input type="hidden" name="id_paciente" value="<?= $id_paciente_sel ?>">
          <button type="submit" class="btn-eliminar">Eliminar paciente</button>
        </form>

        <button class="btn-cancelar" onclick="window.location='pacientes.php'">Cerrar</button>
      </div>
    </div>

    <!-- 🧍 DATOS PERSONALES -->
    <form class="visual">
      <div class="section-title">Datos personales</div>
      <label>Nombre completo</label>
      <input readonly value="<?= htmlspecialchars($paciente_info['nombre']) ?>">
      <label>Fecha de nacimiento</label>
      <input readonly value="<?= date('d/m/Y', strtotime($paciente_info['fecha_nacimiento'])) ?>">
      <label>Celular</label>
      <input readonly value="<?= htmlspecialchars($paciente_info['celular']) ?>">
      <label>Estado civil</label>
      <input readonly value="<?= htmlspecialchars($paciente_info['estado_civil']) ?>">
      <label>Nacionalidad</label>
      <input readonly value="<?= htmlspecialchars($paciente_info['nacionalidad']) ?>">
      <label>Domicilio</label>
      <textarea readonly><?= htmlspecialchars($paciente_info['domicilio']) ?></textarea>
      <label>Profesión</label>
      <input readonly value="<?= htmlspecialchars($paciente_info['profesion']) ?>">
      <label>Contacto de emergencia</label>
      <input readonly value="<?= htmlspecialchars($paciente_info['contacto_emergencia']) ?>">
      <label>Teléfono de emergencia</label>
      <input readonly value="<?= htmlspecialchars($paciente_info['telefono_emergencia']) ?>">
    </form>

    <!-- 🩺 HISTORIA CLÍNICA -->
    <?php if ($historia): ?>
    <form class="visual">
      <div class="section-title">Historia clínica (autorreporte)</div>
      <label>Lugar</label><input readonly value="<?= htmlspecialchars($historia['lugar']) ?>">
      <label>Fecha</label><input readonly value="<?= date('d/m/Y', strtotime($historia['fecha'])) ?>">
      <label>Motivo de consulta</label><textarea readonly><?= htmlspecialchars($historia['motivo_consulta']) ?></textarea>
      <label>¿Sufre alguna enfermedad?</label><input readonly value="<?= htmlspecialchars($historia['enf_general']) ?>">
      <label>¿Cuál?</label><textarea readonly><?= htmlspecialchars($historia['enf_cual']) ?></textarea>
      <label>Medicamentos</label><textarea readonly><?= htmlspecialchars($historia['medicamentos']) ?></textarea>
      <label>Alergias</label><textarea readonly><?= htmlspecialchars($historia['alergias']) ?></textarea>
      <label>Observaciones</label><textarea readonly><?= htmlspecialchars($historia['observaciones']) ?></textarea>
    </form>
    <?php endif; ?>

    <!-- 🗂️ EXPEDIENTES -->
    <h3 style="color:#1d3557;">Expedientes</h3>
    <div class="tabla-inventario">
      <table>
        <tr><th>ID</th><th>Descripción</th><th>Fecha</th><th>Archivo</th><th>Acción</th></tr>
        <?php if ($expedientes && $expedientes->num_rows > 0): while($e = $expedientes->fetch_assoc()): ?>
        <tr>
          <td><?= $e['id_expediente'] ?></td>
          <td><?= htmlspecialchars($e['descripcion']) ?></td>
          <td><?= date('d/m/Y H:i', strtotime($e['fecha_subida'])) ?> hrs</td>
          <td>
            <a href="<?= htmlspecialchars($e['archivo']) ?>" target="_blank">
              <button type="button" class="btn-ver">Ver archivo</button>
            </a>
          </td>
          <td>
            <form method="POST" onsubmit="return confirm('¿Eliminar este expediente?');" style="margin:0;">
              <input type="hidden" name="accion" value="eliminar_expediente">
              <input type="hidden" name="id_expediente" value="<?= $e['id_expediente'] ?>">
              <button type="submit" class="btn-eliminar">Eliminar</button>
            </form>
          </td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="5" style="text-align:center;">Sin expedientes registrados</td></tr>
        <?php endif; ?>
      </table>
    </div>

    <!-- ➕ NUEVO EXPEDIENTE -->
    <div class="form-box" id="formExpediente" style="display:none;">
      <form method="POST" enctype="multipart/form-data" class="visual">
        <h3 class="section-title">Nuevo expediente</h3>
        <input type="hidden" name="accion" value="guardar_expediente">
        <input type="hidden" name="id_paciente" value="<?= $id_paciente_sel ?>">

        <div class="input-group">
          <label>Descripción</label>
          <textarea name="descripcion" required placeholder="Ejemplo: Radiografía, análisis, receta..."></textarea>
        </div>

        <div class="input-group">
          <label>Archivo (PDF o imagen)</label>
          <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png" required>
        </div>

        <div class="buttons" style="display:flex;gap:10px;justify-content:center;margin-top:20px;">
          <button type="submit" class="btn-guardar">Guardar</button>
          <button type="button" class="btn-cancelar" onclick="toggle('formExpediente')">Cancelar</button>
        </div>
      </form>
    </div>

    <!-- 🗓️ CITAS -->
    <h3 style="color:#1d3557;">Historial de citas</h3>
    <div class="tabla-inventario">
      <table>
        <tr><th>ID</th><th>Fecha</th><th>Hora</th><th>Tratamiento</th><th>Estado</th></tr>
        <?php if ($citas && $citas->num_rows > 0): while($c = $citas->fetch_assoc()): ?>
        <tr>
          <td><?= $c['id_cita'] ?></td>
          <td><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
          <td><?= date('g:i A', strtotime($c['hora'])) ?></td>
          <td><?= htmlspecialchars($c['nombre_tratamiento'] ?? '—') ?></td>
          <td><?= ucfirst($c['estado']) ?></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="5" style="text-align:center;">Sin citas registradas</td></tr>
        <?php endif; ?>
      </table>
    </div>

    <!-- 💳 PAGOS -->
    <h3 style="color:#1d3557;">Historial de pagos</h3>
    <div class="tabla-inventario">
      <table>
        <tr><th>ID</th><th>Fecha</th><th>Tratamiento</th><th>Método</th><th>Monto</th><th>Cita</th></tr>
        <?php if ($pagos && $pagos->num_rows > 0): while($p = $pagos->fetch_assoc()): ?>
        <tr>
          <td><?= $p['id_pago'] ?></td>
          <td><?= date('d/m/Y', strtotime($p['fecha_pago'])) ?></td>
          <td><?= htmlspecialchars($p['tratamiento'] ?? '—') ?></td>
          <td><?= htmlspecialchars($p['metodo_pago']) ?></td>
          <td>$<?= number_format($p['monto'], 2) ?></td>
          <td><?= $p['id_cita'] ? '#'.$p['id_cita'] : '—' ?></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="6" style="text-align:center;">Sin pagos registrados</td></tr>
        <?php endif; ?>
      </table>
    </div>
  </div>
  <?php endif; ?>
</div>

<script>
function toggle(id){
  const el=document.getElementById(id);
  if(!el)return;
  el.style.display=(el.style.display==='none'||el.style.display==='')?'block':'none';
  if(el.style.display==='block')window.scrollTo({top:el.offsetTop-100,behavior:'smooth'});
}
(function(){
  window.history.pushState(null,"",window.location.href);
  window.onpopstate=function(){window.history.pushState(null,"",window.location.href);};
})();
</script>

<?php $conexion->close(); ?>
</body>
</html>