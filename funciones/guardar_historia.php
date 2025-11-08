<?php
include("../conexion.php");

// ===== Validar campo obligatorio =====
if (empty($_POST['nombre_paciente'])) {
    die("Error: el nombre del paciente es obligatorio.");
}

/* =======================================================
   1️⃣ Insertar datos en tabla PACIENTES
   ======================================================= */
$nombre = $_POST['nombre_paciente'];
$fecha_nac = $_POST['fecha_nacimiento'] ?? null;
$edad = $_POST['edad'] ?? null;
$celular = $_POST['cel'] ?? null;
$estado_civil = $_POST['estado_civil'] ?? '';
$nacionalidad = $_POST['nacionalidad'] ?? '';
$domicilio = $_POST['domicilio'] ?? '';
$profesion = $_POST['profesion'] ?? '';
$contacto_emergencia = $_POST['contacto_emergencia'] ?? '';
$telefono_emergencia = $_POST['telefono_emergencia'] ?? '';
// === Verificar duplicado ===
$nombre = trim($_POST['nombre_paciente']);
$cel = trim($_POST['cel']);

$stmt_verif = $conexion->prepare("SELECT id_paciente FROM pacientes WHERE nombre = ? OR celular = ?");
$stmt_verif->bind_param("ss", $nombre, $cel);
$stmt_verif->execute();
$res_verif = $stmt_verif->get_result();

if($res_verif->num_rows > 0){
    echo "<script>alert('Este paciente ya está registrado en el sistema.');window.location='pacientes_registrados.php';</script>";
    exit;
}

$sql_paciente = "INSERT INTO pacientes 
(nombre, fecha_nacimiento, edad, celular, estado_civil, nacionalidad, domicilio, profesion, contacto_emergencia, telefono_emergencia)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conexion->prepare($sql_paciente);
$stmt->bind_param("ssisssssss", $nombre, $fecha_nac, $edad, $celular, $estado_civil, $nacionalidad, $domicilio, $profesion, $contacto_emergencia, $telefono_emergencia);
$stmt->execute();
$id_paciente = $conexion->insert_id;
$stmt->close();

/* =======================================================
   2️⃣ Insertar datos generales en HISTORIA_CLINICA
   ======================================================= */
$lugar = $_POST['lugar'] ?? '';
$fecha = $_POST['fecha'] ?? '';
$motivo = $_POST['motivo_consulta'] ?? '';
$diagnostico = $_POST['diagnostico'] ?? '';
$plan_tratamiento = $_POST['plan_tratamiento'] ?? '';
$observaciones = $_POST['observaciones'] ?? '';
$firma_paciente = $_POST['firma_paciente'] ?? '';
$firma_dentista = $_POST['firma_dentista'] ?? '';

$sql_historia = "INSERT INTO historia_clinica 
(id_paciente, lugar, fecha, motivo_consulta, diagnostico, plan_tratamiento, observaciones, firma_paciente, firma_dentista)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt1 = $conexion->prepare($sql_historia);
$stmt1->bind_param("issssssss", $id_paciente, $lugar, $fecha, $motivo, $diagnostico, $plan_tratamiento, $observaciones, $firma_paciente, $firma_dentista);
$stmt1->execute();
$id_historia = $conexion->insert_id;
$stmt1->close();

/* =======================================================
   3️⃣ Insertar ANTECEDENTES MÉDICOS
   ======================================================= */
$enf_general = $_POST['enf_general'] ?? 'No';
$enf_cual = $_POST['enf_cual'] ?? '';
$medicamentos = $_POST['medicamentos'] ?? '';
$alergias = $_POST['alergias'] ?? '';
$transfusiones = $_POST['transfusiones'] ?? 'No';
$operado = $_POST['operado'] ?? 'No';
$operado_deque = $_POST['operado_deque'] ?? '';
$operado_cuando = $_POST['operado_cuando'] ?? null;
$fuma = $_POST['fuma'] ?? 'No';
$toma = $_POST['toma'] ?? 'No';
$drogas = $_POST['drogas'] ?? 'No';

$sql_antecedentes = "INSERT INTO antecedentes_medicos 
(id_historia, enf_general, enf_cual, medicamentos, alergias, transfusiones, operado, operado_deque, operado_cuando, fuma, toma, drogas)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt2 = $conexion->prepare($sql_antecedentes);
$stmt2->bind_param("isssssssssss", $id_historia, $enf_general, $enf_cual, $medicamentos, $alergias, $transfusiones, $operado, $operado_deque, $operado_cuando, $fuma, $toma, $drogas);
$stmt2->execute();
$stmt2->close();

/* =======================================================
   4️⃣ Insertar EXPLORACIÓN BUCAL
   ======================================================= */
$dolor_donde = $_POST['dolor_donde'] ?? '';
$calma = $_POST['calma'] ?? 'No';
$con_que = $_POST['con_que'] ?? '';
$ultima_visita = $_POST['ultima_visita'] ?? null;
$sangrado_encias = $_POST['sangrado_encias'] ?? 'No';
$sangrado_cuando = $_POST['sangrado_cuando'] ?? '';
$movilidad = $_POST['movilidad'] ?? 'No';
$indice_placa = $_POST['indice_placa'] ?? '';
$higiene = $_POST['higiene'] ?? 'Buena';
$manchas = $_POST['manchas'] ?? 'No';
$manchas_desc = $_POST['manchas_desc'] ?? '';
$golpe = $_POST['golpe'] ?? 'No';
$fractura = $_POST['fractura'] ?? 'No';
$cual_diente = $_POST['cual_diente'] ?? '';
$tratamiento_diente = $_POST['tratamiento_diente'] ?? '';
$dificultad_abrir = $_POST['dificultad_abrir'] ?? '';
$sarro = $_POST['sarro'] ?? 'No';
$periodontal = $_POST['periodontal'] ?? 'No';
$estado_bucal = $_POST['estado_bucal'] ?? '';

$sql_exploracion = "INSERT INTO exploracion_bucal 
(id_historia, dolor_donde, calma, con_que, ultima_visita, sangrado_encias, sangrado_cuando, movilidad, indice_placa, higiene, manchas, manchas_desc, golpe, fractura, cual_diente, tratamiento_diente, dificultad_abrir, sarro, periodontal, estado_bucal)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt3 = $conexion->prepare($sql_exploracion);
$stmt3->bind_param("isssssssssssssssssss", $id_historia, $dolor_donde, $calma, $con_que, $ultima_visita, $sangrado_encias, $sangrado_cuando, $movilidad, $indice_placa, $higiene, $manchas, $manchas_desc, $golpe, $fractura, $cual_diente, $tratamiento_diente, $dificultad_abrir, $sarro, $periodontal, $estado_bucal);
$stmt3->execute();
$stmt3->close();

/* =======================================================
   ✅ Confirmación
   ======================================================= */
echo "<script>
alert('Historia clínica registrada correctamente con todos los datos.');
window.location='index.php?page=expediente';
</script>";

$conexion->close();
?>
