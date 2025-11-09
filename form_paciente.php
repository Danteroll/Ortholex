<?php include 'conexion.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head><link rel="stylesheet" href="css/form_paciente.css">
  <meta charset="utf-8"><title>Registro de Paciente</title></head>
<body>
  <h2>Registro de Paciente</h2>
  <form action="funciones/guardar_paciente.php" method="post">
    <label>Nombre*</label><input name="nombre" required>
    <label>Fecha de nacimiento</label><input type="date" name="fecha_nacimiento">
    <label>Celular</label><input name="celular">
    <label>Estado civil</label>
    <select name="estado_civil">
      <option>Soltero</option><option>Casado</option><option>Divorciado</option><option>Viudo</option><option>Otro</option>
    </select>
    <label>Nacionalidad</label><input name="nacionalidad">
    <label>Domicilio</label><textarea name="domicilio"></textarea>
    <label>Profesión</label><input name="profesion">
    <label>Contacto de emergencia</label><input name="contacto_emergencia">
    <label>Teléfono de emergencia</label><input name="telefono_emergencia">

    <hr>
    <h3>Historia clínica (autorreporte)</h3>
    <label>Lugar</label><input name="lugar">
    <label>Fecha</label><input type="date" name="fecha">
    <label>Motivo de consulta</label><textarea name="motivo_consulta"></textarea>

    <label>¿Sufre alguna enfermedad?</label>
    <select name="enf_general"><option>No</option><option>Sí</option></select>
    <label>¿Cuál?</label><textarea name="enf_cual"></textarea>
    <label>Medicamentos</label><textarea name="medicamentos"></textarea>
    <label>Alergias</label><textarea name="alergias"></textarea>
    <label>Transfusiones</label><select name="transfusiones"><option>No</option><option>Sí</option></select>
    <label>Operado</label><select name="operado"><option>No</option><option>Sí</option></select>
    <label>¿De qué?</label><input name="operado_deque">
    <label>¿Cuándo?</label><input type="date" name="operado_cuando">
    <label>Fuma</label><select name="fuma"><option>No</option><option>Sí</option></select>
    <label>Toma</label><select name="toma"><option>No</option><option>Sí</option></select>
    <label>Drogas</label><select name="drogas"><option>No</option><option>Sí</option></select>

    <!-- Enfermedades comunes (Sí/No) -->
    <?php
      $enfs = ['diabetes','hipertension','epilepsia','infarto','anemia','asma','hepatitis','tiroides',
               'angina_pecho','tuberculosis','renal','venereas','vih','gastritis','embarazo','covid','cancer'];
      foreach ($enfs as $e) {
        echo "<label>".ucfirst(str_replace('_',' ',$e))."</label>
              <select name='{$e}'><option>No</option><option>Sí</option></select>";
      }
    ?>
    <label>Otros</label><textarea name="otros"></textarea>

    <label>Observaciones</label><textarea name="observaciones"></textarea>
    <label>Firma del paciente</label><input name="firma_paciente">

    <input type="hidden" name="estado_registro" value="Paciente">
    <button type="submit">Enviar</button>
  </form>
</body>
</html>
