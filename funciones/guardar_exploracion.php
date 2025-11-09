<?php
require "../conexion.php";

$id_historia = field('id_historia');
if (!$id_historia) die("Error: falta id_historia.");

// Insertar exploración
$sql = "INSERT INTO exploracion_bucal
(id_historia, dolor_donde, calma, con_que, ultima_visita, sangrado_encias, sangrado_cuando, movilidad, indice_placa, higiene,
 manchas, manchas_desc, golpe, fractura, cual_diente, tratamiento_diente, dificultad_abrir, sarro, periodontal, estado_bucal,
 diagnostico, plan_tratamiento, observaciones, firma_dentista)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param(
  'issssssssssssssssssssssss',
  $id_historia, $dolor_donde, $calma, $con_que, $ultima_visita, $sangrado_encias, $sangrado_cuando, $movilidad, $indice_placa, $higiene,
  $manchas, $manchas_desc, $golpe, $fractura, $cual_diente, $tratamiento_diente, $dificultad_abrir, $sarro, $periodontal, $estado_bucal,
  $diagnostico, $plan_tratamiento, $observaciones, $firma_dentista
);

$dolor_donde = field('dolor_donde');
$calma = field('calma','No');
$con_que = field('con_que');
$ultima_visita = field('ultima_visita');
$sangrado_encias = field('sangrado_encias','No');
$sangrado_cuando = field('sangrado_cuando');
$movilidad = field('movilidad','No');
$indice_placa = field('indice_placa');
$higiene = field('higiene','Buena');
$manchas = field('manchas','No');
$manchas_desc = field('manchas_desc');
$golpe = field('golpe','No');
$fractura = field('fractura','No');
$cual_diente = field('cual_diente');
$tratamiento_diente = field('tratamiento_diente');
$dificultad_abrir = field('dificultad_abrir');
$sarro = field('sarro','No');
$periodontal = field('periodontal','No');
$estado_bucal = field('estado_bucal');
$diagnostico = field('diagnostico');
$plan_tratamiento = field('plan_tratamiento');
$observaciones = field('observaciones');
$firma_dentista = field('firma_dentista');

if ($stmt->execute()) {
  echo "<script>alert('Exploración guardada correctamente'); window.location='inicio.php?page=pacientes';</script>";
} else {
  die('Error al guardar exploración: '.$stmt->error);
}
