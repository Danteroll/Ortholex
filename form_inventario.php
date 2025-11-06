<?php include("conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Artículo — Ortholex</title>
<style>
body{
  font-family:'Segoe UI',sans-serif;
  background:#f8fbff;
  margin:0;
  padding:40px;
  color:#1d3557;
}
h2{color:#1d3557}
form{
  background:#fff;
  padding:25px;
  border-radius:10px;
  max-width:450px;
  box-shadow:0 3px 6px rgba(0,0,0,0.1);
  margin-bottom:40px;
}
label{display:block;margin-top:10px;font-weight:600}
input{
  width:100%;
  padding:8px;
  margin-top:5px;
  border:1px solid #ccc;
  border-radius:6px;
}
.btn{
  margin-top:15px;
  background-color:#3b82f6;
  color:white;
  border:none;
  padding:8px 14px;
  border-radius:6px;
  cursor:pointer;
}
.btn:hover{background-color:#2563eb}
table{
  border-collapse:collapse;
  width:100%;
  background:#fff;
  box-shadow:0 2px 5px rgba(0,0,0,0.1);
}
th,td{
  border:1px solid #ddd;
  padding:10px;
  text-align:center;
}
th{
  background:#e8f1ff;
  color:#1d3557;
}
tr:hover{background:#f1f5fb}
</style>
</head>
<body>

<h2>Agregar artículo al inventario</h2>
<form method="POST" action="">
    <label>Nombre del artículo:</label>
    <input type="text" name="nombre_objeto" required>

    <label>Cantidad:</label>
    <input type="number" name="cantidad" min="0" required>

    <label>Descripción (opcional):</label>
    <input type="text" name="descripcion">

    <label>Fecha de modificación:</label>
    <input type="date" name="fecha_modificacion" required>

    <button class="btn" name="guardar">Guardar</button>
</form>

<?php
// === GUARDAR NUEVO ARTÍCULO ===
if(isset($_POST['guardar'])){
    $nombre = $_POST['nombre_objeto'];
    $cant = $_POST['cantidad'];
    $desc = $_POST['descripcion'];
    $fecha = $_POST['fecha_modificacion'];

    $stmt = $conexion->prepare("INSERT INTO inventario (nombre_objeto, descripcion, cantidad, fecha_modificacion)
                                VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $nombre, $desc, $cant, $fecha);
    $stmt->execute();

    echo "<script>alert('Artículo agregado correctamente');window.location='index.php?page=inventario';</script>";
}
?>

<!-- ===================== LISTA DE INVENTARIO ===================== -->
<h2>📋 Inventario actual</h2>
<table>
<tr>
  <th>ID</th>
  <th>Nombre del artículo</th>
  <th>Descripción</th>
  <th>Cantidad</th>
  <th>Última modificación</th>
</tr>

<?php
$sql = "SELECT * FROM inventario ORDER BY fecha_modificacion DESC";
$res = $conexion->query($sql);

if($res->num_rows > 0){
    while($row = $res->fetch_assoc()){
        echo "<tr>
                <td>{$row['id_objeto']}</td>
                <td>{$row['nombre_objeto']}</td>
                <td>{$row['descripcion']}</td>
                <td>{$row['cantidad']}</td>
                <td>{$row['fecha_modificacion']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No hay artículos registrados en el inventario</td></tr>";
}
?>
</table>

</body>
</html>
<?php $conexion->close(); ?>
