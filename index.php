<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'principal';
$pages = ['principal','citas','expediente','inventario','pago'];
if (!in_array($page, $pages)) $page = 'principal';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ortholex — <?php echo ucfirst($page); ?></title>
<style>
body {
  font-family: 'Segoe UI', sans-serif;
  background: #f8fbff;
  margin: 0;
  display: flex;
}
.sidebar {
  background: #fff;
  width: 200px;
  height: 100vh;
  box-shadow: 2px 0 5px rgba(0,0,0,0.1);
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 20px;
}
.logo { font-size: 22px; font-weight: bold; color: #1d3557; margin-bottom: 10px; }
.tooth { color: #4a90e2; font-size: 26px; }
.menu a button {
  width: 150px; margin: 10px 0; padding: 10px;
  border: none; border-radius: 8px;
  background: #f1f5f9; cursor: pointer; transition: 0.2s;
}
.menu a button:hover, .active { background: #3b82f6; color: white; }
.content { flex-grow: 1; padding: 30px 50px; }
h1 { color: #1d3557; }
.btn {
  background-color: #3b82f6; color: white;
  border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer;
}
table { border-collapse: collapse; width: 90%; margin-top: 20px; background: white; }
th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
th { background: #f1f5f9; }
</style>
</head>
<body>

<div class="sidebar">
  <div class="logo">Ortho<span class="tooth">🦷</span>lex</div>
  <div class="menu">
    <a href="?page=citas"><button class="<?php echo ($page=='citas')?'active':''; ?>">Citas</button></a>
    <a href="?page=expediente"><button class="<?php echo ($page=='expediente')?'active':''; ?>">Exped.</button></a>
    <a href="?page=inventario"><button class="<?php echo ($page=='inventario')?'active':''; ?>">Inventario</button></a>
    <a href="?page=pago"><button class="<?php echo ($page=='pago')?'active':''; ?>">Pago</button></a>
    <a href="?page=pacientes"><button class="<?php echo ($page=='pacientes')?'active':''; ?>">Pacientes</button></a>
    <a href="?page=principal"><button class="<?php echo ($page=='principal')?'active':''; ?>">Salir</button></a>
</div>


</div>

<div class="content">
<?php
switch ($page) {
  case 'principal': include("form_paciente.php"); break;
  case 'citas': include("form_cita.php"); break;
  case 'expediente': include("form_expediente.php"); break;
  case 'inventario': include("form_inventario.php"); break;
  case 'pago': include("form_pago.php"); break;
  case 'pacientes': include("pacientes_registrados.php"); break;
  case 'historial': include("historial_paciente.php"); break;
}
?>
</div>
</body>
</html>
