<?php
include("../conexion.php");

// Obtener los datos del formulario
$nombre = trim($_POST['nombre']);
$cantidad = intval($_POST['cantidad']);
$descripcion = trim($_POST['descripcion']);
$fecha = $_POST['fecha'] ?? date('Y-m-d');

// Validaciones básicas
if (empty($nombre) || $cantidad <= 0) {
    echo "<script>alert('Por favor ingrese un nombre y cantidad válida.'); window.history.back();</script>";
    exit;
}

// Verificar si el artículo ya existe
$stmt = $conexion->prepare("SELECT id_objeto, cantidad FROM inventario WHERE nombre_objeto = ?");
$stmt->bind_param("s", $nombre);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si existe, se actualiza la cantidad
    $row = $result->fetch_assoc();
    $nuevo_total = $row['cantidad'] + $cantidad;

    $update = $conexion->prepare("UPDATE inventario SET cantidad = ?, descripcion = ?, fecha_modificacion = ? WHERE id_objeto = ?");
    $update->bind_param("issi", $nuevo_total, $descripcion, $fecha, $row['id_objeto']);
    $update->execute();

    echo "<script>alert('Cantidad actualizada correctamente.'); window.location='../inicio.php?page=inventario';</script>";
} else {
    // Si no existe, se inserta nuevo
    $insert = $conexion->prepare("INSERT INTO inventario (nombre_objeto, descripcion, cantidad, fecha_modificacion) VALUES (?, ?, ?, ?)");
    $insert->bind_param("ssis", $nombre, $descripcion, $cantidad, $fecha);
    $insert->execute();

    echo "<script>alert('Artículo agregado correctamente al inventario.'); window.location='../inicio.php?page=inventario';</script>";
}

$stmt->close();
$conexion->close();
?>
