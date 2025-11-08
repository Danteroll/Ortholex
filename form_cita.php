<?php include("conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nueva Cita — Ortholex</title>
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
input,select{
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

div.cita_NP {
    margin-top: 10px;
    
}
</style>
</head>
<body>

<h2>Registrar nueva cita</h2>
<form method="POST" action="">
    <label>Paciente:</label>
    <select name="id_paciente" required>
        <option value="">Seleccione...</option>
        <?php
        $res = $conexion->query("SELECT * FROM pacientes");
        while($p = $res->fetch_assoc()){
            echo "<option value='{$p['id_paciente']}'>{$p['nombre']}</option>";
        }
        ?>
    </select>

    <label>Fecha:</label>
    <input type="date" name="fecha" required>

    <label>Hora:</label>
    <input type="time" name="hora" required>

    <label>Motivo:</label>
    <input type="text" name="motivo" placeholder="Ej. revisión, limpieza..." required>

    <button class="btn" name="guardar">Guardar cita</button>

</form>

<div class="cita_NP">
  <a class="btn" href="form_paciente.php" role="button">Nuevo paciente</a>
</div>

<?php
// === GUARDAR CITA ===
if(isset($_POST['guardar'])){
    $id_p = $_POST['id_paciente'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $motivo = $_POST['motivo'];

    $stmt = $conexion->prepare("INSERT INTO citas (id_paciente, fecha, hora, motivo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $id_p, $fecha, $hora, $motivo);
    $stmt->execute();

    echo "<script>alert('Cita guardada correctamente');window.location='index.php?page=citas';</script>";
}
?>

<!-- ===================== LISTA DE CITAS ===================== -->
<h2>Citas registradas</h2>
<table>
<tr>
  <th>ID</th>
  <th>Paciente</th>
  <th>Fecha</th>
  <th>Hora</th>
  <th>Motivo</th>
  <th>Estado</th>
</tr>

<?php
$sql = "SELECT c.id_cita, p.nombre AS paciente, c.fecha, c.hora, c.motivo, c.estado 
        FROM citas c 
        JOIN pacientes p ON c.id_paciente = p.id_paciente 
        ORDER BY c.fecha DESC, c.hora ASC";
$res = $conexion->query($sql);

if($res->num_rows > 0){
    while($row = $res->fetch_assoc()){
        echo "<tr>
                <td>{$row['id_cita']}</td>
                <td>{$row['paciente']}</td>
                <td>{$row['fecha']}</td>
                <td>{$row['hora']}</td>
                <td>{$row['motivo']}</td>
                <td>{$row['estado']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No hay citas registradas</td></tr>";
}
?>
</table>

</body>
</html>
<?php $conexion->close(); ?>
