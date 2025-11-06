<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'principal';
$pages = ['principal','citas','expediente','inventario','pago','pacientes'];
if (!in_array($page, $pages)) $page = 'principal';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ortholex — <?php echo ucfirst($page); ?></title>
  <link rel="stylesheet" href="css/index.css">
</head>
<body>

  <!-- Barra superior -->
  <div class="topbar">
    <img src="imagenes/logo" alt="Logo" class="topbar-logo">
  </div>

  <!-- Menú lateral -->
  <div class="sidebar">
    <ul class="menu">
      <li class="<?php echo ($page=='citas') ? 'active' : ''; ?>">
        <a href="?page=citas">Citas</a>
      </li>
      <li class="<?php echo ($page=='expediente') ? 'active' : ''; ?>">
        <a href="?page=expediente">Expedientes</a>
      </li>
      <li class="<?php echo ($page=='inventario') ? 'active' : ''; ?>">
        <a href="?page=inventario">Inventario</a>
      </li>
      <li class="<?php echo ($page=='pago') ? 'active' : ''; ?>">
        <a href="?page=pago">Pagos</a>
      </li>
      <li class="<?php echo ($page=='pacientes') ? 'active' : ''; ?>">
        <a href="?page=pacientes">Pacientes</a>
      </li>
      <li class="<?php echo ($page=='principal') ? 'active' : ''; ?>">
        <a href="?page=principal">Salir</a>
      </li>
    </ul>
  </div>

  <!-- Contenido dinámico -->
  <div class="main">
    <div class="content">
      <?php
        switch ($page) {
          case 'principal': include("form_paciente.php"); break;
          case 'citas': include("form_cita.php"); break;
          case 'expediente': include("form_expediente.php"); break;
          case 'inventario': include("form_inventario.php"); break;
          case 'pago': include("form_pago.php"); break;
          case 'pacientes': include("pacientes_registrados.php"); break;
        }
      ?>
    </div>
  </div>

</body>
</html>