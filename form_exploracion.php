<?php
include("conexion.php");

// Validar que venga id_historia
if (!isset($_GET['id_historia'])) {
  die("Falta el parámetro id_historia.");
}
$id_historia = intval($_GET['id_historia']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Exploración Bucal</title>
  <link rel="stylesheet" href="css/form_paciente.css">
</head>
<body>
  <h2>Exploración Bucal</h2>
  <form action="funciones/guardar_exploracion.php" method="POST">
    <input type="hidden" name="id_historia" value="<?php echo $id_historia; ?>">

    <label>¿Dónde hay dolor?</label>
    <input name="dolor_donde">

    <label>¿Se calma?</label>
    <select name="calma"><option>No</option><option>Sí</option></select>

    <label>¿Con qué?</label>
    <input name="con_que">

    <label>Última visita al dentista</label>
    <input type="date" name="ultima_visita">

    <label>¿Sangrado de encías?</label>
    <select name="sangrado_encias"><option>No</option><option>Sí</option></select>
    <label>¿Cuándo?</label>
    <input name="sangrado_cuando">

    <label>¿Movilidad dental?</label>
    <select name="movilidad"><option>No</option><option>Sí</option></select>

    <label>Índice de placa</label>
    <input name="indice_placa">

    <label>Higiene</label>
    <select name="higiene">
      <option>Muy buena</option><option>Buena</option><option>Regular</option><option>Mala</option>
    </select>

    <label>¿Manchas?</label>
    <select name="manchas"><option>No</option><option>Sí</option></select>
    <label>Descripción de manchas</label>
    <input name="manchas_desc">

    <label>¿Golpe en dientes?</label>
    <select name="golpe"><option>No</option><option>Sí</option></select>

    <label>¿Fractura?</label>
    <select name="fractura"><option>No</option><option>Sí</option></select>
    <label>¿Cuál diente?</label>
    <input name="cual_diente">

    <label>¿Tratamiento previo?</label>
    <input name="tratamiento_diente">

    <label>¿Dificultad para abrir la boca?</label>
    <input name="dificultad_abrir">

    <label>¿Sarro?</label>
    <select name="sarro"><option>No</option><option>Sí</option></select>

    <label>¿Enfermedad periodontal?</label>
    <select name="periodontal"><option>No</option><option>Sí</option></select>

    <label>Estado bucal general</label>
    <textarea name="estado_bucal"></textarea>

    <hr>
    <h3>Diagnóstico y tratamiento</h3>
    <label>Diagnóstico</label><textarea name="diagnostico"></textarea>
    <label>Plan de tratamiento</label><textarea name="plan_tratamiento"></textarea>
    <label>Observaciones</label><textarea name="observaciones"></textarea>
    <label>Firma del dentista</label><input name="firma_dentista">

    <button type="submit">Guardar exploración</button>
  </form>
</body>
</html>
