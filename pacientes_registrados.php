<?php include("conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pacientes Registrados — Ortholex</title>
<style>
body{
  font-family:'Segoe UI',sans-serif;
  background:#f8fbff;
  margin:0;
  padding:40px;
  color:#1d3557;
}
h2{color:#1d3557}
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
.btn{
  background:#3b82f6;
  color:white;
  border:none;
  padding:6px 10px;
  border-radius:6px;
  cursor:pointer;
}
.btn:hover{background:#2563eb}
.search{
  margin-bottom:20px;
  display:flex;
  justify-content:space-between;
  flex-wrap:wrap;
  gap:10px;
}
input[type=text]{
  padding:8px;
  width:250px;
  border:1px solid #ccc;
  border-radius:6px;
}
</style>
</head>
<body>

<h2>👤 Pacientes Registrados</h2>

<div class="search">
  <form method="GET" action="">
    <input type="text" name="buscar" placeholder="Buscar por nombre o teléfono" value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>">
    <button class="btn">Buscar</button>
  </form>
  <a href="form_paciente.php"><button class="btn">+ Nuevo paciente</button></a>
</div>

<table>
<tr>
  <th>ID</th>
  <th>Nombre</th>
  <th>Edad</th>
  <th>Celular</th>
  <th>Profesión</th>
  <th>Historial</th>
</tr>

<?php
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : "";
if($buscar){
    $stmt = $conexion->prepare("SELECT * FROM pacientes WHERE nombre LIKE ? OR celular LIKE ?");
    $term = "%$buscar%";
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = $conexion->query("SELECT * FROM pacientes ORDER BY fecha_registro DESC");
}

if($res->num_rows > 0){
    while($row = $res->fetch_assoc()){
        echo "<tr>
                <td>{$row['id_paciente']}</td>
                <td>{$row['nombre']}</td>
                <td>{$row['edad']}</td>
                <td>{$row['celular']}</td>
                <td>{$row['profesion']}</td>
                <td><a href='historial_paciente.php?id={$row['id_paciente']}'><button class='btn'>Ver historial</button></a></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No se encontraron pacientes.</td></tr>";
}
?>
</table>

</body>
</html>
<?php $conexion->close(); ?>
