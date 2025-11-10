<?php
include("conexion.php");
date_default_timezone_set('America/Mexico_City');

// 📝 Registrar paciente + historia clínica
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_paciente'])) {

  // --- Insertar paciente ---
  $stmt = $conexion->prepare("
    INSERT INTO pacientes (nombre, fecha_nacimiento, celular, estado_civil, nacionalidad, domicilio, profesion, contacto_emergencia, telefono_emergencia)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");
  if (!$stmt) {
    die("Error en prepare(): " . $conexion->error);
  }

  $stmt->bind_param("sssssssss",
    $_POST['nombre'], $_POST['fecha_nacimiento'], $_POST['celular'],
    $_POST['estado_civil'], $_POST['nacionalidad'], $_POST['domicilio'],
    $_POST['profesion'], $_POST['contacto_emergencia'], $_POST['telefono_emergencia']
  );

  if (!$stmt->execute()) {
    echo "<script>alert('❌ Error al registrar paciente: ".$conexion->error."');</script>";
    exit;
  }

  $id_paciente = $stmt->insert_id;
  $stmt->close();

  // --- Insertar historia clínica ---
  $sql = "
    INSERT INTO historia_clinica (
      id_paciente, lugar, fecha, motivo_consulta, enf_general, 
      enf_cual, medicamentos, alergias, transfusiones, operado, 
      operado_deque, operado_cuando, fuma, toma, drogas,
      diabetes, hipertension, epilepsia, infarto, anemia, 
      asma, hepatitis, tiroides, angina_pecho, tuberculosis,
      renal, venereas, vih, gastritis, embarazo, 
      covid, cancer, otros, observaciones
    )
    VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?)
  ";

  $stmt2 = $conexion->prepare($sql);
  if (!$stmt2) {
    die("Error en prepare historia_clinica: " . $conexion->error);
  }

  $stmt2->bind_param("issssssssssssssssssssssssssssssss",
    $id_paciente, $_POST['lugar'], $_POST['fecha'], $_POST['motivo_consulta'],
    $_POST['enf_general'], $_POST['enf_cual'], $_POST['medicamentos'], $_POST['alergias'], $_POST['transfusiones'],
    $_POST['operado'], $_POST['operado_deque'], $_POST['operado_cuando'], $_POST['fuma'], $_POST['toma'], $_POST['drogas'],
    $_POST['diabetes'], $_POST['hipertension'], $_POST['epilepsia'], $_POST['infarto'], $_POST['anemia'], $_POST['asma'],
    $_POST['hepatitis'], $_POST['tiroides'], $_POST['angina_pecho'], $_POST['tuberculosis'],
    $_POST['renal'], $_POST['venereas'], $_POST['vih'], $_POST['gastritis'], $_POST['embarazo'], $_POST['covid'], $_POST['cancer'],
    $_POST['otros'], $_POST['observaciones']
  );
  $stmt2->execute();
  $stmt2->close();

  // ✅ Redirige a la página anterior
  echo "<script>alert('✅ Registro completado correctamente.'); window.history.back();</script>";
  exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Paciente — Ortholex</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f3f4f8;
      margin: 0;
      padding: 20px;
    }
    .container {
      background: #fff;
      max-width: 700px;
      margin: auto;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #a16976;
      text-align: center;
      margin-bottom: 10px;
    }
    p.desc {
      text-align: center;
      color: #555;
      font-size: 15px;
    }
    .input-group {
      margin-bottom: 12px;
    }
    label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      font-family: inherit;
      transition: border-color 0.3s;
    }
    input:focus, select:focus, textarea:focus {
      border-color: #a16976;
      outline: none;
      box-shadow: 0 0 4px rgba(161,105,118,0.3);
    }
    textarea { resize: vertical; min-height: 60px; }
    .section-title {
      margin-top: 20px;
      color: #a16976;
      font-size: 18px;
      border-bottom: 1px solid #a16976;
      padding-bottom: 4px;
    }
    .buttons {
      display: flex;
      gap: 10px;
      margin-top: 20px;
    }
    button {
      flex: 1;
      background: #a16976;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      font-weight: bold;
      transition: background 0.3s, transform 0.2s;
    }
    button:hover { background: #8b5564; transform: scale(1.03); }
    footer {
      text-align: center;
      color: #777;
      margin-top: 30px;
      font-size: 13px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Registro de Paciente</h2>
    <p class="desc">Complete sus datos para crear su expediente en la clínica.</p>

    <form method="POST" autocomplete="off">
      <div class="section-title">Datos personales</div>
      <div class="input-group"><label>Nombre completo*</label><input name="nombre" required></div>
      <div class="input-group"><label>Fecha de nacimiento</label><input type="date" name="fecha_nacimiento"></div>
      <div class="input-group"><label>Celular</label><input name="celular"></div>
      <div class="input-group">
        <label>Estado civil</label>
        <select name="estado_civil">
          <option>Soltero</option>
          <option>Casado</option>
          <option>Divorciado</option>
          <option>Viudo</option>
        </select>
      </div>
      <div class="input-group"><label>Nacionalidad</label><input name="nacionalidad"></div>
      <div class="input-group"><label>Domicilio</label><textarea name="domicilio"></textarea></div>
      <div class="input-group"><label>Profesión</label><input name="profesion"></div>
      <div class="input-group"><label>Contacto de emergencia</label><input name="contacto_emergencia"></div>
      <div class="input-group"><label>Teléfono de emergencia</label><input name="telefono_emergencia"></div>

      <div class="section-title">Historia clínica (autorreporte)</div>
      <div class="input-group"><label>Lugar</label><input name="lugar"></div>
      <div class="input-group"><label>Fecha</label><input type="date" name="fecha"></div>
      <div class="input-group"><label>Motivo de consulta</label><textarea name="motivo_consulta"></textarea></div>
      <div class="input-group"><label>¿Sufre alguna enfermedad?</label><select name="enf_general"><option>No</option><option>Sí</option></select></div>
      <div class="input-group"><label>¿Cuál?</label><textarea name="enf_cual"></textarea></div>
      <div class="input-group"><label>Medicamentos</label><textarea name="medicamentos"></textarea></div>
      <div class="input-group"><label>Alergias</label><textarea name="alergias"></textarea></div>

      <!-- Botones principales -->
      <div class="buttons">
        <button type="submit" name="registrar_paciente">Enviar registro</button>
        <button type="button" onclick="window.history.back()">Cancelar</button>
      </div>
    </form>

    <footer>Ortholex Dental — Registro de pacientes © <?= date('Y') ?></footer>
  </div>
</body>
</html>



