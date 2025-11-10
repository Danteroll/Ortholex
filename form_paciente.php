<?php
include("conexion.php");
date_default_timezone_set('America/Mexico_City');

// 📝 Registrar paciente + historia clínica
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_paciente'])) {

  // --- Insertar paciente ---
  $stmt = $conexion->prepare("
    INSERT INTO pacientes (nombre, fecha_nacimiento, edad, celular, estado_civil, nacionalidad, domicilio, profesion, contacto_emergencia, telefono_emergencia, estado_registro)
    VALUES (?, ?, NULL, ?, ?, ?, ?, ?, ?, ?, 'Paciente')
  ");
  $stmt->bind_param("sssssssss",
    $_POST['nombre'], $_POST['fecha_nacimiento'], $_POST['celular'],
    $_POST['estado_civil'], $_POST['nacionalidad'], $_POST['domicilio'],
    $_POST['profesion'], $_POST['contacto_emergencia'], $_POST['telefono_emergencia']
  );

  if (!$stmt->execute()) {
    echo "<script>alert('Error al registrar paciente: ".$conexion->error."');</script>";
    exit;
  }
  $id_paciente = $stmt->insert_id;
  $stmt->close();

  // --- Guardar firma ---
  $firma_guardada = null;
  if (!empty($_POST['firma_base64'])) {
    $firmaData = $_POST['firma_base64'];
    $firmaData = str_replace('data:image/png;base64,', '', $firmaData);
    $firmaData = str_replace(' ', '+', $firmaData);
    $imagenBinaria = base64_decode($firmaData);
    $nombreSeguro = hash('sha256', uniqid('', true)) . '.png';
    $rutaFirma = 'firmas/' . $nombreSeguro;
    if (!is_dir('firmas')) mkdir('firmas', 0755, true);
    file_put_contents($rutaFirma, $imagenBinaria);
    $firma_guardada = $rutaFirma;
  }

  // --- Insertar historia clínica ---
  $sql = "
    INSERT INTO historia_clinica (
      id_paciente, lugar, fecha, motivo_consulta, enf_general, 
      enf_cual, medicamentos, alergias, transfusiones, operado, 
      operado_deque, operado_cuando, fuma, toma, drogas,
      diabetes, hipertension, epilepsia, infarto, anemia, 
      asma, hepatitis, tiroides, angina_pecho, tuberculosis,
      renal, venereas, vih, gastritis, embarazo, 
      covid, cancer, otros, observaciones, firma_paciente
    )
    VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?)
  ";

  $stmt2 = $conexion->prepare($sql);
  $stmt2->bind_param("issssssssssssssssssssssssssssssssss",
    $id_paciente, $_POST['lugar'], $_POST['fecha'], $_POST['motivo_consulta'],
    $_POST['enf_general'], $_POST['enf_cual'], $_POST['medicamentos'], $_POST['alergias'], $_POST['transfusiones'],
    $_POST['operado'], $_POST['operado_deque'], $_POST['operado_cuando'], $_POST['fuma'], $_POST['toma'], $_POST['drogas'],
    $_POST['diabetes'], $_POST['hipertension'], $_POST['epilepsia'], $_POST['infarto'], $_POST['anemia'], $_POST['asma'],
    $_POST['hepatitis'], $_POST['tiroides'], $_POST['angina_pecho'], $_POST['tuberculosis'],
    $_POST['renal'], $_POST['venereas'], $_POST['vih'], $_POST['gastritis'], $_POST['embarazo'], $_POST['covid'], $_POST['cancer'],
    $_POST['otros'], $_POST['observaciones'], $firma_guardada
  );
  $stmt2->execute();
  $stmt2->close();

  echo "<script>alert('✅ Registro completado correctamente.'); window.location='registro_paciente.php';</script>";
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
    button {
      background: #a16976;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      width: 100%;
      margin-top: 15px;
      cursor: pointer;
    }
    button:hover { background: #8b5564; }
    canvas {
      border: 1px solid #ccc;
      border-radius: 8px;
      width: 100%;
      height: 200px;
      touch-action: none;
    }
    .firma-buttons {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }
    .btn-secundario {
      flex: 1;
      background: #999;
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
      <div class="input-group"><label>Celular</label><input name="celular"></div>
      <div class="input-group">
        <label>Estado civil</label>
        <select name="estado_civil"><option>Soltero</option><option>Casado</option><option>Divorciado</option><option>Viudo</option></select>
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

      <div class="section-title">Firma del paciente</div>
      <canvas id="canvasFirma"></canvas>
      <div class="firma-buttons">
        <button type="button" class="btn-secundario" onclick="limpiarFirma()">Borrar</button>
        <button type="button" onclick="guardarFirma()">Guardar firma</button>
      </div>
      <input type="hidden" name="firma_base64" id="firma_base64">

      <button type="submit" name="registrar_paciente">Enviar registro</button>
    </form>

    <footer>Ortholex Dental — Registro de pacientes © <?= date('Y') ?></footer>
  </div>

  <script>
  const canvas = document.getElementById("canvasFirma");
  const ctx = canvas.getContext("2d");
  let dibujando = false;
  function getPos(e){
    const rect = canvas.getBoundingClientRect();
    if(e.touches && e.touches[0]) return {x:e.touches[0].clientX-rect.left, y:e.touches[0].clientY-rect.top};
    else return {x:e.clientX-rect.left, y:e.clientY-rect.top};
  }
  function empezar(e){ dibujando=true; const p=getPos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); e.preventDefault(); }
  function dibujar(e){ if(!dibujando) return; const p=getPos(e); ctx.lineWidth=2; ctx.lineCap="round"; ctx.strokeStyle="#000"; ctx.lineTo(p.x,p.y); ctx.stroke(); e.preventDefault(); }
  function terminar(){ dibujando=false; ctx.closePath(); }
  canvas.addEventListener("mousedown",empezar);
  canvas.addEventListener("mousemove",dibujar);
  canvas.addEventListener("mouseup",terminar);
  canvas.addEventListener("mouseleave",terminar);
  canvas.addEventListener("touchstart",empezar);
  canvas.addEventListener("touchmove",dibujar);
  canvas.addEventListener("touchend",terminar);
  function limpiarFirma(){ ctx.clearRect(0,0,canvas.width,canvas.height); }
  function guardarFirma(){ document.getElementById("firma_base64").value = canvas.toDataURL("image/png"); alert("Firma guardada ✅"); }
  </script>
</body>
</html>
