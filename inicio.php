<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Ortholex</title>
  <link rel="stylesheet" href="css/inicio.css">
</head>

<body>
  <div class="topbar">
    <img src="imagenes/logo" alt="Logo" class="topbar-logo">
  </div>

  <div class="sidebar">
    <ul class="menu">
      <li><a href="form_cita.php">Citas</a></li>
      <li><a href="pacientes.php" class="active">Pacientes</a></li>
      <li><a href="form_inventario.php">Inventario</a></li>
      <li><a href="form_pago.php">Pagos</a></li>
      <li><a href="tratamientos.php">Tratamientos</a></li>
      <li><a href="index.php">Salir</a></li>
    </ul>
  </div>

  <div class="main">
    <div class="content">
      <div class="bienvenida-centro">
        <h1>Bienvenida al Sistema DEADJB</h1>
        <p>Seleccione una opción del menú lateral para comenzar.</p>
      </div>
    </div>
  </div>
  <script>
    // Bloquea la navegación con botones "Atrás" y "Adelante"
    (function() {
      // Limpia el historial actual para evitar retroceso
      window.history.pushState(null, "", window.location.href);
      window.onpopstate = function() {
        window.history.pushState(null, "", window.location.href);
      };
    })();
  </script>
</body>

</html>