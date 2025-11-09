<?php
require "../conexion.php";

$id = field('id_tratamiento');
$nombre = field('nombre_tratamiento');
$desc = field('descripcion');
$costo = field('costo');

if (!$id || !$nombre) {
  die("Datos incompletos.");
}

$sql = "UPDATE tratamientos SET nombre_tratamiento=?, descripcion=?, costo=? WHERE id_tratamiento=?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('ssdi', $nombre, $desc, $costo, $id);

if ($stmt->execute()) {
  echo "<script>alert('Tratamiento actualizado correctamente'); window.location='../lista_tratamientos.php';</script>";
} else {
  die('Error al actualizar: '.$stmt->error);
}
