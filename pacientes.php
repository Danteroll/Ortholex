<?php
include("conexion.php");
date_default_timezone_set('America/Mexico_City');

/* ===================== Acciones ===================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $accion = $_POST['accion'] ?? '';

  // Guarda la exploracion bucal
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

    $stmt->bind_param(
      "issssssssssssssssssssss",
      $id_historia,
      $dolor_donde,
      $calma,
      $con_que,
      $ultima_visita,
      $sangrado_encias,
      $sangrado_cuando,
      $movilidad,
      $indice_placa,
      $higiene,
      $manchas,
      $manchas_desc,
      $golpe,
      $fractura,
      $cual_diente,
      $tratamiento_diente,
      $dificultad_abrir,
      $sarro,
      $periodontal,
      $estado_bucal,
      $diagnostico,
      $plan_tratamiento,
      $observaciones
    );

    $ok = $stmt->execute();
    $stmt->close();

    //Obtiene el id_paciente para regresar al perfil
    $q = $conexion->prepare("SELECT id_paciente FROM historia_clinica WHERE id_historia = ?");
    $q->bind_param("i", $id_historia);
    $q->execute();
    $r = $q->get_result()->fetch_assoc();
    $q->close();

    $id_paciente = $r['id_paciente'] ?? 0;

    echo "<script>
      alert('" . ($ok ? "Exploración bucal registrada correctamente." : "Error al guardar exploración bucal.") . "');
      window.location='pacientes.php?id_paciente=$id_paciente';
    </script>";
    exit;
  }


  //Guarda el expediente
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
  //Edita al paciente
  if ($accion === 'editar_paciente') {
    $id_p = intval($_POST['id_paciente']);
    $nombre = $_POST['nombre'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $celular = $_POST['celular'];
    $estado_civil = $_POST['estado_civil'];
    $nacionalidad = $_POST['nacionalidad'];
    $domicilio = $_POST['domicilio'];
    $profesion = $_POST['profesion'];
    $contacto_emergencia = $_POST['contacto_emergencia'];
    $telefono_emergencia = $_POST['telefono_emergencia'];

    $stmt = $conexion->prepare("UPDATE pacientes SET nombre=?, fecha_nacimiento=?, celular=?, estado_civil=?, nacionalidad=?, domicilio=?, profesion=?, contacto_emergencia=?, telefono_emergencia=? WHERE id_paciente=?");
    $stmt->bind_param("sssssssssi", $nombre, $fecha_nacimiento, $celular, $estado_civil, $nacionalidad, $domicilio, $profesion, $contacto_emergencia, $telefono_emergencia, $id_p);
    $ok = $stmt->execute();
    $stmt->close();

    echo "<script>alert('" . ($ok ? "Datos personales actualizados correctamente." : "Error al actualizar.") . "'); window.location='pacientes.php?id_paciente=$id_p';</script>";
    exit;
  }

  // Edita la historia clinica
  if ($accion === 'editar_historia') {
    $id_h = intval($_POST['id_historia']);
    $campos = [
      'lugar',
      'fecha',
      'motivo_consulta',
      'enf_general',
      'enf_cual',
      'medicamentos',
      'alergias',
      'transfusiones',
      'operado',
      'operado_deque',
      'operado_cuando',
      'fuma',
      'toma',
      'drogas',
      'diabetes',
      'hipertension',
      'epilepsia',
      'infarto',
      'anemia',
      'asma',
      'hepatitis',
      'tiroides',
      'angina_pecho',
      'tuberculosis',
      'renal',
      'venereas',
      'vih',
      'gastritis',
      'embarazo',
      'covid',
      'cancer',
      'otros',
      'observaciones'
    ];
    $sets = implode(',', array_map(fn($f) => "$f=?", $campos));
    $stmt = $conexion->prepare("UPDATE historia_clinica SET $sets WHERE id_historia=?");
    $tipos = str_repeat('s', count($campos)) . 'i';
    $valores = array_map(fn($f) => $_POST[$f] ?? '', $campos);
    $valores[] = $id_h;
    $stmt->bind_param($tipos, ...$valores);
    $ok = $stmt->execute();
    $stmt->close();

    echo "<script>alert('" . ($ok ? "Historia clínica actualizada correctamente." : "Error al actualizar historia clínica.") . "'); window.location='pacientes.php?id_paciente=" . $_POST['id_paciente'] . "';</script>";
    exit;
  }

  // Edita la exploracion bucal
  if ($accion === 'editar_exploracion') {
    $id_e = intval($_POST['id_exploracion']);
    $stmt = $conexion->prepare("UPDATE exploracion_bucal SET dolor_donde=?, calma=?, con_que=?, ultima_visita=?, sangrado_encias=?, sangrado_cuando=?, movilidad=?, indice_placa=?, higiene=?, manchas=?, manchas_desc=?, golpe=?, fractura=?, cual_diente=?, tratamiento_diente=?, dificultad_abrir=?, sarro=?, periodontal=?, estado_bucal=?, diagnostico=?, plan_tratamiento=?, observaciones=? WHERE id_exploracion=?");
    $stmt->bind_param(
      "ssssssssssssssssssssssi",
      $_POST['dolor_donde'],
      $_POST['calma'],
      $_POST['con_que'],
      $_POST['ultima_visita'],
      $_POST['sangrado_encias'],
      $_POST['sangrado_cuando'],
      $_POST['movilidad'],
      $_POST['indice_placa'],
      $_POST['higiene'],
      $_POST['manchas'],
      $_POST['manchas_desc'],
      $_POST['golpe'],
      $_POST['fractura'],
      $_POST['cual_diente'],
      $_POST['tratamiento_diente'],
      $_POST['dificultad_abrir'],
      $_POST['sarro'],
      $_POST['periodontal'],
      $_POST['estado_bucal'],
      $_POST['diagnostico'],
      $_POST['plan_tratamiento'],
      $_POST['observaciones'],
      $id_e
    );
    $ok = $stmt->execute();
    $stmt->close();

    echo "<script>alert('" . ($ok ? "Exploración bucal actualizada correctamente." : "Error al actualizar.") . "'); window.location='pacientes.php?id_paciente=" . $_POST['id_paciente'] . "';</script>";
    exit;
  }


  // Elimina a paciente
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

  // Elimina expediente individual
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
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Celular</th>
            <th>Ver</th>
          </tr>
          <?php if ($pacientes && $pacientes->num_rows > 0): while ($p = $pacientes->fetch_assoc()): ?>
              <tr>
                <td><?= $p['id_paciente'] ?></td>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= htmlspecialchars($p['celular']) ?></td>
                <td><a href="pacientes.php?id_paciente=<?= $p['id_paciente'] ?>"><button class="btn-modificar">Abrir</button></a></td>
              </tr>
            <?php endwhile;
          else: ?>
            <tr>
              <td colspan="4" style="text-align:center;">No hay pacientes registrados</td>
            </tr>
          <?php endif; ?>
        </table>
      </div>
    </div>

    <?php if ($id_paciente_sel > 0 && $paciente_info): ?>
      <div class="inventario-container">
        <div class="inventario-header">
          <h2>Paciente: <?= htmlspecialchars($paciente_info['nombre']) ?></h2>
          <div style="display:flex;gap:10px;">
            <button class="btn-modificar" onclick="toggle('formExpediente')">Nuevo archivo</button>

            <form method="POST" onsubmit="return confirm('¿Eliminar paciente y todos sus datos?');">
              <input type="hidden" name="accion" value="eliminar_paciente">
              <input type="hidden" name="id_paciente" value="<?= $id_paciente_sel ?>">
              <button type="submit" class="btn-eliminar">Eliminar paciente</button>
            </form>

            <button class="btn-cancelar" onclick="window.location='pacientes.php'">Cerrar</button>
          </div>
        </div>

        <!-- Datos personales -->
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
        <div style="text-align:center;margin:10px;">
          <button class="btn-modificar" onclick="toggle('formEditarPaciente')">Editar datos personales</button>
        </div>

        <form class="visual" id="formEditarPaciente" method="POST" style="display:none;">
          <input type="hidden" name="accion" value="editar_paciente">
          <input type="hidden" name="id_paciente" value="<?= $paciente_info['id_paciente'] ?>">

          <label>Nombre completo</label><input name="nombre" value="<?= htmlspecialchars($paciente_info['nombre']) ?>">
          <label>Fecha de nacimiento</label><input type="date" name="fecha_nacimiento" value="<?= htmlspecialchars($paciente_info['fecha_nacimiento']) ?>">
          <label>Celular</label><input name="celular" value="<?= htmlspecialchars($paciente_info['celular']) ?>">
          <label>Estado civil</label><input name="estado_civil" value="<?= htmlspecialchars($paciente_info['estado_civil']) ?>">
          <label>Nacionalidad</label><input name="nacionalidad" value="<?= htmlspecialchars($paciente_info['nacionalidad']) ?>">
          <label>Domicilio</label><textarea name="domicilio"><?= htmlspecialchars($paciente_info['domicilio']) ?></textarea>
          <label>Profesión</label><input name="profesion" value="<?= htmlspecialchars($paciente_info['profesion']) ?>">
          <label>Contacto de emergencia</label><input name="contacto_emergencia" value="<?= htmlspecialchars($paciente_info['contacto_emergencia']) ?>">
          <label>Teléfono de emergencia</label><input name="telefono_emergencia" value="<?= htmlspecialchars($paciente_info['telefono_emergencia']) ?>">

          <div style="display:flex;gap:10px;justify-content:center;margin-top:15px;">
            <button class="btn-guardar" type="submit">Guardar cambios</button>
            <button class="btn-cancelar" type="button" onclick="toggle('formEditarPaciente', true)">Cancelar</button>
          </div>
        </form>


        <!-- Historia clinica -->
        <?php if ($historia): ?>
          <form class="visual">
            <div class="section-title">Historia clínica (autorreporte)</div>

            <label>Lugar</label><input readonly value="<?= htmlspecialchars($historia['lugar']) ?>">
            <label>Fecha</label><input readonly value="<?= date('d/m/Y', strtotime($historia['fecha'])) ?>">
            <label>Motivo de consulta</label><textarea readonly><?= htmlspecialchars($historia['motivo_consulta']) ?></textarea>

            <h3>Antecedentes generales</h3>
            <label>¿Sufre alguna enfermedad?</label><input readonly value="<?= htmlspecialchars($historia['enf_general']) ?>">
            <label>¿Cuál?</label><textarea readonly><?= htmlspecialchars($historia['enf_cual']) ?></textarea>
            <label>Medicamentos</label><textarea readonly><?= htmlspecialchars($historia['medicamentos']) ?></textarea>
            <label>Alergias</label><textarea readonly><?= htmlspecialchars($historia['alergias']) ?></textarea>
            <label>Transfusiones</label><input readonly value="<?= htmlspecialchars($historia['transfusiones']) ?>">

            <h3>Antecedentes quirúrgicos</h3>
            <label>¿Ha sido operado?</label><input readonly value="<?= htmlspecialchars($historia['operado']) ?>">
            <label>¿De qué?</label><textarea readonly><?= htmlspecialchars($historia['operado_deque']) ?></textarea>
            <label>¿Cuándo?</label><input readonly value="<?= htmlspecialchars($historia['operado_cuando']) ?>">

            <h3>Hábitos</h3>
            <label>¿Fuma?</label><input readonly value="<?= htmlspecialchars($historia['fuma']) ?>">
            <label>¿Toma alcohol?</label><input readonly value="<?= htmlspecialchars($historia['toma']) ?>">
            <label>¿Consume drogas?</label><input readonly value="<?= htmlspecialchars($historia['drogas']) ?>">

            <h3>Antecedentes médicos</h3>
            <label>Diabetes</label><input readonly value="<?= htmlspecialchars($historia['diabetes']) ?>">
            <label>Hipertensión</label><input readonly value="<?= htmlspecialchars($historia['hipertension']) ?>">
            <label>Epilepsia</label><input readonly value="<?= htmlspecialchars($historia['epilepsia']) ?>">
            <label>Infarto</label><input readonly value="<?= htmlspecialchars($historia['infarto']) ?>">
            <label>Anemia</label><input readonly value="<?= htmlspecialchars($historia['anemia']) ?>">
            <label>Asma</label><input readonly value="<?= htmlspecialchars($historia['asma']) ?>">
            <label>Hepatitis</label><input readonly value="<?= htmlspecialchars($historia['hepatitis']) ?>">
            <label>Tiroides</label><input readonly value="<?= htmlspecialchars($historia['tiroides']) ?>">
            <label>Angina de pecho</label><input readonly value="<?= htmlspecialchars($historia['angina_pecho']) ?>">
            <label>Tuberculosis</label><input readonly value="<?= htmlspecialchars($historia['tuberculosis']) ?>">
            <label>Enfermedad renal</label><input readonly value="<?= htmlspecialchars($historia['renal']) ?>">
            <label>Enfermedades venéreas</label><input readonly value="<?= htmlspecialchars($historia['venereas']) ?>">
            <label>VIH/SIDA</label><input readonly value="<?= htmlspecialchars($historia['vih']) ?>">
            <label>Gastritis</label><input readonly value="<?= htmlspecialchars($historia['gastritis']) ?>">
            <label>Embarazo</label><input readonly value="<?= htmlspecialchars($historia['embarazo']) ?>">
            <label>COVID-19</label><input readonly value="<?= htmlspecialchars($historia['covid']) ?>">
            <label>Cáncer</label><input readonly value="<?= htmlspecialchars($historia['cancer']) ?>">
            <label>Otros</label><textarea readonly><?= htmlspecialchars($historia['otros']) ?></textarea>

            <h3>Observaciones finales</h3>
            <label>Observaciones</label><textarea readonly><?= htmlspecialchars($historia['observaciones']) ?></textarea>
          </form>
        <?php else: ?>
          <div style="text-align:center;margin:20px 0;color:#555;">
            <p>Este paciente aún no tiene una historia clínica registrada.</p>
          </div>
        <?php endif; ?>

        <div style="text-align:center;margin:10px;">
          <button class="btn-modificar" onclick="toggle('formEditarHistoria')">Editar historia clínica</button>
        </div>

        <form class="visual" id="formEditarHistoria" method="POST" style="display:none;color:#1d3557;">
          <input type="hidden" name="accion" value="editar_historia">
          <input type="hidden" name="id_historia" value="<?= $historia['id_historia'] ?>">
          <input type="hidden" name="id_paciente" value="<?= $id_paciente_sel ?>">

          <label>Lugar</label>
          <input name="lugar" value="<?= htmlspecialchars($historia['lugar']) ?>">

          <label>Fecha</label>
          <input type="date" name="fecha" value="<?= htmlspecialchars($historia['fecha']) ?>">

          <label>Motivo de consulta</label>
          <textarea name="motivo_consulta"><?= htmlspecialchars($historia['motivo_consulta']) ?></textarea>

          <label>¿Sufre alguna enfermedad?</label>
          <select name="enf_general">
            <option <?= ($historia['enf_general'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['enf_general'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>¿Cuál?</label>
          <textarea name="enf_cual"><?= htmlspecialchars($historia['enf_cual']) ?></textarea>

          <label>Medicamentos</label>
          <textarea name="medicamentos"><?= htmlspecialchars($historia['medicamentos']) ?></textarea>

          <label>Alergias</label>
          <textarea name="alergias"><?= htmlspecialchars($historia['alergias']) ?></textarea>

          <label>Transfusiones</label>
          <select name="transfusiones">
            <option <?= ($historia['transfusiones'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['transfusiones'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>¿Ha sido operado?</label>
          <select name="operado">
            <option <?= ($historia['operado'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['operado'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>¿De qué?</label>
          <textarea name="operado_deque"><?= htmlspecialchars($historia['operado_deque']) ?></textarea>

          <label>¿Cuándo?</label>
          <input type="date" name="operado_cuando" value="<?= htmlspecialchars($historia['operado_cuando']) ?>">

          <label>¿Fuma?</label>
          <select name="fuma">
            <option <?= ($historia['fuma'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['fuma'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>¿Toma alcohol?</label>
          <select name="toma">
            <option <?= ($historia['toma'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['toma'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>¿Consume drogas?</label>
          <select name="drogas">
            <option <?= ($historia['drogas'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['drogas'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Diabetes</label>
          <select name="diabetes">
            <option <?= ($historia['diabetes'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['diabetes'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Hipertensión</label>
          <select name="hipertension">
            <option <?= ($historia['hipertension'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['hipertension'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Epilepsia</label>
          <select name="epilepsia">
            <option <?= ($historia['epilepsia'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['epilepsia'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Infarto</label>
          <select name="infarto">
            <option <?= ($historia['infarto'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['infarto'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Anemia</label>
          <select name="anemia">
            <option <?= ($historia['anemia'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['anemia'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Asma</label>
          <select name="asma">
            <option <?= ($historia['asma'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['asma'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Hepatitis</label>
          <select name="hepatitis">
            <option <?= ($historia['hepatitis'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['hepatitis'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Tiroides</label>
          <select name="tiroides">
            <option <?= ($historia['tiroides'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['tiroides'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Angina de pecho</label>
          <select name="angina_pecho">
            <option <?= ($historia['angina_pecho'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['angina_pecho'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Tuberculosis</label>
          <select name="tuberculosis">
            <option <?= ($historia['tuberculosis'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['tuberculosis'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Enfermedad renal</label>
          <select name="renal">
            <option <?= ($historia['renal'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['renal'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Enfermedades venéreas</label>
          <select name="venereas">
            <option <?= ($historia['venereas'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['venereas'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>VIH/SIDA</label>
          <select name="vih">
            <option <?= ($historia['vih'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['vih'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Gastritis</label>
          <select name="gastritis">
            <option <?= ($historia['gastritis'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['gastritis'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Embarazo</label>
          <select name="embarazo">
            <option <?= ($historia['embarazo'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['embarazo'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>COVID-19</label>
          <select name="covid">
            <option <?= ($historia['covid'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['covid'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Cáncer</label>
          <select name="cancer">
            <option <?= ($historia['cancer'] == 'Sí') ? 'selected' : '' ?>>Sí</option>
            <option <?= ($historia['cancer'] == 'No') ? 'selected' : '' ?>>No</option>
          </select>

          <label>Otros</label>
          <textarea name="otros"><?= htmlspecialchars($historia['otros']) ?></textarea>

          <label>Observaciones</label>
          <textarea name="observaciones"><?= htmlspecialchars($historia['observaciones']) ?></textarea>

          <div style="display:flex;gap:10px;justify-content:center;margin-top:15px;">
            <button class="btn-guardar" type="submit">Guardar cambios</button>
            <button class="btn-cancelar" type="button" onclick="toggle('formEditarHistoria', true)">Cancelar</button>
          </div>
        </form>




        <!-- Exploracion bucal -->
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
            <div style="text-align:center;margin:10px;">
              <button class="btn-modificar" onclick="toggle('formEditarExploracion')">Editar exploración bucal</button>
            </div>

            <form class="visual" id="formEditarExploracion" method="POST" style="display:none;">
              <input type="hidden" name="accion" value="editar_exploracion">
              <input type="hidden" name="id_exploracion" value="<?= $exp['id_exploracion'] ?>">
              <input type="hidden" name="id_paciente" value="<?= $id_paciente_sel ?>">

              <label>¿Dónde hay dolor?</label><input name="dolor_donde" value="<?= htmlspecialchars($exp['dolor_donde']) ?>">
              <label>¿Se calma?</label><input name="calma" value="<?= htmlspecialchars($exp['calma']) ?>">
              <label>¿Con qué?</label><input name="con_que" value="<?= htmlspecialchars($exp['con_que']) ?>">
              <label>Última visita</label><input type="date" name="ultima_visita" value="<?= htmlspecialchars($exp['ultima_visita']) ?>">
              <label>¿Sangrado de encías?</label><input name="sangrado_encias" value="<?= htmlspecialchars($exp['sangrado_encias']) ?>">
              <label>¿Cuándo?</label><input name="sangrado_cuando" value="<?= htmlspecialchars($exp['sangrado_cuando']) ?>">
              <label>¿Movilidad dental?</label><input name="movilidad" value="<?= htmlspecialchars($exp['movilidad']) ?>">
              <label>Índice de placa</label><input name="indice_placa" value="<?= htmlspecialchars($exp['indice_placa']) ?>">
              <label>Higiene</label><input name="higiene" value="<?= htmlspecialchars($exp['higiene']) ?>">
              <label>Estado bucal general</label><textarea name="estado_bucal"><?= htmlspecialchars($exp['estado_bucal']) ?></textarea>
              <label>Diagnóstico</label><textarea name="diagnostico"><?= htmlspecialchars($exp['diagnostico']) ?></textarea>
              <label>Plan de tratamiento</label><textarea name="plan_tratamiento"><?= htmlspecialchars($exp['plan_tratamiento']) ?></textarea>
              <label>Observaciones</label><textarea name="observaciones"><?= htmlspecialchars($exp['observaciones']) ?></textarea>

              <div style="display:flex;gap:10px;justify-content:center;margin-top:15px;">
                <button class="btn-guardar" type="submit">Guardar cambios</button>
                <button class="btn-cancelar" type="button" onclick="toggle('formEditarExploracion', true)">Cancelar</button>
              </div>
            </form>


          <?php else: ?>
            <!-- Si no hay exploración registrada -->
            <div style="text-align:center;margin:20px 0;">
              <p style="color:#555;">Este paciente no tiene una exploración bucal registrada.</p>
              <button class="btn-modificar" onclick="toggle('formExploracion')">Agregar exploración bucal</button>
            </div>

            <!-- Formulario nueva exploracion -->
            <form class="visual" id="formExploracion" method="POST" autocomplete="off" style="display:none;margin-top:20px;">
              <div class="section-title">Registrar Exploración Bucal</div>
              <input type="hidden" name="accion" value="guardar_exploracion">
              <input type="hidden" name="id_historia" value="<?= $historia['id_historia'] ?>">

              <label>¿Dónde hay dolor?</label><input name="dolor_donde">
              <label>¿Se calma?</label><select name="calma">
                <option>No</option>
                <option>Sí</option>
              </select>
              <label>¿Con qué?</label><input name="con_que">
              <label>Última visita al dentista</label><input type="date" name="ultima_visita">
              <label>¿Sangrado de encías?</label><select name="sangrado_encias">
                <option>No</option>
                <option>Sí</option>
              </select>
              <label>¿Cuándo?</label><input name="sangrado_cuando">
              <label>¿Movilidad dental?</label><select name="movilidad">
                <option>No</option>
                <option>Sí</option>
              </select>
              <label>Índice de placa</label><input name="indice_placa">
              <label>Higiene</label><select name="higiene">
                <option>Muy buena</option>
                <option>Buena</option>
                <option>Regular</option>
                <option>Mala</option>
              </select>
              <label>¿Manchas?</label><select name="manchas">
                <option>No</option>
                <option>Sí</option>
              </select>
              <label>Descripción de manchas</label><input name="manchas_desc">
              <label>¿Golpe en dientes?</label><select name="golpe">
                <option>No</option>
                <option>Sí</option>
              </select>
              <label>¿Fractura?</label><select name="fractura">
                <option>No</option>
                <option>Sí</option>
              </select>
              <label>¿Cuál diente?</label><input name="cual_diente">
              <label>¿Tratamiento previo?</label><input name="tratamiento_diente">
              <label>¿Dificultad para abrir la boca?</label><input name="dificultad_abrir">
              <label>¿Sarro?</label><select name="sarro">
                <option>No</option>
                <option>Sí</option>
              </select>
              <label>¿Enfermedad periodontal?</label><select name="periodontal">
                <option>No</option>
                <option>Sí</option>
              </select>
              <label>Estado bucal general</label><textarea name="estado_bucal"></textarea>
              <label>Diagnóstico</label><textarea name="diagnostico"></textarea>
              <label>Plan de tratamiento</label><textarea name="plan_tratamiento"></textarea>
              <label>Observaciones</label><textarea name="observaciones"></textarea>

              <div class="buttons" style="display:flex;gap:10px;justify-content:center;margin-top:20px;">
                <button type="submit" class="btn-guardar">Guardar</button>
                <button type="button" class="btn-cancelar" onclick="toggle('formExploracion', true)">Cancelar</button>
              </div>
            </form>
        <?php endif;
        } ?>


        <!--Expedientes-->
        <h3 style="color:#1d3557;">Archivos</h3>
        <div class="tabla-inventario">
          <table>
            <tr>
              <th>ID</th>
              <th>Descripción</th>
              <th>Fecha</th>
              <th>Archivo</th>
              <th>Acción</th>
            </tr>
            <?php if ($expedientes && $expedientes->num_rows > 0): while ($e = $expedientes->fetch_assoc()): ?>
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
              <?php endwhile;
            else: ?>
              <tr>
                <td colspan="5" style="text-align:center;">Sin archivos registrados</td>
              </tr>
            <?php endif; ?>
          </table>
        </div>

        <!--Nuevo expediente -->
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
              <button type="button" class="btn-cancelar" onclick="toggle('formExpediente', true)">Cancelar</button>
            </div>
          </form>
        </div>

        <!-- Citas-->
        <h3 style="color:#1d3557;">Historial de citas</h3>
        <div class="tabla-inventario">
          <table>
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Hora</th>
              <th>Tratamiento</th>
              <th>Estado</th>
            </tr>
            <?php if ($citas && $citas->num_rows > 0): while ($c = $citas->fetch_assoc()): ?>
                <tr>
                  <td><?= $c['id_cita'] ?></td>
                  <td><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
                  <td><?= date('g:i A', strtotime($c['hora'])) ?></td>
                  <td><?= htmlspecialchars($c['nombre_tratamiento'] ?? '—') ?></td>
                  <td><?= ucfirst($c['estado']) ?></td>
                </tr>
              <?php endwhile;
            else: ?>
              <tr>
                <td colspan="5" style="text-align:center;">Sin citas registradas</td>
              </tr>
            <?php endif; ?>
          </table>
        </div>

        <!-- Pagos -->
        <h3 style="color:#1d3557;">Historial de pagos</h3>
        <div class="tabla-inventario">
          <table>
            <tr>
              <th>ID</th>
              <th>Fecha</th>
              <th>Tratamiento</th>
              <th>Método</th>
              <th>Monto</th>
              <th>Cita</th>
            </tr>
            <?php if ($pagos && $pagos->num_rows > 0): while ($p = $pagos->fetch_assoc()): ?>
                <tr>
                  <td><?= $p['id_pago'] ?></td>
                  <td><?= date('d/m/Y', strtotime($p['fecha_pago'])) ?></td>
                  <td><?= htmlspecialchars($p['tratamiento'] ?? '—') ?></td>
                  <td><?= htmlspecialchars($p['metodo_pago']) ?></td>
                  <td>$<?= number_format($p['monto'], 2) ?></td>
                  <td><?= $p['id_cita'] ? '#' . $p['id_cita'] : '—' ?></td>
                </tr>
              <?php endwhile;
            else: ?>
              <tr>
                <td colspan="6" style="text-align:center;">Sin pagos registrados</td>
              </tr>
            <?php endif; ?>
          </table>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <script>
    // Control de formularios: solo se cierran con "Cancelar"
    function toggle(id, forceClose = false) {
      const el = document.getElementById(id);
      if (!el) return;

      // Si se presiona Cancelar fuerza el cierre
      if (forceClose) {
        el.style.display = 'none';
        return;
      }

      // Si ya está visible y se presiona el mismo botón no hara nada
      if (el.style.display === 'block') return;

      // Si estaba oculto muestra y hace scroll suave
      el.style.display = 'block';
      window.scrollTo({
        top: el.offsetTop - 100,
        behavior: 'smooth'
      });
    }

    // Evita volver atrás en el navegador
    (function() {
      window.history.pushState(null, "", window.location.href);
      window.onpopstate = function() {
        window.history.pushState(null, "", window.location.href);
      };
    })();
  </script>


  <?php $conexion->close(); ?>
</body>

</html>