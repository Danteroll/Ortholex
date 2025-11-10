<?php
include("../conexion.php");
date_default_timezone_set('America/Mexico_City');

$id = intval($_POST['id_objeto']);
$nombre = trim($_POST['nombre_objeto']);
$descripcion = trim($_POST['descripcion']);
$cantidad = intval($_POST['cantidad']);
$fecha_modificacion = date('Y-m-d H:i:s');

if ($id <= 0 || empty($nombre)) {
    echo "<script>alert('Datos inválidos.'); window.history.back();</script>";
    exit;
}

$stmt = $conexion->prepare("
    UPDATE inventario 
    SET nombre_objeto = ?, descripcion = ?, cantidad = ?, fecha_modificacion = ?
    WHERE id_objeto = ?
");
$stmt->bind_param("ssisi", $nombre, $descripcion, $cantidad, $fecha_modificacion, $id);

if ($stmt->execute()) {
    echo "<script>alert('Artículo actualizado correctamente.'); window.location='../form_inventario.php';</script>";
} else {
    echo "<script>alert('Error al actualizar el artículo.'); window.history.back();</script>";
}

$stmt->close();
$conexion->close();
?>
