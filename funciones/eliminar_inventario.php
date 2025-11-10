<?php
include("../conexion.php");

$idEliminar = trim($_POST['idEliminar']);
$cantidadEliminar = intval($_POST['cantidadEliminar']);

if (empty($idEliminar) || $cantidadEliminar <= 0) {
    echo "<script>alert('Por favor ingrese un ID o nombre válido y cantidad correcta.'); window.history.back();</script>";
    exit;
}

// Verificar si el parámetro es ID o nombre
if (is_numeric($idEliminar)) {
    $stmt = $conexion->prepare("SELECT id_objeto, nombre_objeto, cantidad FROM inventario WHERE id_objeto = ?");
    $stmt->bind_param("i", $idEliminar);
} else {
    $stmt = $conexion->prepare("SELECT id_objeto, nombre_objeto, cantidad FROM inventario WHERE nombre_objeto = ?");
    $stmt->bind_param("s", $idEliminar);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('No se encontró ningún artículo con ese nombre o ID.'); window.history.back();</script>";
    exit;
}

$articulo = $result->fetch_assoc();

if ($articulo['cantidad'] < $cantidadEliminar) {
    echo "<script>alert('No hay suficiente cantidad para eliminar.'); window.history.back();</script>";
    exit;
}

$nuevaCantidad = $articulo['cantidad'] - $cantidadEliminar;

// Si llega a 0, eliminar el registro completo
if ($nuevaCantidad == 0) {
    $delete = $conexion->prepare("DELETE FROM inventario WHERE id_objeto = ?");
    $delete->bind_param("i", $articulo['id_objeto']);
    $delete->execute();
    echo "<script>alert('Artículo eliminado completamente del inventario.'); window.location='../form_inventario.php?page=inventario';</script>";
} else {
    $update = $conexion->prepare("UPDATE inventario SET cantidad = ?, fecha_modificacion = NOW() WHERE id_objeto = ?");
    $update->bind_param("ii", $nuevaCantidad, $articulo['id_objeto']);
    $update->execute();
    echo "<script>alert('Cantidad actualizada correctamente.'); window.location='../form_inventario.php?page=inventario';</script>";
}

$stmt->close();
$conexion->close();
?>
