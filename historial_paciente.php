<?php include("conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Historial del Paciente — Ortholex</title>
<style>
body{font-family:'Segoe UI',sans-serif;background:#f8fbff;margin:0;padding:40px;color:#1d3557}
h2{color:#1d3557;margin-top:40px}
table{border-collapse:collapse;width:100%;background:#fff;margin-top:10px;box-shadow:0 2px 5px rgba(0,0,0,0.1)}
th,td{border:1px solid #ddd;padding:10px;text-align:center}
th{background:#e8f1ff;color:#1d3557}
tr:hover{background:#f1f5fb}
.back{background:#3b82f6;color:#fff;padding:8px 14px;border-radius:6px;text-decoration:none}
.back:hover{background:#2563eb}
</style>
</head>
<body>

<?php
$id = intval($_GET['id'] ?? 0);
if($id <= 0){ echo "<p>ID inválido.</p>"; exit; }

$p = $conexion->query("SELECT * FROM pacientes WHERE id_paciente=$id")->fetch_assoc();
if(!$p){ echo "<p>Paciente no encontrado.</p>"; exit; }

echo "<h1>Historial de {$p['nombre']}</h1>";
echo "<a href='pacientes_registrados.php' class='back'>← Volver</a>";

// === Citas ===
echo "<h2>📅 Citas</h2>";
$res = $conexion->query("SELECT * FROM citas WHERE id_paciente=$id ORDER BY fecha DESC");
if($res->num_rows > 0){
    echo "<table><tr><th>Fecha</th><th>Hora</th><th>Motivo</th><th>Estado</th></tr>";
    while($r = $res->fetch_assoc()){
        echo "<tr><td>{$r['fecha']}</td><td>{$r['hora']}</td><td>{$r['motivo']}</td><td>{$r['estado']}</td></tr>";
    }
    echo "</table>";
} else echo "<p>No tiene citas registradas.</p>";

// === Pagos ===
echo "<h2>💰 Pagos</h2>";
$res = $conexion->query("SELECT * FROM pagos WHERE id_paciente=$id ORDER BY fecha_pago DESC");
if($res->num_rows > 0){
    echo "<table><tr><th>Servicio</th><th>Monto</th><th>Método</th><th>Fecha</th></tr>";
    while($r = $res->fetch_assoc()){
        $m = "$" . number_format($r['monto'],2);
        echo "<tr><td>{$r['servicio']}</td><td>$m</td><td>{$r['metodo_pago']}</td><td>{$r['fecha_pago']}</td></tr>";
    }
    echo "</table>";
} else echo "<p>No hay pagos registrados.</p>";

// === Expedientes ===
echo "<h2>📂 Expedientes</h2>";
$res = $conexion->query("SELECT * FROM expedientes WHERE id_paciente=$id ORDER BY fecha_subida DESC");
if($res->num_rows > 0){
    echo "<table><tr><th>Descripción</th><th>Archivo</th><th>Fecha</th></tr>";
    while($r = $res->fetch_assoc()){
        $a = $r['archivo'] ? "<a href='{$r['archivo']}' target='_blank'>Ver archivo</a>" : "—";
        echo "<tr><td>{$r['descripcion']}</td><td>$a</td><td>{$r['fecha_subida']}</td></tr>";
    }
    echo "</table>";
} else echo "<p>No hay expedientes.</p>";

// === Historia clínica ===
echo "<h2>🩺 Historia clínica</h2>";
$res = $conexion->query("SELECT * FROM historia_clinica WHERE id_paciente=$id ORDER BY fecha DESC");
if($res->num_rows > 0){
    echo "<table><tr><th>Lugar</th><th>Fecha</th><th>Diagnóstico</th><th>Plan</th></tr>";
    while($r = $res->fetch_assoc()){
        echo "<tr><td>{$r['lugar']}</td><td>{$r['fecha']}</td><td>{$r['diagnostico']}</td><td>{$r['plan']}</td></tr>";
    }
    echo "</table>";
} else echo "<p>No hay historia clínica registrada.</p>";

$conexion->close();
?>

</body>
</html>
