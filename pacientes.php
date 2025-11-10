<?php
include("conexion.php");
date_default_timezone_set('America/Mexico_City');

/* ===================== ACCIONES POST ===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? '';

  // Guardar nuevo paciente
  if ($accion === 'guardar_paciente') {
    $nombre = trim($_POST['nombre']);
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?: null;
    $celular = trim($_POST['celular'] ?? '');
    $estado_civil = trim($_POST['estado_civil'] ?? '');
    $nacionalidad = trim($_POST['nacionalidad'] ?? '');
    $domicilio = trim($_POST['domicilio'] ?? '');
    $profesion = trim($_POST['profesion'] ?? '');
    $contacto_emergencia = trim($_POST['contacto_emergencia'] ?? '');
    $telefono_emergencia = trim($_POST['telefono_emergencia'] ?? '');

    $stmt = $conexion->prepare("
      INSERT INTO pacientes
      (nombre, fecha_nacimiento, celular, estado_civil, nacionalidad, domicilio, profesion, contacto_emergencia, telefono_emergencia, fecha_registro)
      VALUES (?,?,?,?,?,?,?,?,?, NOW())
    ");
    $stmt->bind_param("sssssssss", $nombre, $fecha_nacimiento, $celular, $estado_civil, $nacionalidad, $domicilio, $profesion, $contacto_emergencia, $telefono_emergencia);
    $ok = $stmt->execute();
    $stmt->close();

    echo "<script>alert('".($ok?'Paciente registrado':'Error al registrar paciente')."'); window.location='pacientes.php';</script>";
    exit;
  }

  // Guardar historia clínica (autorreporte)
  if ($accion === 'guardar_historia') {
    $id_paciente = intval($_POST['id_paciente']);
    $lugar = trim($_POST['lugar'] ?? '');
    $fecha = $_POST['fecha'] ?: date('Y-m-d');
    $motivo_consulta = trim($_POST['motivo_consulta'] ?? '');

    // Campos básicos (extiende con tus columnas reales)
    $stmt = $conexion->prepare("
      INSERT INTO historia_clinica (id_paciente, lugar, fecha, motivo_consulta, fecha_registro)
      VALUES (?,?,?,?, NOW())
    ");
    $stmt->bind_param("isss", $id_paciente, $lugar, $fecha, $motivo_consulta);
    $ok = $stmt->execute();
    $stmt->close();

    echo "<script>alert('".($ok?'Historia registrada':'Error al registrar historia')."'); window.location='pacientes.php?id_paciente=".$id_paciente."';</script>";
    exit;
  }

  // Guardar exploración bucal (requiere id_historia)
  if ($accion === 'guardar_exploracion') {
    $id_historia = intval($_POST['id_historia']);
    // === Campos de tu formulario (ajusta nombres si difieren en tu tabla) ===
    $dolor_donde = trim($_POST['dolor_donde'] ?? '');
    $calma = trim($_POST['calma'] ?? 'No');
    $con_que = trim($_POST['con_que'] ?? '');
    $ultima_visita = $_POST['ultima_visita'] ?: null;
    $sangrado_encias = trim($_POST['sangrado_encias'] ?? 'No');
    $sangrado_cuando = trim($_POST['sangrado_cuando'] ?? '');
    $movilidad = trim($_POST['movilidad'] ?? 'No');
    $indice_placa = trim($_POST['indice_placa'] ?? '');
    $higiene = trim($_POST['higiene'] ?? 'Regular');
    $manchas = trim($_POST['manchas'] ?? 'No');
    $manchas_desc = trim($_POST['manchas_desc'] ?? '');
    $golpe = trim($_POST['golpe'] ?? 'No');
    $fractura = trim($_POST['fractura'] ?? 'No');
    $cual_diente = trim($_POST['cual_diente'] ?? '');
    $tratamiento_diente = trim($_POST['tratamiento_diente'] ?? '');
    $dificultad_abrir = trim($_POST['dificultad_abrir'] ?? '');
    $sarro = trim($_POST['sarro'] ?? 'No');
    $periodontal = trim($_POST['periodontal'] ?? 'No');
    $estado_bucal = trim($_POST['estado_bucal'] ?? '');
    $diagnostico = trim($_POST['diagnostico'] ?? '');
    $plan_tratamiento = trim($_POST['plan_tratamiento'] ?? '');
    $observaciones = trim($_POST['observaciones'] ?? '');
    $firma_dentista = trim($_POST['firma_dentista'] ?? '');

    $stmt = $conexion->prepare("
      INSERT INTO exploracion_bucal
      (id_historia, dolor_donde, calma, con_que, ultima_visita, sangrado_encias, sangrado_cuando, movilidad, indice_placa, higiene, manchas, manchas_desc, golpe, fractura, cual_diente, tratamiento_diente, dificultad_abrir, sarro, periodontal, estado_bucal, diagnostico, plan_tratamiento, observaciones, firma_dentista, fecha_registro)
      VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, ?, NOW())
    ");
    $stmt->bind_param(
      "isssssssssssssssssssssss",
      $id_historia, $dolor_donde, $calma, $con_que, $ultima_visita, $sangrado_encias, $sangrado_cuando, $movilidad, $indice_placa, $higiene, $manchas, $manchas_desc, $golpe, $fractura, $cual_diente, $tratamiento_diente, $dificultad_abrir, $sarro, $periodontal, $estado_bucal, $diagnostico, $plan_tratamiento, $observaciones, $firma_dentista
    );
    $ok = $stmt->execute();
    $stmt->close();

    // Obtener id_paciente para regresar a su panel
    $q = $conexion->prepare("SELECT id_paciente FROM historia_clinica WHERE id_historia=?");
    $q->bind_param("i", $id_historia);
    $q->execute();
    $r = $q->get_result()->fetch_assoc();
    $q->close();

    $id_paciente = $r['id_paciente'] ?? 0;

    echo "<script>alert('".($ok?'Exploración guardada':'Error al guardar exploración')."'); window.location='pacientes.php?id_paciente=".$id_paciente."';</script>";
    exit;
  }
}

/* ===================== DATOS PARA VISTA ===================== */
$id_paciente_sel = isset($_GET['id_paciente']) ? intval($_GET['id_paciente']) : 0;

// Lista de pacientes
$pacientes = $conexion->query("SELECT id_paciente, nombre, celular FROM pacientes ORDER BY nombre ASC");

// Si hay paciente seleccionado: info, historias, y (si quieres) última exploración
$paciente_info = null;
$historias = null;
if ($id_paciente_sel > 0) {
  $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE id_paciente=?");
  $stmt->bind_param("i", $id_paciente_sel);
  $stmt->execute();
  $paciente_info = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  $historias = $conexion->query("
     SELECT id_historia, lugar, fecha, motivo_consulta, fecha_registro
     FROM historia_clinica
     WHERE id_paciente = {$id_paciente_sel}
     ORDER BY fecha DESC, id_historia DESC
  ");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ortholex — Pacientes</title>
  <link rel="stylesheet" href="css/inicio.css">
</head>
<body>
  <!-- Topbar -->
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

  <!-- Main -->
  <div class="main">
    <div class="content">

      <!-- Lista + nuevo paciente -->
      <div class="inventario-container">
        <div class="inventario-header">
          <h2>Pacientes</h2>
<a href="form_paciente.php" class="btn-modificar">Nuevo paciente</a>
        </div>

        <!-- Tabla pacientes -->
        <div class="tabla-inventario">
          <table>
            <tr><th>ID</th><th>Nombre</th><th>Celular</th><th>Ver</th></tr>
            <?php if ($pacientes && $pacientes->num_rows>0): while($p = $pacientes->fetch_assoc()): ?>
              <tr>
                <td><?= $p['id_paciente'] ?></td>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= htmlspecialchars($p['celular']) ?></td>
                <td><a class="btn-modificar" href="pacientes.php?id_paciente=<?= $p['id_paciente'] ?>">Abrir</a></td>
              </tr>
            <?php endwhile; else: ?>
              <tr><td colspan="4" style="text-align:center;">No hay pacientes registrados</td></tr>
            <?php endif; ?>
          </table>
        </div>
      </div>

      <!-- Panel del paciente seleccionado -->
      <?php if ($id_paciente_sel > 0 && $paciente_info): ?>
      <div class="inventario-container" style="margin-top:30px;">
        <div class="inventario-header">
          <h2>Paciente: <?= htmlspecialchars($paciente_info['nombre']) ?></h2>
          <div style="display:flex;gap:10px;">
            <button class="btn-modificar" onclick="toggle('formHistoria')">Nueva historia clínica</button>
            <button class="btn-modificar" onclick="toggle('formExploracion')">Nueva exploración</button>
          </div>
        </div>

        <!-- Historias del paciente -->
        <h3>Historias clínicas</h3>
        <div class="tabla-inventario">
          <table>
            <tr><th>ID Historia</th><th>Fecha</th><th>Lugar</th><th>Motivo</th></tr>
            <?php if ($historias && $historias->num_rows>0): while($h = $historias->fetch_assoc()): ?>
              <tr>
                <td><?= $h['id_historia'] ?></td>
                <td><?= htmlspecialchars($h['fecha']) ?></td>
                <td><?= htmlspecialchars($h['lugar']) ?></td>
                <td><?= htmlspecialchars($h['motivo_consulta']) ?></td>
              </tr>
            <?php endwhile; else: ?>
              <tr><td colspan="4" style="text-align:center;">Sin historias para este paciente.</td></tr>
            <?php endif; ?>
          </table>
        </div>

        <!-- Form historia clínica (autorreporte) -->
        <div class="form-box" id="formHistoria" style="display:none;">
          <form method="POST" autocomplete="off">
            <h3>Nueva historia clínica</h3>
            <input type="hidden" name="accion" value="guardar_historia">
            <input type="hidden" name="id_paciente" value="<?= $id_paciente_sel ?>">

            <div class="input-group"><label>Lugar</label><input name="lugar"></div>
            <div class="input-group"><label>Fecha</label><input type="date" name="fecha" value="<?= date('Y-m-d') ?>"></div>
            <div class="input-group"><label>Motivo de consulta</label><textarea name="motivo_consulta"></textarea></div>

            <div class="buttons">
              <button class="btn-guardar" type="submit">Guardar historia</button>
              <button class="btn-cancelar" type="button" onclick="toggle('formHistoria')">Cancelar</button>
            </div>
          </form>
        </div>

        <!-- Form exploración bucal -->
        <div class="form-box" id="formExploracion" style="display:none;">
          <form method="POST" autocomplete="off">
            <h3>Nueva exploración bucal</h3>
            <input type="hidden" name="accion" value="guardar_exploracion">

            <!-- Seleccionar historia a la que se liga la exploración -->
            <div class="input-group">
              <label>Historia clínica</label>
              <select name="id_historia" required>
                <option value="">Seleccione...</option>
                <?php
                  $hs = $conexion->query("SELECT id_historia, fecha, motivo_consulta FROM historia_clinica WHERE id_paciente={$id_paciente_sel} ORDER BY fecha DESC, id_historia DESC");
                  if ($hs && $hs->num_rows>0) {
                    while($x = $hs->fetch_assoc()){
                      echo "<option value='{$x['id_historia']}'>#{$x['id_historia']} — {$x['fecha']} — ".htmlspecialchars($x['motivo_consulta'])."</option>";
                    }
                  }
                ?>
              </select>
            </div>

            <!-- Campos principales (los que me pasaste) -->
            <div class="input-group"><label>¿Dónde hay dolor?</label><input name="dolor_donde"></div>
            <div class="input-group"><label>¿Se calma?</label>
              <select name="calma"><option>No</option><option>Sí</option></select>
            </div>
            <div class="input-group"><label>¿Con qué?</label><input name="con_que"></div>
            <div class="input-group"><label>Última visita al dentista</label><input type="date" name="ultima_visita"></div>
            <div class="input-group"><label>¿Sangrado de encías?</label>
              <select name="sangrado_encias"><option>No</option><option>Sí</option></select>
            </div>
            <div class="input-group"><label>¿Cuándo?</label><input name="sangrado_cuando"></div>
            <div class="input-group"><label>¿Movilidad dental?</label>
              <select name="movilidad"><option>No</option><option>Sí</option></select>
            </div>
            <div class="input-group"><label>Índice de placa</label><input name="indice_placa"></div>
            <div class="input-group"><label>Higiene</label>
              <select name="higiene">
                <option>Muy buena</option><option>Buena</option><option>Regular</option><option>Mala</option>
              </select>
            </div>
            <div class="input-group"><label>¿Manchas?</label>
              <select name="manchas"><option>No</option><option>Sí</option></select>
            </div>
            <div class="input-group"><label>Descripción de manchas</label><input name="manchas_desc"></div>
            <div class="input-group"><label>¿Golpe en dientes?</label>
              <select name="golpe"><option>No</option><option>Sí</option></select>
            </div>
            <div class="input-group"><label>¿Fractura?</label>
              <select name="fractura"><option>No</option><option>Sí</option></select>
            </div>
            <div class="input-group"><label>¿Cuál diente?</label><input name="cual_diente"></div>
            <div class="input-group"><label>¿Tratamiento previo?</label><input name="tratamiento_diente"></div>
            <div class="input-group"><label>¿Dificultad para abrir la boca?</label><input name="dificultad_abrir"></div>
            <div class="input-group"><label>¿Sarro?</label>
              <select name="sarro"><option>No</option><option>Sí</option></select>
            </div>
            <div class="input-group"><label>¿Enfermedad periodontal?</label>
              <select name="periodontal"><option>No</option><option>Sí</option></select>
            </div>
            <div class="input-group"><label>Estado bucal general</label><textarea name="estado_bucal"></textarea></div>

            <h4>Diagnóstico y tratamiento</h4>
            <div class="input-group"><label>Diagnóstico</label><textarea name="diagnostico"></textarea></div>
            <div class="input-group"><label>Plan de tratamiento</label><textarea name="plan_tratamiento"></textarea></div>
            <div class="input-group"><label>Observaciones</label><textarea name="observaciones"></textarea></div>
            <div class="input-group"><label>Firma del dentista</label><input name="firma_dentista"></div>

            <div class="buttons">
              <button class="btn-guardar" type="submit">Guardar exploración</button>
              <button class="btn-cancelar" type="button" onclick="toggle('formExploracion')">Cancelar</button>
            </div>
          </form>
        </div>

      </div>
      <?php endif; ?>

    </div>
  </div>

  <script>
  function toggle(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.display = (el.style.display === 'none' || el.style.display === '') ? 'block' : 'none';
    if (el.style.display === 'block') {
      window.scrollTo({ top: el.offsetTop - 80, behavior: 'smooth' });
    }
  }
  </script>

  <style>
    .tabla-inventario table th, .tabla-inventario table td { text-align:center; }
    .form-box .input-group { margin-bottom: 10px; }
    .form-box .input-group input,
    .form-box .input-group textarea,
    .form-box .input-group select {
      width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 8px; font-size: 14px;
    }
    .form-box h3, .form-box h4 { margin-top: 10px; }
  </style>

<?php $conexion->close(); ?>
</body>
</html>
