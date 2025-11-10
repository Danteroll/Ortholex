<?php
include("conexion.php");
date_default_timezone_set('America/Mexico_City');

// === Helpers ===
function leer_precio($campo) {
  // Acepta "1.234,56" o "1234,56" o "1234.56"
  $raw = isset($_POST[$campo]) ? trim($_POST[$campo]) : '';
  // Quita espacios y separadores de miles comunes
  $raw = str_replace([' ', ','], ['', '.'], $raw); // "1,234.56" -> "1.234.56" (igual sirve), "1234,56" -> "1234.56"
  return is_numeric($raw) ? (float)$raw : 0.0;
}

// === AGREGAR NUEVO ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_tratamiento'])) {
  $nombre = trim($_POST['nombre_tratamiento'] ?? '');
  $desc   = trim($_POST['descripcion'] ?? '');
  // OJO: el input se llama "precio"
  $precio = leer_precio('precio');

  if ($nombre === '' || $precio <= 0) {
    echo "<script>alert('Verifica nombre y precio (> 0).'); window.history.back();</script>";
    exit;
  }

  $stmt = $conexion->prepare("INSERT INTO tratamientos (nombre_tratamiento, descripcion, costo) VALUES (?, ?, ?)");
  $stmt->bind_param("ssd", $nombre, $desc, $precio);
  $stmt->execute();
  $stmt->close();

  echo "<script>alert('✅ Tratamiento agregado correctamente'); window.location='tratamientos.php';</script>";
  exit;
}

// === EDITAR ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_tratamiento'])) {
  $id     = intval($_POST['id_tratamiento']);
  $nombre = trim($_POST['nombre_tratamiento'] ?? '');
  $desc   = trim($_POST['descripcion'] ?? '');
  // OJO: el input se llama "precio"
  $precio = leer_precio('precio');

  if ($id <= 0 || $nombre === '' || $precio <= 0) {
    echo "<script>alert('Verifica ID, nombre y precio (> 0).'); window.history.back();</script>";
    exit;
  }

  $stmt = $conexion->prepare("UPDATE tratamientos SET nombre_tratamiento=?, descripcion=?, costo=? WHERE id_tratamiento=?");
  $stmt->bind_param("ssdi", $nombre, $desc, $precio, $id);
  $stmt->execute();
  $stmt->close();

  echo "<script>alert('✏️ Tratamiento actualizado correctamente'); window.location='tratamientos.php';</script>";
  exit;
}

// === ELIMINAR ===
if (isset($_GET['eliminar'])) {
  $id = intval($_GET['eliminar']);
  if ($id > 0) {
    $conexion->query("DELETE FROM tratamientos WHERE id_tratamiento=$id");
  }
  echo "<script>alert('🗑️ Tratamiento eliminado'); window.location='tratamientos.php';</script>";
  exit;
}

// === LISTAR ===
$tratamientos = $conexion->query("SELECT * FROM tratamientos ORDER BY id_tratamiento ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ortholex — Tratamientos</title>
  <link rel="stylesheet" href="css/inicio.css">
</head>
<body>

<div class="topbar">
  <img src="imagenes/logo" alt="Logo" class="topbar-logo">
</div>

<div class="sidebar">
  <ul class="menu">
    <li><a href="form_cita.php">Citas</a></li>
    <li><a href="pacientes.php">Pacientes</a></li>
    <li><a href="form_expediente.php">Expedientes</a></li>
    <li><a href="form_inventario.php">Inventario</a></li>
    <li><a href="form_pago.php">Pagos</a></li>
    <li><a href="tratamientos.php" class="active">Tratamientos</a></li>
  </ul>
</div>

<div class="main">
  <div class="content">
    <div class="inventario-container">
      <div class="inventario-header">
        <h2>Gestión de Tratamientos</h2>
        <button class="btn-modificar" onclick="toggleForm()">Nuevo tratamiento</button>
      </div>

      <!-- Form nuevo -->
      <div class="form-box" id="nuevoTratamiento" style="display:none;">
        <form method="POST" autocomplete="off">
          <h3>Registrar nuevo tratamiento</h3>

          <div class="input-group">
            <label>Nombre:</label>
            <input type="text" name="nombre_tratamiento" required>
          </div>

          <div class="input-group">
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3"></textarea>
          </div>

          <div class="input-group">
            <label>Precio ($):</label>
            <input type="text" name="precio" inputmode="decimal" placeholder="Ej. 750 o 750.50" required>
          </div>

          <div class="buttons">
            <button type="submit" name="guardar_tratamiento" class="btn-guardar">Guardar</button>
            <button type="button" class="btn-cancelar" onclick="toggleForm()">Cancelar</button>
          </div>
        </form>
      </div>

      <!-- Tabla -->
      <div class="tabla-inventario">
        <table>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Acciones</th>
          </tr>

          <?php if ($tratamientos && $tratamientos->num_rows > 0): ?>
            <?php while ($row = $tratamientos->fetch_assoc()): ?>
              <tr>
                <form method="POST" autocomplete="off">
                  <td><?= $row['id_tratamiento'] ?></td>
                  <td><input type="text" name="nombre_tratamiento" value="<?= htmlspecialchars($row['nombre_tratamiento']) ?>" required></td>
                  <td><input type="text" name="descripcion" value="<?= htmlspecialchars($row['descripcion']) ?>"></td>
                  <td><input type="text" name="precio" value="<?= number_format((float)$row['costo'], 2, '.', '') ?>" inputmode="decimal" required></td>
                  <td>
                    <input type="hidden" name="id_tratamiento" value="<?= $row['id_tratamiento'] ?>">
                    <button type="submit" name="editar_tratamiento" class="btn-modificar">💾 Guardar</button>
                    <a href="tratamientos.php?eliminar=<?= $row['id_tratamiento'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este tratamiento?')">🗑️</a>
                  </td>
                </form>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No hay tratamientos registrados.</td></tr>
          <?php endif; ?>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
function toggleForm() {
  const form = document.getElementById('nuevoTratamiento');
  form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
  if (form.style.display === 'block') {
    window.scrollTo({ top: form.offsetTop - 100, behavior: 'smooth' });
  }
}
</script>

<?php $conexion->close(); ?>
</body>
</html>
