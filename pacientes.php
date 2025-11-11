<?php
include("conexion.php");
date_default_timezone_set('America/Mexico_City');

/* ===================== ACCIONES ===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? '';

// 🦷 GUARDAR EXPLORACIÓN BUCAL
if ($accion === 'guardar_exploracion') {
    $id_historia = intval($_POST['id_historia']);
    $dolor_donde = trim($_POST['dolor_donde'] ?? '');
    $calma = $_POST['calma'] ?? 'No';
    $con_que = trim($_POST['con_que'] ?? '');
    $ultima_visita = $_POST['ultima_visita'] ?: null;
    $sangrado_encias = $_POST['sangrado_encias'] ?? 'No';
    $sangrado_cuando = trim($_POST['sangrado_cuando'] ?? '');
    $movilidad = $_POST['movilidad'] ?? 'No';
    $indice_placa = trim($_POST['indice_placa'] ?? '');
    $higiene = $_POST['higiene'] ?? 'Buena';
    $manchas = $_POST['manchas'] ?? 'No';
    $manchas_desc = trim($_POST['manchas_desc'] ?? '');
    $golpe = $_POST['golpe'] ?? 'No';
    $fractura = $_POST['fractura'] ?? 'No';
    $cual_diente = trim($_POST['cual_diente'] ?? '');
    $tratamiento_diente = trim($_POST['tratamiento_diente'] ?? '');
    $dificultad_abrir = trim($_POST['dificultad_abrir'] ?? '');
    $sarro = $_POST['sarro'] ?? 'No';
    $periodontal = $_POST['periodontal'] ?? 'No';
    $estado_bucal = trim($_POST['estado_bucal'] ?? '');
    $diagnostico = trim($_POST['diagnostico'] ?? '');
    $plan_tratamiento = trim($_POST['plan_tratamiento'] ?? '');
    $observaciones = trim($_POST['observaciones'] ?? '');

    $stmt = $conexion->prepare("
        INSERT INTO exploracion_bucal (
            id_historia, dolor_donde, calma, con_que, ultima_visita,
            sangrado_encias, sangrado_cuando, movilidad, indice_placa, higiene,
            manchas, manchas_desc, golpe, fractura, cual_diente,
            tratamiento_diente, dificultad_abrir, sarro, periodontal, estado_bucal,
            diagnostico, plan_tratamiento, observaciones, fecha_registro
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    // ✅ 23 variables: 1 entero + 22 strings
    $stmt->bind_param(
        "issssssssssssssssssssss",
        $id_historia, $dolor_donde, $calma, $con_que, $ultima_visita,
        $sangrado_encias, $sangrado_cuando, $movilidad, $indice_placa, $higiene,
        $manchas, $manchas_desc, $golpe, $fractura, $cual_diente,
        $tratamiento_diente, $dificultad_abrir, $sarro, $periodontal, $estado_bucal,
        $diagnostico, $plan_tratamiento, $observaciones
    );

    $ok = $stmt->execute();
    $stmt->close();

    // 🔁 Obtener id_paciente para regresar al perfil
    $q = $conexion->prepare("SELECT id_paciente FROM historia_clinica WHERE id_historia = ?");
    $q->bind_param("i", $id_historia);
    $q->execute();
    $r = $q->get_result()->fetch_assoc();
    $q->close();

    $id_paciente = $r['id_paciente'] ?? 0;

    echo "<script>
      alert('".($ok ? "Exploración bucal registrada correctamente." : "Error al guardar exploración bucal.")."');
      window.location='pacientes.php?id_paciente=$id_paciente';
    </script>";
    exit;
}


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

<!-- 🦷 EXPLORACIÓN BUCAL -->
<?php
// Verificamos primero si hay historia válida
if (!isset($historia['id_historia']) || empty($historia['id_historia'])) {
  echo "<div style='text-align:center;margin:20px 0;color:#555;'>
          <p>⚠️ No se puede registrar una exploración bucal sin una historia clínica.</p>
        </div>";
} else {
  $id_historia = intval($historia['id_historia']);
  $expl = $conexion->query("SELECT * FROM exploracion_bucal WHERE id_historia = $id_historia LIMIT 1");

  if ($expl && $expl->num_rows > 0):
    $exp = $expl->fetch_assoc();
?>
<form class="visual">
  <div class="section-title">Exploración Bucal</div>

  <label>¿Dónde hay dolor?</label><input readonly value="<?= htmlspecialchars($exp['dolor_donde']) ?>">
  <label>¿Se calma?</label><input readonly value="<?= htmlspecialchars($exp['calma']) ?>">
  <label>¿Con qué?</label><input readonly value="<?= htmlspecialchars($exp['con_que']) ?>">
  <label>Última visita al dentista</label><input readonly value="<?= htmlspecialchars($exp['ultima_visita']) ?>">
  <label>¿Sangrado de encías?</label><input readonly value="<?= htmlspecialchars($exp['sangrado_encias']) ?>">
  <label>¿Cuándo?</label><input readonly value="<?= htmlspecialchars($exp['sangrado_cuando']) ?>">
  <label>¿Movilidad dental?</label><input readonly value="<?= htmlspecialchars($exp['movilidad']) ?>">
  <label>Índice de placa</label><input readonly value="<?= htmlspecialchars($exp['indice_placa']) ?>">
  <label>Higiene</label><input readonly value="<?= htmlspecialchars($exp['higiene']) ?>">
  <label>¿Manchas?</label><input readonly value="<?= htmlspecialchars($exp['manchas']) ?>">
  <label>Descripción de manchas</label><input readonly value="<?= htmlspecialchars($exp['manchas_desc']) ?>">
  <label>¿Golpe en dientes?</label><input readonly value="<?= htmlspecialchars($exp['golpe']) ?>">
  <label>¿Fractura?</label><input readonly value="<?= htmlspecialchars($exp['fractura']) ?>">
  <label>¿Cuál diente?</label><input readonly value="<?= htmlspecialchars($exp['cual_diente']) ?>">
  <label>¿Tratamiento previo?</label><input readonly value="<?= htmlspecialchars($exp['tratamiento_diente']) ?>">
  <label>¿Dificultad para abrir la boca?</label><input readonly value="<?= htmlspecialchars($exp['dificultad_abrir']) ?>">
  <label>¿Sarro?</label><input readonly value="<?= htmlspecialchars($exp['sarro']) ?>">
  <label>¿Enfermedad periodontal?</label><input readonly value="<?= htmlspecialchars($exp['periodontal']) ?>">
  <label>Estado bucal general</label><textarea readonly><?= htmlspecialchars($exp['estado_bucal']) ?></textarea>
  <label>Diagnóstico</label><textarea readonly><?= htmlspecialchars($exp['diagnostico']) ?></textarea>
  <label>Plan de tratamiento</label><textarea readonly><?= htmlspecialchars($exp['plan_tratamiento']) ?></textarea>
  <label>Observaciones</label><textarea readonly><?= htmlspecialchars($exp['observaciones']) ?></textarea>
  <label>Fecha de registro</label><input readonly value="<?= date('d/m/Y H:i', strtotime($exp['fecha_registro'])) ?> hrs">
</form>

<?php else: ?>
  <!-- 🔹 Si no hay exploración registrada -->
  <div style="text-align:center;margin:20px 0;">
    <p style="color:#555;">Este paciente no tiene una exploración bucal registrada.</p>
    <button class="btn-modificar" onclick="toggle('formExploracion')">Agregar exploración bucal</button>
  </div>

  <!-- ➕ FORMULARIO NUEVA EXPLORACIÓN (oculto por defecto) -->
  <form class="visual" id="formExploracion" method="POST" autocomplete="off" style="display:none;margin-top:20px;">
    <div class="section-title">Registrar Exploración Bucal</div>
    <input type="hidden" name="accion" value="guardar_exploracion">
    <input type="hidden" name="id_historia" value="<?= $historia['id_historia'] ?>">

    <label>¿Dónde hay dolor?</label><input name="dolor_donde">
    <label>¿Se calma?</label><select name="calma"><option>No</option><option>Sí</option></select>
    <label>¿Con qué?</label><input name="con_que">
    <label>Última visita al dentista</label><input type="date" name="ultima_visita">
    <label>¿Sangrado de encías?</label><select name="sangrado_encias"><option>No</option><option>Sí</option></select>
    <label>¿Cuándo?</label><input name="sangrado_cuando">
    <label>¿Movilidad dental?</label><select name="movilidad"><option>No</option><option>Sí</option></select>
    <label>Índice de placa</label><input name="indice_placa">
    <label>Higiene</label><select name="higiene"><option>Muy buena</option><option>Buena</option><option>Regular</option><option>Mala</option></select>
    <label>¿Manchas?</label><select name="manchas"><option>No</option><option>Sí</option></select>
    <label>Descripción de manchas</label><input name="manchas_desc">
    <label>¿Golpe en dientes?</label><select name="golpe"><option>No</option><option>Sí</option></select>
    <label>¿Fractura?</label><select name="fractura"><option>No</option><option>Sí</option></select>
    <label>¿Cuál diente?</label><input name="cual_diente">
    <label>¿Tratamiento previo?</label><input name="tratamiento_diente">
    <label>¿Dificultad para abrir la boca?</label><input name="dificultad_abrir">
    <label>¿Sarro?</label><select name="sarro"><option>No</option><option>Sí</option></select>
    <label>¿Enfermedad periodontal?</label><select name="periodontal"><option>No</option><option>Sí</option></select>
    <label>Estado bucal general</label><textarea name="estado_bucal"></textarea>
    <label>Diagnóstico</label><textarea name="diagnostico"></textarea>
    <label>Plan de tratamiento</label><textarea name="plan_tratamiento"></textarea>
    <label>Observaciones</label><textarea name="observaciones"></textarea>

    <div class="buttons" style="display:flex;gap:10px;justify-content:center;margin-top:20px;">
      <button type="submit" class="btn-guardar">Guardar</button>
      <button type="button" class="btn-cancelar" onclick="toggle('formExploracion')">Cancelar</button>
    </div>
  </form>
<?php endif; } ?>


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