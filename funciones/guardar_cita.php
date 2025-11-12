<?php
require '../conexion.php';

$id_paciente = field('id_paciente');
$id_tratamiento = field('id_tratamiento');
$fecha = field('fecha');
$hora = field('hora');
$motivo = field('motivo');
$estado = field('estado', 'pendiente');

if (!$id_paciente || !$fecha || !$hora) {
  die("Datos incompletos para registrar cita.");
}

$sql = "INSERT INTO citas (id_paciente, id_tratamiento, fecha, hora, motivo, estado)
        VALUES (?,?,?,?,?,?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('iissss', $id_paciente, $id_tratamiento, $fecha, $hora, $motivo, $estado);

if ($stmt->execute()) {
  echo "<script>alert('Cita registrada correctamente'); window.location='../inicio.php?page=citas';</script>";
} else {
  die('Error al guardar cita: ' . $stmt->error);
}
