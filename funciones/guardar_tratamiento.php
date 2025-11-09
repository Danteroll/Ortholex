<?php
require "../conexion.php";

$nombre_tratamiento = field('nombre_tratamiento');
$descripcion = field('descripcion');
$costo = field('costo');

if (!$nombre_tratamiento || !$costo) {
  die("Faltan datos del tratamiento.");
}

$sql = "INSERT INTO tratamientos (nombre_tratamiento, descripcion, costo)
        VALUES (?, ?, ?)";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('ssd', $nombre_tratamiento, $descripcion, $costo);

if ($stmt->execute()) {
  echo "<script>alert('Tratamiento registrado correctamente'); window.location='../lista_tratamientos.php';</script>";
} else {
  die('Error al guardar tratamiento: '.$stmt->error);
}
