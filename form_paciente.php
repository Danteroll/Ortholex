<?php
include("conexion.php");
date_default_timezone_set('America/Mexico_City');

// Registra paciente + historia clínica
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_paciente'])) {

  // --- Inserta un paciente ---
  $stmt = $conexion->prepare("
    INSERT INTO pacientes (nombre, fecha_nacimiento, celular, estado_civil, nacionalidad, domicilio, profesion, contacto_emergencia, telefono_emergencia, fecha_registro)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
  ");
  $stmt->bind_param(
    "sssssssss",
    $_POST['nombre'],
    $_POST['fecha_nacimiento'],
    $_POST['celular'],
    $_POST['estado_civil'],
    $_POST['nacionalidad'],
    $_POST['domicilio'],
    $_POST['profesion'],
    $_POST['contacto_emergencia'],
    $_POST['telefono_emergencia']
  );
  $stmt->execute();
  $id_paciente = $stmt->insert_id;
  $stmt->close();

  // --- Inserta la historia clínica ---
  $sql = "
    INSERT INTO historia_clinica (
      id_paciente, lugar, fecha, motivo_consulta, enf_general, 
      enf_cual, medicamentos, alergias, transfusiones, operado, 
      operado_deque, operado_cuando, fuma, toma, drogas, 
      diabetes, hipertension, epilepsia, infarto, anemia, 
      asma, hepatitis, tiroides, angina_pecho, tuberculosis, 
      renal, venereas, vih, gastritis, embarazo, 
      covid, cancer, otros, observaciones, fecha_registro
    ) VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,NOW())
  ";

  $stmt2 = $conexion->prepare($sql);
  $stmt2->bind_param(
    "isssssssssssssssssssssssssssssssss",
    $id_paciente,
    $_POST['lugar'],
    $_POST['fecha'],
    $_POST['motivo_consulta'],
    $_POST['enf_general'],
    $_POST['enf_cual'],
    $_POST['medicamentos'],
    $_POST['alergias'],
    $_POST['transfusiones'],
    $_POST['operado'],
    $_POST['operado_deque'],
    $_POST['operado_cuando'],
    $_POST['fuma'],
    $_POST['toma'],
    $_POST['drogas'],
    $_POST['diabetes'],
    $_POST['hipertension'],
    $_POST['epilepsia'],
    $_POST['infarto'],
    $_POST['anemia'],
    $_POST['asma'],
    $_POST['hepatitis'],
    $_POST['tiroides'],
    $_POST['angina_pecho'],
    $_POST['tuberculosis'],
    $_POST['renal'],
    $_POST['venereas'],
    $_POST['vih'],
    $_POST['gastritis'],
    $_POST['embarazo'],
    $_POST['covid'],
    $_POST['cancer'],
    $_POST['otros'],
    $_POST['observaciones']
  );
  $stmt2->execute();
  $stmt2->close();

  echo "<script>alert('Registro completado correctamente.'); window.location='form_cita.php';</script>";
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
      max-width: 800px;
      margin: auto;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

    input,
    select,
    textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      font-family: inherit;
      transition: border-color 0.3s;
    }

    input:focus,
    select:focus,
    textarea:focus {
      border-color: #a16976;
      outline: none;
      box-shadow: 0 0 4px rgba(161, 105, 118, 0.3);
    }

    textarea {
      resize: vertical;
      min-height: 60px;
    }

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

    button:hover {
      background: #8b5564;
      transform: scale(1.03);
    }

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
      <div class="input-group"><label>Celular</label><input type="tel" name="celular" maxlength="10" pattern="[0-9]{10}" title="Debe contener 10 dígitos" required></div>
      <div class="input-group"><label>Estado civil</label>
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
      <div class="input-group"><label>Teléfono de emergencia</label><input type="tel" name="telefono_emergencia" maxlength="10" pattern="[0-9]{10}" required></div>

      <div class="section-title">Historia clínica (autorreporte)</div>
      <div class="input-group"><label>Lugar</label><input name="lugar"></div>
      <div class="input-group"><label>Fecha</label><input type="date" name="fecha" value="<?= date('Y-m-d') ?>"></div>
      <div class="input-group"><label>Motivo de consulta</label><textarea name="motivo_consulta"></textarea></div>

      <div class="input-group"><label>¿Sufre alguna enfermedad?</label><select name="enf_general">
          <option>No</option>
          <option>Sí</option>
        </select></div>
      <div class="input-group"><label>¿Cuál?</label><textarea name="enf_cual"></textarea></div>
      <div class="input-group"><label>Medicamentos</label><textarea name="medicamentos"></textarea></div>
      <div class="input-group"><label>Alergias</label><textarea name="alergias"></textarea></div>
      <div class="input-group"><label>Transfusiones</label><select name="transfusiones">
          <option>No</option>
          <option>Sí</option>
        </select></div>
      <div class="input-group"><label>Operado</label><select name="operado">
          <option>No</option>
          <option>Sí</option>
        </select></div>
      <div class="input-group"><label>¿De qué?</label><input name="operado_deque"></div>
      <div class="input-group"><label>¿Cuándo?</label><input type="date" name="operado_cuando"></div>
      <div class="input-group"><label>Fuma</label><select name="fuma">
          <option>No</option>
          <option>Sí</option>
        </select></div>
      <div class="input-group"><label>Toma</label><select name="toma">
          <option>No</option>
          <option>Sí</option>
        </select></div>
      <div class="input-group"><label>Drogas</label><select name="drogas">
          <option>No</option>
          <option>Sí</option>
        </select></div>

      <?php
      $enfs = ['diabetes', 'hipertension', 'epilepsia', 'infarto', 'anemia', 'asma', 'hepatitis', 'tiroides', 'angina_pecho', 'tuberculosis', 'renal', 'venereas', 'VIH', 'gastritis', 'embarazo', 'covid', 'cancer'];
      foreach ($enfs as $e) {
        echo "<div class='input-group'>
                  <label>" . ucfirst(str_replace('_', ' ', $e)) . "</label>
                  <select name='{$e}'><option>No</option><option>Sí</option></select>
                </div>";
      }
      ?>
      <div class="input-group"><label>Otros</label><textarea name="otros"></textarea></div>
      <div class="input-group"><label>Observaciones</label><textarea name="observaciones"></textarea></div>

      <div class="buttons">
        <button type="submit" name="registrar_paciente">Enviar registro</button>
        <button type="button" onclick="window.history.back()">Cancelar</button>
      </div>
    </form>

    <footer>Ortholex — Registro de pacientes © <?= date('Y') ?></footer>
  </div>
</body>

</html>