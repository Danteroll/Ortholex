<?php
include("conexion.php");
date_default_timezone_set('America/Mexico_City');
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Ortholex</title>
  <link rel="stylesheet" href="css/inicio.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f8fbff;
      margin: 0;
      padding: 40px;
      color: #1d3557;
    }

    h2 {
      color: #1d3557
    }

    form {
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      max-width: 450px;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
      margin-bottom: 40px;
    }

    label {
      display: block;
      margin-top: 10px;
      font-weight: 600
    }

    input,
    textarea,
    select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .btn {
      margin-top: 15px;
      background-color: #3b82f6;
      color: white;
      border: none;
      padding: 8px 14px;
      border-radius: 6px;
      cursor: pointer;
    }

    .btn:hover {
      background-color: #2563eb
    }

    table {
      border-collapse: collapse;
      width: 100%;
      background: #fff;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: center;
    }

    th {
      background: #e8f1ff;
      color: #1d3557;
    }

    tr:hover {
      background: #f1f5fb
    }

    a.link {
      color: #3b82f6;
      text-decoration: none
    }

    a.link:hover {
      text-decoration: underline
    }
  </style>
</head>

<body>
  <div class="topbar">
    <img src="imagenes/logo" alt="Logo" class="topbar-logo">
  </div>

  <div class="sidebar">
    <ul class="menu">
      <li><a href="form_cita.php">Citas</a></li>
      <li><a href="pacientes.php" class="active">Pacientes</a></li>
      <li><a href="form_expediente.php">Expedientes</a></li>
      <li><a href="form_inventario.php">Inventario</a></li>
      <li><a href="form_pago.php">Pagos</a></li>
      <li><a href="tratamientos.php">Tratamientos</a></li>
      <li><a href="index.php">Salir</a></li>
    </ul>
  </div>
  <div class="main">
    <div class="content">

      <h2>Registrar nuevo expediente</h2>
      <form method="POST" action="" enctype="multipart/form-data">
        <label>Paciente:</label>
        <select name="id_paciente" required>
          <option value="">Seleccione...</option>
          <?php
          $res = $conexion->query("SELECT * FROM pacientes ORDER BY nombre ASC");
          while ($p = $res->fetch_assoc()) {
            echo "<option value='{$p['id_paciente']}'>{$p['nombre']}</option>";
          }
          ?>
        </select>

        <label>Descripción:</label>
        <textarea name="descripcion" required></textarea>

        <label>Archivo (PDF o imagen):</label>
        <input type="file" name="archivo" accept=".pdf,.jpg,.jpeg,.png" required>

        <button class="btn" name="guardar">Guardar expediente</button>
      </form>

      <?php
      // === Guarda el expediente ===
      if (isset($_POST['guardar'])) {
        $id_p = $_POST['id_paciente'];
        $desc = $_POST['descripcion'];
        // Genera la fecha y hora actual automáticamente
        $fecha_actual = date('Y-m-d H:i:s');
        // === Guarda el archivo ===
        $nombreArchivo = $_FILES['archivo']['name'];
        if (!is_dir("uploads")) mkdir("uploads");
        $ruta = "uploads/" . time() . "_" . basename($nombreArchivo);
        move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta);

        // === Inserta en BD ===
        $stmt = $conexion->prepare("
            INSERT INTO expedientes (id_paciente, descripcion, archivo, fecha_subida)
            VALUES (?, ?, ?, ?)
          ");
        $stmt->bind_param("isss", $id_p, $desc, $ruta, $fecha_actual);
        $stmt->execute();
        echo "<script>alert('Expediente guardado correctamente');window.location='form_expediente.php';</script>";
      }
      ?>

      <!-- ===================== LISTA DE EXPEDIENTES ===================== -->
      <h2>Expedientes registrados</h2>
      <table class="tabla-inventario">
        <tr>
          <th>ID</th>
          <th>Paciente</th>
          <th>Descripción</th>
          <th>Archivo</th>
          <th>Fecha de subida</th>
        </tr>

        <?php
        $sql = "SELECT e.id_expediente, p.nombre AS paciente, e.descripcion, e.archivo, e.fecha_subida
        FROM expedientes e
        JOIN pacientes p ON e.id_paciente = p.id_paciente
        ORDER BY e.fecha_subida DESC";
        $res = $conexion->query($sql);
        ?>

        <?php if ($res && $res->num_rows > 0): ?>
          <?php while ($row = $res->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id_expediente'] ?></td>
              <td><?= htmlspecialchars($row['paciente']) ?></td>
              <td><?= htmlspecialchars($row['descripcion']) ?></td>
              <td>
                <?php if (!empty($row['archivo'])): ?>
                  <a class="link" href="<?= htmlspecialchars($row['archivo']) ?>" target="_blank">Ver archivo</a>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
              <td><?= date('d-m-Y H:i', strtotime($row['fecha_subida'])) ?> hrs</td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5">No hay expedientes registrados</td>
          </tr>
        <?php endif; ?>

      </table>

    </div>
  </div>
  <script>
    // Bloquea navegación con botones "Atrás" y "Adelante"
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
<?php $conexion->close(); ?>