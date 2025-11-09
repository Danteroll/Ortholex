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
  <link rel="stylesheet" href="css/inicio.css">
  <script>
    // 🔒 Verificar inicio de sesión
    document.addEventListener("DOMContentLoaded", () => {
      const logueado = sessionStorage.getItem("logueado");
      if (logueado !== "true") {
        window.location.href = "index.php"; // redirige al login si no hay sesión
      }
    });

    // 🚫 Bloquear botones de navegador
    history.pushState(null, null, location.href);
    window.onpopstate = function () {
      history.pushState(null, null, location.href);
    };
  </script>
</head>
<body>

  <!-- Barra superior -->
  <div class="topbar">
    <img src="imagenes/logo" alt="Logo" class="topbar-logo">
  </div>

  <!-- Menú lateral -->
  <div class="sidebar">
    <ul class="menu">
      <li class="<?php echo ($page=='citas') ? 'active' : ''; ?>"><a href="?page=citas">Citas</a></li>
      <li class="<?php echo ($page=='expediente') ? 'active' : ''; ?>"><a href="?page=expediente">Expedientes</a></li>
      <li class="<?php echo ($page=='inventario') ? 'active' : ''; ?>"><a href="?page=inventario">Inventario</a></li>
      <li class="<?php echo ($page=='pago') ? 'active' : ''; ?>"><a href="?page=pago">Pagos</a></li>
      <li><a href="#" id="logout">Salir</a></li>
    </ul>
  </div>

  <!-- Contenido principal -->
  <div class="main">
    <div class="content">
      <?php
        if ($page == 'principal') {
          echo '<div class="bienvenida-centro">';
          echo '  <h1>Bienvenida al Sistema DEADBJ</h1>';
          echo '  <p>Seleccione una opción del menú lateral para comenzar.</p>';
          echo '</div>';
        } else {
          switch ($page) {
            case "citas": include("form_cita.php"); break;
            case "expediente": include("pacientes_registrados.php"); break;
            case "inventario": include("form_inventario.php"); break;
            case "pago": include("form_pago.php"); break;
            case 'paciente': include("form_historia_clinica.php"); break;
          }
        }
      ?>


    </div>
  </div>

  <script>
    // 🔓 Cerrar sesión
    document.getElementById("logout").addEventListener("click", function(e) {
      e.preventDefault();
      sessionStorage.removeItem("logueado");
      window.location.href = "index.php"; // vuelve al login
    });
  </script>

</body>
</html>


