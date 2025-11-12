<?php
include("../conexion.php");
date_default_timezone_set('America/Mexico_City');

// Obtiene los datos del formulario
$nombre = trim($_POST['nombre']);
$cantidad = intval($_POST['cantidad']);
$descripcion = trim($_POST['descripcion']);

// Genera fecha y hora actuales
$fecha_actual = date('Y-m-d H:i:s');

// Validaciones básicas
if (empty($nombre) || $cantidad <= 0) {
    echo "<script>alert('Por favor ingrese un nombre y cantidad válida.'); window.history.back();</script>";
    exit;
}

// Verifica si el artículo ya existe
$stmt = $conexion->prepare("SELECT id_objeto, cantidad FROM inventario WHERE nombre_objeto = ?");
$stmt->bind_param("s", $nombre);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si existe, se actualiza la cantidad y la fecha de modificación
    $row = $result->fetch_assoc();
    $nuevo_total = $row['cantidad'] + $cantidad;

    $update = $conexion->prepare("
        UPDATE inventario 
        SET cantidad = ?, descripcion = ?, fecha_modificacion = ? 
        WHERE id_objeto = ?
    ");
    $update->bind_param("issi", $nuevo_total, $descripcion, $fecha_actual, $row['id_objeto']);
    $update->execute();

    echo "<script>alert('Cantidad actualizada correctamente.'); window.location='../form_inventario.php';</script>";
} else {
    // Si no existe, se inserta nuevo artículo con fecha y hora actual
    $insert = $conexion->prepare("
        INSERT INTO inventario (nombre_objeto, descripcion, cantidad, fecha_modificacion)
        VALUES (?, ?, ?, ?)
    ");
    $insert->bind_param("ssis", $nombre, $descripcion, $cantidad, $fecha_actual);
    $insert->execute();

    echo "<script>alert('Artículo agregado correctamente al inventario.'); window.location='../form_inventario.php';</script>";
}

$stmt->close();
$conexion->close();
