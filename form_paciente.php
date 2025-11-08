<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Historia Clínica - Formulario</title>
  <style>
    :root{--bg:#0f172a;--panel:#111827;--ink:#e5e7eb;--muted:#94a3b8;--accent:#22c55e;--danger:#ef4444;--border:#334155}
    html,body{height:100%}
    body{margin:0;background:var(--bg);color:var(--ink);font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,"Helvetica Neue",Arial}
    .wrap{max-width:1100px;margin:auto;padding:28px}
    h1{font-size:clamp(1.4rem,2.5vw,2rem);margin:0 0 18px}
    h2{font-size:1.1rem;margin:26px 0 10px;color:var(--accent)}
    fieldset{border:1px solid var(--border);border-radius:12px;padding:18px;margin:18px 0;background:var(--panel)}
    legend{padding:0 8px;color:var(--muted)}
    .grid{display:grid;gap:12px}
    .g-2{grid-template-columns:repeat(2,minmax(0,1fr))}
    .g-3{grid-template-columns:repeat(3,minmax(0,1fr))}
    .g-4{grid-template-columns:repeat(4,minmax(0,1fr))}
    .row{display:flex;gap:10px;flex-wrap:wrap}
    label{display:block;font-size:.9rem;color:var(--muted);margin-bottom:6px}
    input[type=text],input[type=date],input[type=number],input[type=tel],input[type=time],select,textarea{
      width:100%;box-sizing:border-box;border:1px solid var(--border);border-radius:10px;background:#0b1220;color:var(--ink);padding:10px 12px;font-size:.95rem
    }
    textarea{min-height:80px;resize:vertical}
    .hint{font-size:.8rem;color:var(--muted)}
    .inline{display:flex;gap:14px;align-items:center;flex-wrap:wrap}
    .chip{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);padding:6px 10px;border-radius:999px;background:#0b1220}
    .tbl{width:100%;border-collapse:collapse}
    .tbl th,.tbl td{border:1px solid var(--border);padding:8px;text-align:left;vertical-align:top}
    .tbl th{background:#0b1220;color:var(--muted)}
    .footer{display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end;margin-top:24px}
    button{appearance:none;border:0;border-radius:12px;padding:10px 14px;font-weight:600;cursor:pointer}
    .primary{background:var(--accent);color:#0b1220}
    .ghost{background:transparent;border:1px solid var(--border);color:var(--ink)}
    .danger{background:var(--danger);color:white}
    .section-title{font-weight:700;color:var(--ink)}
    .divider{height:1px;background:var(--border);margin:16px 0}
    .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
    /* === Ajustes de contraste en la tabla de enfermedades === */
#enfermedades-tabla th {
  background: #a16976;       /* tono gris-azulado medio */
  color: #f1f5f9;            /* texto claro */
}

#enfermedades-tabla td {
  background: #a16976;       /* panel oscuro igual que el fondo general */
  color: #e2e8f0;            /* texto visible */
}

#enfermedades-tabla label {
  color: #e2e8f0;            /* etiquetas claras */
  font-weight: 500;
}

#enfermedades-tabla input[type="text"] {
  background: #a16976;
  color: #f8fafc;
  border: 1px solid #a16976;
  border-radius: 8px;
}

#enfermedades-tabla input[type="radio"] {
  accent-color: #22c55e; /* color verde para el radio seleccionado */
}

/* alternar filas para mejor legibilidad */
#enfermedades-tabla tbody tr:nth-child(even) td {
  background: #a16976;
}
 
</style>
</head>
<body>
  <div class="wrap">
    <h1>Historia Clínica – Formulario</h1>
    <p class="hint">Complete todos los campos aplicables. Los campos se activan según las respuestas.</p>

    <form id="historia-form" method="POST" action="funciones/guardar_historia.php">
      <!-- 1. DATOS GENERALES -->
      <fieldset>
        <legend>Historia Clínica General</legend>

        <div class="grid g-2">
          <div>
            <label for="lugar">Lugar</label>
            <input id="lugar" name="lugar" type="text" autocomplete="address-level2"/>
          </div>
          <div>
            <label for="fecha">Fecha</label>
            <input id="fecha" name="fecha" type="date"/>
          </div>
        </div>

        <div class="grid g-3">
          <div>
            <label for="nombre">Nombre del paciente</label>
            <input id="nombre" name="nombre_paciente" type="text" required />
          </div>
          <div>
            <label for="nacimiento">Fecha de nacimiento</label>
            <input id="nacimiento" name="fecha_nacimiento" type="date" />
          </div>
          <div>
            <label for="cel">Celular</label>
            <input id="cel" name="cel" type="tel" placeholder="10 dígitos" pattern="[0-9]{10}" />
          </div>
        </div>

        <div class="grid g-3">
          <div>
            <label for="edad">Edad</label>
            <input id="edad" name="edad" type="number" min="0" max="120" />
          </div>
          <div>
            <label for="estado_civil">Estado civil</label>
            <select id="estado_civil" name="estado_civil">
              <option value="">Seleccione…</option>
              <option>Soltero/a</option>
              <option>Casado/a</option>
              <option>Unión libre</option>
              <option>Divorciado/a</option>
              <option>Viudo/a</option>
              <option>Otro</option>
            </select>
          </div>
          <div>
            <label for="nacionalidad">Nacionalidad</label>
            <input id="nacionalidad" name="nacionalidad" type="text" />
          </div>
        </div>

        <div>
          <label for="domicilio">Domicilio (Calle, Núm., Colonia, Ciudad)</label>
          <textarea id="domicilio" name="domicilio" placeholder="Calle, número, colonia y ciudad"></textarea>
        </div>

        <div class="grid g-2">
          <div>
            <label for="profesion">Profesión / Trabajo</label>
            <input id="profesion" name="profesion" type="text" />
          </div>
          <div>
            <label for="titular">Titular</label>
            <input id="titular" name="titular" type="text" />
          </div>
        </div>

        <div class="grid g-2">
          <div>
            <label for="contacto_nombre">Contacto de emergencia – Nombre</label>
            <input id="contacto_nombre" name="contacto_nombre" type="text" />
          </div>
          <div>
            <label for="contacto_tel">Contacto de emergencia – Teléfono</label>
            <input id="contacto_tel" name="contacto_tel" type="tel" placeholder="10 dígitos" pattern="[0-9]{10}" />
          </div>
        </div>

        <div>
          <label for="motivo">Motivo de consulta</label>
          <textarea id="motivo" name="motivo"></textarea>
        </div>

        <h2 class="section-title">Dolor</h2>
        <div class="row">
          <span class="chip"><input type="checkbox" id="dolor_suave" name="dolor_tipo" value="suave"><label for="dolor_suave">Suave</label></span>
          <span class="chip"><input type="checkbox" id="dolor_moderado" value="moderado"><label for="dolor_moderado">Moderado</label></span>
          <span class="chip"><input type="checkbox" id="dolor_intenso" value="intenso"><label for="dolor_intenso">Intenso</label></span>
          <span class="chip"><input type="checkbox" id="dolor_temporario" value="temporario"><label for="dolor_temporario">Temporario</label></span>
          <span class="chip"><input type="checkbox" id="dolor_interm" value="intermitente"><label for="dolor_interm">Intermitente</label></span>
          <span class="chip"><input type="checkbox" id="dolor_continuo" value="continuo"><label for="dolor_continuo">Continuo</label></span>
          <span class="chip"><input type="checkbox" id="dolor_espontaneo" value="espontaneo"><label for="dolor_espontaneo">Espontáneo</label></span>
          <span class="chip"><input type="checkbox" id="dolor_provocado" value="provocado"><label for="dolor_provocado">Provocado</label></span>
          <span class="chip"><input type="checkbox" id="dolor_frio" value="al frio"><label for="dolor_frio">Al frío</label></span>
          <span class="chip"><input type="checkbox" id="dolor_calor" value="al calor"><label for="dolor_calor">Al calor</label></span>
          <span class="chip"><input type="checkbox" id="dolor_localizado" value="localizado"><label for="dolor_localizado">Localizado</label></span>
        </div>
        <div class="grid g-2" style="margin-top:10px">
          <div>
            <label for="dolor_donde">¿Dónde?</label>
            <input id="dolor_donde" name="dolor_donde" type="text" disabled />
          </div>
          <div>
            <label>¿Puede calmarlo con algo?</label>
            <div class="inline">
              <label class="chip"><input type="radio" name="calma" id="calma_si" value="si"> <span>Sí</span></label>
              <label class="chip"><input type="radio" name="calma" id="calma_no" value="no"> <span>No</span></label>
              <input id="con_que" name="con_que" type="text" placeholder="¿Con qué?" disabled style="min-width:240px"/>
            </div>
          </div>
        </div>

        <div>
          <label for="ultima_visita">Última visita al dentista</label>
          <input id="ultima_visita" name="ultima_visita" type="date" />
        </div>

        <div class="divider"></div>

        <div class="grid g-3">
          <div>
            <label>¿Sufre de alguna enfermedad?</label>
            <div class="inline">
              <label class="chip"><input type="radio" name="enf_general" value="si"> <span>Sí</span></label>
              <label class="chip"><input type="radio" name="enf_general" value="no" checked> <span>No</span></label>
            </div>
          </div>
          <div class="gspan-2">
            <label for="enf_cual">¿De qué?</label>
            <input id="enf_cual" name="enf_cual" type="text" />
          </div>
        </div>

        <div>
          <label for="medicamentos">Medicamentos que consume habitualmente</label>
          <textarea id="medicamentos" name="medicamentos"></textarea>
        </div>
        <div>
          <label for="alergias">Alergias (droga/medicamento/alimento)</label>
          <textarea id="alergias" name="alergias"></textarea>
        </div>

        <h2 class="section-title">Listado de enfermedades</h2>
        <p class="hint">Marque SÍ o NO; si responde SÍ, describa el tratamiento.</p>
        <table class="tbl" id="enfermedades-tabla">
          <thead>
            <tr>
              <th>Enfermedad</th>
              <th>Sí</th>
              <th>No</th>
              <th>Tratamiento (se activa si SÍ)</th>
            </tr>
          </thead>
          <tbody>
            <!-- filas generadas por JS para mantener el HTML más limpio -->
          </tbody>
        </table>

        <div class="grid g-3">
          <div>
            <label for="otros_enf">Otros (describa cuáles)</label>
            <input id="otros_enf" name="otros_enf" type="text" />
          </div>
          <div>
            <label>¿Tuvo transfusiones?</label>
            <div class="inline">
              <label class="chip"><input type="radio" name="transfusiones" value="si"> <span>Sí</span></label>
              <label class="chip"><input type="radio" name="transfusiones" value="no" checked> <span>No</span></label>
            </div>
          </div>
          <div>
            <label>¿Fue operado alguna vez?</label>
            <div class="inline">
              <label class="chip"><input type="radio" name="operado" value="si"> <span>Sí</span></label>
              <label class="chip"><input type="radio" name="operado" value="no" checked> <span>No</span></label>
            </div>
          </div>
        </div>

        <div class="grid g-2">
          <div>
            <label for="operado_deque">¿De qué?</label>
            <input id="operado_deque" name="operado_deque" type="text" />
          </div>
          <div>
            <label for="operado_cuando">¿Cuándo?</label>
            <input id="operado_cuando" name="operado_cuando" type="date" />
          </div>
        </div>

        <div class="grid g-3">
          <div>
            <label>¿Fuma?</label>
            <div class="inline">
              <label class="chip"><input type="radio" name="fuma" value="si"> <span>Sí</span></label>
              <label class="chip"><input type="radio" name="fuma" value="no" checked> <span>No</span></label>
            </div>
          </div>
          <div>
            <label>¿Toma?</label>
            <div class="inline">
              <label class="chip"><input type="radio" name="toma" value="si"> <span>Sí</span></label>
              <label class="chip"><input type="radio" name="toma" value="no" checked> <span>No</span></label>
            </div>
          </div>
          <div>
            <label>¿Consume algún tipo de droga?</label>
            <div class="inline">
              <label class="chip"><input type="radio" name="drogas" value="si"> <span>Sí</span></label>
              <label class="chip"><input type="radio" name="drogas" value="no" checked> <span>No</span></label>
            </div>
          </div>
        </div>
      </fieldset>

      <!-- 2. ODONTOLÓGICA -->
      <fieldset>
        <legend>Historia Clínica Odontológica</legend>

        <div class="grid g-3">
          <div>
            <label>¿Le sangran las encías?</label>
            <div class="inline">
              <label class="chip"><input type="radio" name="sangrado_encias" value="si"> <span>Sí</span></label>
              <label class="chip"><input type="radio" name="sangrado_encias" value="no" checked> <span>No</span></label>
            </div>
          </div>
          <div>
            <label for="sangrado_cuando">¿Cuándo?</label>
            <input id="sangrado_cuando" name="sangrado_cuando" type="text" />
          </div>
          <div>
            <label>¿Tiene movilidad en sus dientes?</label>
            <div class="inline">
              <label class="chip"><input type="radio" name="movilidad" value="si"> <span>Sí</span></label>
              <label class="chip"><input type="radio" name="movilidad" value="no" checked> <span>No</span></label>
            </div>
          </div>
        </div>

        <div class="grid g-3">
          <div>
            <label for="indice_placa">Índice de placa</label>
            <input id="indice_placa" name="indice_placa" type="text" />
          </div>
          <div>
            <label>Estado de higiene bucal</label>
            <div class="inline">
              <label class="chip"><input type="radio" name="higiene" value="muy_bueno"> <span>Muy bueno</span></label>
              <label class="chip"><input type="radio" name="higiene" value="bueno"> <span>Bueno</span></label>
              <label class="chip"><input type="radio" name="higiene" value="deficiente"> <span>Deficiente</span></label>
              <label class="chip"><input type="radio" name="higiene" value="malo"> <span>Malo</span></label>
            </div>
          </div>
          <div>
            <label>¿Manchas en dientes o encías?</label>
            <div class="inline">
              <label class="chip"><input type="radio" name="manchas" value="si"> <span>Sí</span></label>
              <label class="chip"><input type="radio" name="manchas" value="no" checked> <span>No</span></label>
            </div>
            <input id="manchas_desc" name="manchas_desc" type="text" placeholder="Descripción" />
          </div>
        </div>

        <div class="grid g-3">
          <div>
            <label>¿Sufrió algún golpe en los dientes?</label>
            <div class="inline">
              <label class="chip"><input type="radio" name="golpe" value="si"> <span>Sí</span></label>
              <label class="chip"><input type="radio" name="golpe" value="no" checked> <span>No</span></label>
            </div>
          </div>
          <div>
            <label>¿Se le fracturó algún diente?</label>
            <div class="inline">
              <label class="chip"><input type="radio" name="fractura" value="si"> <span>Sí</span></label>
              <label class="chip"><input type="radio" name="fractura" value="no" checked> <span>No</span></label>
            </div>
          </div>
          <div>
            <label for="cual_diente">¿Cuál?</label>
            <input id="cual_diente" name="cual_diente" type="text" />
          </div>
        </div>

        <div class="grid g-2">
          <div>
            <label for="tratamiento_diente">¿Recibió algún tratamiento?</label>
            <input id="tratamiento_diente" name="tratamiento_diente" type="text" />
          </div>
          <div>
            <label for="dificultad_abrir">¿Dificultad para abrir la boca?</label>
            <input id="dificultad_abrir" name="dificultad_abrir" type="text" />
          </div>
        </div>

        <div class="grid g-3">
          <div>
            <label for="encia">Encía</label>
            <input id="encia" name="encia" type="text" />
          </div>
          <div>
            <label for="lengua">Lengua</label>
            <input id="lengua" name="lengua" type="text" />
          </div>
          <div>
            <label for="paladar">Paladar</label>
            <input id="paladar" name="paladar" type="text" />
          </div>
          <div>
            <label for="piso_boca">Piso de boca</label>
            <input id="piso_boca" name="piso_boca" type="text" />
          </div>
          <div>
            <label for="carrillos">Carrillos</label>
            <input id="carrillos" name="carrillos" type="text" />
          </div>
          <div>
            <label for="rebordes">Rebordes</label>
            <input id="rebordes" name="rebordes" type="text" />
          </div>
        </div>
      </fieldset>

      <!-- 3. DIAGNÓSTICO / CONSENTIMIENTO -->
      <fieldset>
        <legend>Exploración, Diagnóstico y Plan</legend>
        <div class="grid g-2">
          <div>
            <label for="estado_bucal">Estado bucal general</label>
            <textarea id="estado_bucal" name="estado_bucal"></textarea>
          </div>
          <div>
            <label>Hallazgos</label>
            <div class="grid g-2">
              <div>
                <label>Presencia de sarro</label>
                <div class="inline">
                  <label class="chip"><input type="radio" name="sarro" value="si"> <span>Sí</span></label>
                  <label class="chip"><input type="radio" name="sarro" value="no" checked> <span>No</span></label>
                </div>
              </div>
              <div>
                <label>Enfermedad periodontal</label>
                <div class="inline">
                  <label class="chip"><input type="radio" name="periodontal" value="si"> <span>Sí</span></label>
                  <label class="chip"><input type="radio" name="periodontal" value="no" checked> <span>No</span></label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div>
          <label for="diagnostico">Diagnóstico presuntivo</label>
          <textarea id="diagnostico" name="diagnostico"></textarea>
        </div>
        <div class="grid g-2">
          <div>
            <label for="plan">Plan de tratamiento</label>
            <textarea id="plan" name="plan"></textarea>
          </div>
          <div>
            <label for="plan_fecha">Fecha (plan)</label>
            <input id="plan_fecha" name="plan_fecha" type="date" />
          </div>
        </div>
        <div>
          <label for="observaciones">Observaciones</label>
          <textarea id="observaciones" name="observaciones"></textarea>
        </div>
      </fieldset>

      <!-- 4. FIRMAS -->
      <fieldset>
        <legend>Consentimiento</legend>
        <p class="hint">Declaro que he contestado todas las preguntas con honestidad y que la información suministrada quedará reservada en esta Historia Clínica y amparada por el secreto profesional. Autorizo los procedimientos correspondientes para mi tratamiento, conociendo los riesgos que conllevan.</p>
        <div class="grid g-3">
          <div>
            <label for="firma_paciente">Nombre y firma del paciente</label>
            <input id="firma_paciente" name="firma_paciente" type="text" placeholder="Nombre completo" />
          </div>
          <div>
            <label for="padre_tutor">Padre o Tutor (si aplica)</label>
            <input id="padre_tutor" name="padre_tutor" type="text" placeholder="Nombre completo" />
          </div>
          <div>
            <label for="firma_dentista">Nombre y firma del dentista</label>
            <input id="firma_dentista" name="firma_dentista" type="text" placeholder="Nombre completo" />
          </div>
        </div>
      </fieldset>

      <div class="footer">
        <button type="button" class="ghost" onclick="window.print()">Imprimir/Guardar PDF</button>
        <button type="reset" class="danger">Limpiar</button>
        <button type="submit" class="primary">Guardar</button>
      </div>
    </form>
  </div>

  <script>
    // Lista de enfermedades del PDF
    const enfermedades = [
      "Diabetes","Hipertensión Arterial","Epilepsia o Convulsiones","Infarto","Anemia","Asma","Hepatitis","Híper o Hipotiroidismo","Angina de pecho","Tuberculosis","Insuficiencia renal","Enfermedades venéreas","H.I.V. / SIDA","Gastritis","Embarazo","COVID","Cáncer"
    ];

    const tbody = document.querySelector('#enfermedades-tabla tbody');
    enfermedades.forEach((enf, idx)=>{
      const id = `enf_${idx}`;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><label for="${id}_si" class="sr-only">${enf}</label>${enf}</td>
        <td style="text-align:center"><input type="radio" name="${id}_yn" id="${id}_si" value="si"></td>
        <td style="text-align:center"><input type="radio" name="${id}_yn" id="${id}_no" value="no" checked></td>
        <td><input type="text" id="${id}_trat" name="${id}_trat" placeholder="Describa tratamiento" disabled></td>
      `;
      tbody.appendChild(tr);
    });

    // Activar tratamiento solo cuando SÍ
    tbody.addEventListener('change', (e)=>{
      if(e.target && e.target.name.endsWith('_yn')){
        const base = e.target.name.replace('_yn','');
        const trat = document.getElementById(base+"_trat");
        trat.disabled = (e.target.value !== 'si');
        if(trat.disabled) trat.value = '';
      }
    });

    // Activar campo "¿Dónde?" solo si el dolor es localizado
    const chkLocal = document.getElementById('dolor_localizado');
    const dolorDonde = document.getElementById('dolor_donde');
    const toggleDonde = () => {
      dolorDonde.disabled = !chkLocal.checked;
      if(dolorDonde.disabled) dolorDonde.value = '';
    };
    [chkLocal].forEach(el=> el.addEventListener('change', toggleDonde));

    // Activar "¿Con qué?" solo si "Puede calmarlo" = Sí
    const calmaSi = document.getElementById('calma_si');
    const calmaNo = document.getElementById('calma_no');
    const conQue = document.getElementById('con_que');
    const toggleConQue = () => {
      const yes = calmaSi.checked;
      conQue.disabled = !yes;
      if(!yes) conQue.value = '';
    };
    [calmaSi, calmaNo].forEach(el=> el.addEventListener('change', toggleConQue));

    // Calcula edad basada en la fecha de nacimiento (si se desea)
    const nacimiento = document.getElementById('nacimiento');
    const edad = document.getElementById('edad');
    nacimiento.addEventListener('change', ()=>{
      if(!nacimiento.value) return;
      const dob = new Date(nacimiento.value);
      const today = new Date();
      let y = today.getFullYear() - dob.getFullYear();
      const m = today.getMonth() - dob.getMonth();
      if(m < 0 || (m === 0 && today.getDate() < dob.getDate())) y--;
      if(y>=0 && y<130) edad.value = y;
    });

 
    // Inicializaciones
    toggleDonde();
    toggleConQue();
  </script>
</body>
</html>
