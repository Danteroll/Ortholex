<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Historia Clínica — Ortholex</title>
  <link rel="stylesheet" href="css/form_paciente.css">
</head>
<body>

<div class="form-container">
  <div class="header">
    <h1>Historia Clínica Nueva</h1>
    <p>Consultorio Dental Ortholex</p>
  </div>

  <form method="POST" action="guardar_historia.php" class="historia-form">

    <!-- 🧾 DATOS GENERALES -->
    <section>
      <h2>Datos del Paciente</h2>

      <div class="input-group"><label>Nombre completo:</label><input type="text" name="nombre" required></div>

      <div class="input-row">
        <div class="input-group"><label>Fecha de nacimiento:</label><input type="date" name="fecha_nacimiento"></div>
        <div class="input-group"><label>Edad:</label><input type="number" name="edad" min="0"></div>
        <div class="input-group"><label>Sexo:</label>
          <select name="sexo"><option value="">Seleccione...</option><option>Femenino</option><option>Masculino</option></select>
        </div>
      </div>

      <div class="input-group"><label>Dirección:</label><input type="text" name="direccion"></div>

      <div class="input-row">
        <div class="input-group"><label>Teléfono / Celular:</label><input type="text" name="telefono"></div>
        <div class="input-group"><label>Estado civil:</label><input type="text" name="estado_civil"></div>
      </div>

      <div class="input-row">
        <div class="input-group"><label>Nacionalidad:</label><input type="text" name="nacionalidad"></div>
        <div class="input-group"><label>Ocupación:</label><input type="text" name="ocupacion"></div>
      </div>

      <div class="input-group"><label>Nombre del titular:</label><input type="text" name="titular"></div>
      <div class="input-group"><label>Contacto de emergencia (nombre y teléfono):</label><input type="text" name="contacto_emergencia"></div>
    </section>

    <!-- ⚕️ MOTIVO DE CONSULTA -->
    <section>
      <h2>Motivo de Consulta</h2>

      <div class="input-group"><label>Motivo principal:</label><textarea name="motivo_consulta" rows="2"></textarea></div>

      <div class="input-row">
        <div class="input-group"><label>¿Ha tenido dolor?</label>
          <select name="ha_tenido_dolor">
            <option value="">Seleccione...</option><option>Suave</option><option>Moderado</option>
            <option>Intenso</option><option>Temporario</option><option>Intermitente</option>
            <option>Continuo</option><option>Espontáneo</option><option>Provocado</option>
            <option>Frío</option><option>Calor</option><option>Localizado</option>
          </select>
        </div>
        <div class="input-group"><label>¿Dónde?</label><input type="text" name="dolor_donde"></div>
      </div>

      <div class="input-row">
        <div class="input-group"><label>¿Puede calmarlo con algo?</label><input type="text" name="dolor_calma"></div>
        <div class="input-group"><label>¿Con qué?</label><input type="text" name="dolor_con_que"></div>
      </div>

      <div class="input-group"><label>Última visita al dentista:</label><input type="text" name="ultima_visita"></div>
    </section>

    <!-- 🩺 ANTECEDENTES MÉDICOS -->
    <section>
      <h2>Antecedentes Médicos</h2>
      <div class="input-row">
        <div class="input-group">
          <label>¿Sufre alguna enfermedad?</label>
          <select name="enfermedad_si_no">
            <option value="">Seleccione...</option>
            <option>SI</option>
            <option>NO</option>
          </select>
        </div>

        <div class="input-group">
          <label>¿De qué?</label>
          <input type="text" name="enfermedad_detalle" placeholder="Especifique si respondió 'Sí'">
        </div>
      </div>
      <div class="input-group"><label>Medicamentos habituales:</label><textarea name="medicamentos" rows="2"></textarea></div>
      <div class="input-group"><label>¿Es alérgico a alguna droga o alimento?</label><textarea name="alergias" rows="2"></textarea></div>

      <h3>Enfermedades (marque y describa tratamiento si aplica)</h3>
      <div class="tabla-inventario">
        <table>
          <tr><th>Enfermedad</th><th>SI</th><th>NO</th><th>Tratamiento</th></tr>
          <?php
          $enfermedades = ["Diabetes","Hipertensión arterial","Epilepsia","Infarto","Anemia","Asma","Hepatitis","Hipotiroidismo / Hipertiroidismo",
          "Angina de pecho","Tuberculosis","Insuficiencia renal","Enfermedades venéreas","H.I.V / SIDA","Gastritis","Embarazo","COVID","Cáncer"];
          foreach($enfermedades as $e){
            $id = strtolower(str_replace([' ','/'],'_',$e));
            echo "
              <tr>
                <td>$e</td>
                <td><input type='radio' name='$id' value='SI'></td>
                <td><input type='radio' name='$id' value='NO'></td>
                <td><input type='text' name='tratamiento_$id'></td>
              </tr>";
          }
          ?>
        </table>
      </div>

      <div class="input-group"><label>Otros padecimientos:</label><textarea name="otros_enfermedades" rows="2"></textarea></div>

      <div class="input-row">
        <div class="input-group"><label>¿Tuvo transfusiones?</label><select name="transfusiones"><option>SI</option><option>NO</option></select></div>
        <div class="input-group"><label>¿Fue operado alguna vez?</label><select name="operado"><option>SI</option><option>NO</option></select></div>
      </div>

      <div class="input-row">
        <div class="input-group"><label>¿De qué?</label><input type="text" name="operado_de"></div>
        <div class="input-group"><label>¿Cuándo?</label><input type="text" name="operado_cuando"></div>
      </div>

      <div class="input-row">
        <div class="input-group"><label>¿Fuma?</label><select name="fuma"><option>SI</option><option>NO</option></select></div>
        <div class="input-group"><label>¿Toma?</label><select name="toma"><option>SI</option><option>NO</option></select></div>
        <div class="input-group"><label>¿Consume drogas?</label><select name="drogas"><option>SI</option><option>NO</option></select></div>
      </div>
    </section>

    <!-- 🦷 HISTORIA ODONTOLÓGICA -->
    <section>
      <h2>Historia Odontológica</h2>

      <div class="input-row">
        <div class="input-group"><label>¿Le sangran las encías?</label><select name="encias"><option>SI</option><option>NO</option></select></div>
        <div class="input-group"><label>¿Tiene movilidad en los dientes?</label><select name="movilidad"><option>SI</option><option>NO</option></select></div>
      </div>

      <div class="input-row">
        <div class="input-group"><label>Índice de placa:</label><input type="text" name="placa"></div>
        <div class="input-group"><label>Estado de higiene bucal:</label>
          <select name="higiene">
            <option>Muy bueno</option><option>Bueno</option><option>Deficiente</option><option>Malo</option>
          </select>
        </div>
      </div>

      <div class="input-row">
        <div class="input-group"><label>¿Manchas en dientes o encías?</label><select name="manchas"><option>SI</option><option>NO</option></select></div>
        <div class="input-group"><label>¿Sufrió algún golpe en los dientes?</label><select name="golpe"><option>SI</option><option>NO</option></select></div>
      </div>

      <div class="input-row">
        <div class="input-group"><label>¿Se fracturó algún diente?</label><select name="fractura"><option>SI</option><option>NO</option></select></div>
        <div class="input-group"><label>¿Cuál?</label><input type="text" name="fractura_cual"></div>
      </div>

      <div class="input-group"><label>¿Recibió tratamiento?</label><input type="text" name="tratamiento_fractura"></div>
      <div class="input-group"><label>¿Dificultad para abrir la boca?</label><input type="text" name="dificultad_boca"></div>

      <div class="input-row">
        <div class="input-group"><label>Encía:</label><input type="text" name="encia"></div>
        <div class="input-group"><label>Lengua:</label><input type="text" name="lengua"></div>
      </div>

      <div class="input-row">
        <div class="input-group"><label>Paladar:</label><input type="text" name="paladar"></div>
        <div class="input-group"><label>Piso de boca:</label><input type="text" name="piso_boca"></div>
      </div>

      <div class="input-row">
        <div class="input-group"><label>Carrillos:</label><input type="text" name="carrillos"></div>
        <div class="input-group"><label>Rebordes:</label><input type="text" name="rebordes"></div>
      </div>
    </section>

    <!-- 📋 EVALUACIÓN CLÍNICA -->
    <section>
      <h2>Evaluación Clínica</h2>

      <div class="input-row">
        <div class="input-group"><label>Presencia de sarro:</label><select name="sarro"><option>SI</option><option>NO</option></select></div>
        <div class="input-group"><label>Enfermedad periodontal:</label><select name="periodontal"><option>SI</option><option>NO</option></select></div>
      </div>

      <div class="input-group"><label>Diagnóstico presuntivo:</label><textarea name="diagnostico" rows="3"></textarea></div>
      <div class="input-group"><label>Plan de tratamiento:</label><textarea name="plan_tratamiento" rows="3"></textarea></div>
      <div class="input-group"><label>Fecha:</label><input type="date" name="fecha"></div>
      <div class="input-group"><label>Observaciones:</label><textarea name="observaciones" rows="3"></textarea></div>
    </section>

    <!-- ✍️ FIRMAS -->
    <section>
      <h2>Autorización y Firmas</h2>

      <div class="input-row">
        <div class="input-group"><label>Firma del paciente:</label><input type="text" name="firma_paciente"></div>
        <div class="input-group"><label>Padre o tutor:</label><input type="text" name="padre_tutor"></div>
        <div class="input-group"><label>Firma del dentista:</label><input type="text" name="firma_dentista"></div>
      </div>
    </section>

<div class="buttons">
  <button type="submit" class="btn-guardar">Guardar Historia</button>
  <button type="button" class="btn-cancelar" onclick="window.history.back()">Cancelar</button>
</div>


  </form>
</div>

</body>
</html>


