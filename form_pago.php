<?php include("conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Pago — Ortholex</title>
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
</style>
</head>
<body>

<h2>Registrar nuevo pago</h2>
<form method="POST" action="">
    <label>Paciente:</label>
    <select name="id_paciente" required>
        <option value="">Seleccione...</option>
        <?php
        $res = $conexion->query("SELECT * FROM pacientes ORDER BY nombre ASC");
        while($p = $res->fetch_assoc()){
            echo "<option value='{$p['id_paciente']}'>{$p['nombre']}</option>";
        }
        ?>
    </select>

    <label>Servicio:</label>
    <input type="text" name="servicio" placeholder="Ej. Limpieza dental" required>

    <label>Monto:</label>
    <input type="number" name="monto" step="0.01" required>

    <label>Método de pago:</label>
    <select name="metodo_pago" required>
        <option>Efectivo</option>
        <option>Tarjeta</option>
        <option>Transferencia</option>
        <option>Otro</option>
    </select>

    <label>Fecha de pago:</label>
    <input type="date" name="fecha_pago" required>

    <button class="btn" name="guardar">Registrar pago</button>
</form>

<?php
// === GUARDAR PAGO ===
if(isset($_POST['guardar'])){
    $id_p = $_POST['id_paciente'];
    $serv = $_POST['servicio'];
    $monto = $_POST['monto'];
    $met = $_POST['metodo_pago'];
    $fecha = $_POST['fecha_pago'];

    $stmt = $conexion->prepare("INSERT INTO pagos (id_paciente, servicio, monto, metodo_pago, fecha_pago)
                                VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isdss", $id_p, $serv, $monto, $met, $fecha);
    $stmt->execute();

    echo "<script>alert('Pago registrado correctamente');window.location='index.php?page=pago';</script>";
}
?>

<!-- ===================== LISTA DE PAGOS ===================== -->
<h2>💰 Pagos registrados</h2>
<table>
<tr>
  <th>ID</th>
  <th>Paciente</th>
  <th>Servicio</th>
  <th>Monto</th>
  <th>Método</th>
  <th>Fecha</th>
</tr>

<?php
$sql = "SELECT pg.id_pago, p.nombre AS paciente, pg.servicio, pg.monto, pg.metodo_pago, pg.fecha_pago
        FROM pagos pg
        JOIN pacientes p ON pg.id_paciente = p.id_paciente
        ORDER BY pg.fecha_pago DESC";
$res = $conexion->query($sql);

if($res->num_rows > 0){
    while($row = $res->fetch_assoc()){
        $monto_format = "$" . number_format($row['monto'], 2);
        echo "<tr>
                <td>{$row['id_pago']}</td>
                <td>{$row['paciente']}</td>
                <td>{$row['servicio']}</td>
                <td>{$monto_format}</td>
                <td>{$row['metodo_pago']}</td>
                <td>{$row['fecha_pago']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No hay pagos registrados</td></tr>";
}
?>
</table>

</body>
</html>
<?php $conexion->close(); ?>
