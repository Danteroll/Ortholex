<?php
require '../conexion.php';

// Inserta un paciente
$stmt = $conexion->prepare("
  INSERT INTO pacientes
  (nombre, fecha_nacimiento, edad, celular, estado_civil, nacionalidad, domicilio,
   profesion, contacto_emergencia, telefono_emergencia, estado_registro)
  VALUES (?,?,?,?,?,?,?,?,?,?,?)
");
$edad = null;
$stmt->bind_param(
  'ssissssssss',
  $nombre,
  $fecha_nacimiento,
  $edad,
  $celular,
  $estado_civil,
  $nacionalidad,
  $domicilio,
  $profesion,
  $contacto_emergencia,
  $telefono_emergencia,
  $estado_registro
);

$nombre = field('nombre');
$fecha_nacimiento = field('fecha_nacimiento');
$celular = field('celular');
$estado_civil = field('estado_civil', 'Soltero');
$nacionalidad = field('nacionalidad');
$domicilio = field('domicilio');
$profesion = field('profesion');
$contacto_emergencia = field('contacto_emergencia');
$telefono_emergencia = field('telefono_emergencia');
$estado_registro = 'Paciente';

if (!$stmt->execute()) {
  die('Error insertando paciente: ' . $stmt->error);
}
$id_paciente = $stmt->insert_id;
$stmt->close();

// Inserta la historia clinica del paciente (autorreporte)
$sql = "
INSERT INTO historia_clinica (
  id_paciente, lugar, fecha, motivo_consulta, enf_general, 
  enf_cual, medicamentos, alergias, transfusiones, operado, 
  operado_deque, operado_cuando, fuma, toma, drogas,
  diabetes, hipertension, epilepsia, infarto, anemia, 
  asma, hepatitis, tiroides, angina_pecho, tuberculosis,
  renal, venereas, vih, gastritis, embarazo,
  covid, cancer, otros, observaciones, firma_paciente
) VALUES (?,?,?,?,?, ?,?,?,?,? ,?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?)
";
$stmt2 = $conexion->prepare($sql);
$stmt2->bind_param(
  'issssssssssssssssssssssssssssssssss',
  $id_paciente,
  $lugar,
  $fecha,
  $motivo_consulta,
  $enf_general,
  $enf_cual,
  $medicamentos,
  $alergias,
  $transfusiones,
  $operado,
  $operado_deque,
  $operado_cuando,
  $fuma,
  $toma,
  $drogas,
  $diabetes,
  $hipertension,
  $epilepsia,
  $infarto,
  $anemia,
  $asma,
  $hepatitis,
  $tiroides,
  $angina_pecho,
  $tuberculosis,
  $renal,
  $venereas,
  $vih,
  $gastritis,
  $embarazo,
  $covid,
  $cancer,
  $otros,
  $observaciones,
  $firma_paciente
);

// Mapea las variables
$lugar = field('lugar');
$fecha = field('fecha');
$motivo_consulta = field('motivo_consulta');
$enf_general   = field('enf_general', 'No');
$enf_cual      = field('enf_cual');
$medicamentos  = field('medicamentos');
$alergias      = field('alergias');
$transfusiones = field('transfusiones', 'No');
$operado       = field('operado', 'No');
$operado_deque = field('operado_deque');
$operado_cuando = field('operado_cuando');
$fuma          = field('fuma', 'No');
$toma          = field('toma', 'No');
$drogas        = field('drogas', 'No');
$diabetes      = field('diabetes', 'No');
$hipertension  = field('hipertension', 'No');
$epilepsia     = field('epilepsia', 'No');
$infarto       = field('infarto', 'No');
$anemia        = field('anemia', 'No');
$asma          = field('asma', 'No');
$hepatitis     = field('hepatitis', 'No');
$tiroides      = field('tiroides', 'No');
$angina_pecho  = field('angina_pecho', 'No');
$tuberculosis  = field('tuberculosis', 'No');
$renal         = field('renal', 'No');
$venereas      = field('venereas', 'No');
$vih           = field('vih', 'No');
$gastritis     = field('gastritis', 'No');
$embarazo      = field('embarazo', 'No');
$covid         = field('covid', 'No');
$cancer        = field('cancer', 'No');
$otros         = field('otros');
$observaciones   = field('observaciones');
$firma_paciente  = field('firma_paciente');

if (!$stmt2->execute()) {
  die('Error insertando historia clínica: ' . $stmt2->error);
}
$id_historia = $stmt2->insert_id;
$stmt2->close;

// Redirige a “gracias” o directo al panel
echo "<script>alert('Registro enviado. ¡Gracias!'); window.location='gracias.html';</script>";
