<?php
include("conexion.php");
date_default_timezone_set('America/Mexico_City');

// === AGREGAR NUEVO ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_tratamiento'])) {
  $nombre = trim($_POST['nombre_tratamiento']);
  $desc = trim($_POST['descripcion']);
  $precio = floatval($_POST['costo']);

  $stmt = $conexion->prepare("INSERT INTO tratamientos (nombre_tratamiento, descripcion, costo) VALUES (?, ?, ?)");
  $stmt->bind_param("ssd", $nombre, $desc, $precio);
  $stmt->execute();

  echo "<script>alert('✅ Tratamiento agregado correctamente'); window.location='tratamientos.php';</script>";
  exit;
}

// === EDITAR ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_tratamiento'])) {
  $id = intval($_POST['id_tratamiento']);
  $nombre = trim($_POST['nombre_tratamiento']);
  $desc = trim($_POST['descripcion']);
  $precio = floatval($_POST['costo']);

  $stmt = $conexion->prepare("UPDATE tratamientos SET nombre_tratamiento=?, descripcion=?, costo=? WHERE id_tratamiento=?");
  $stmt->bind_param("ssdi", $nombre, $desc, $precio, $id);
  $stmt->execute();

  echo "<script>alert('✏️ Tratamiento actualizado correctamente'); window.location='tratamientos.php';</script>";
  exit;
}

// === ELIMINAR ===
if (isset($_GET['eliminar'])) {
  $id = intval($_GET['eliminar']);
  $conexion->query("DELETE FROM tratamientos WHERE id_tratamiento=$id");
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

<!-- Barra superior -->
<div class="topbar">
  <img src="imagenes/logo" alt="Logo" class="topbar-logo">
</div>

<!-- Sidebar -->
  <div class="sidebar">
    <ul class="menu">
      <li><a href="form_cita.php">Citas</a></li>
      <li><a href="pacientes.php" class="active">Pacientes</a></li>
      <li><a href="form_expediente.php">Expedientes</a></li>
      <li><a href="form_inventario.php">Inventario</a></li>
      <li><a href="form_pago.php">Pagos</a></li>
      <li><a href="tratamientos.php">Tratamientos</a></li>
    </ul>
  </div>

<!-- Contenido principal -->
<div class="main">
  <div class="content">
    <div class="inventario-container">
      <div class="inventario-header">
        <h2>Gestión de Tratamientos</h2>
        <button class="btn-modificar" onclick="toggleForm()">Nuevo tratamiento</button>
      </div>

      <!-- 🦷 Formulario nuevo tratamiento -->
      <div class="form-box" id="nuevoTratamiento" style="display:none;">
        <form method="POST">
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
            <input type="number" step="0.01" name="precio" required>
          </div>

          <div class="buttons">
            <button type="submit" name="guardar_tratamiento" class="btn-guardar">Guardar</button>
            <button type="button" class="btn-cancelar" onclick="toggleForm()">Cancelar</button>
          </div>
        </form>
      </div>

      <!-- 📋 Tabla de tratamientos -->
      <div class="tabla-inventario">
        <table>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Acciones</th>
          </tr>

          <?php if ($tratamientos->num_rows > 0): ?>
            <?php while ($row = $tratamientos->fetch_assoc()): ?>
              <tr>
                <form method="POST">
                  <td><?= $row['id_tratamiento'] ?></td>
                  <td><input type="text" name="nombre_tratamiento" value="<?= htmlspecialchars($row['nombre_tratamiento']) ?>" required></td>
                  <td><input type="text" name="descripcion" value="<?= htmlspecialchars($row['descripcion']) ?>"></td>
                  <td><input type="number" step="0.01" name="precio" value="<?= $row['costo'] ?>" required></td>
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
  window.scrollTo({ top: form.offsetTop - 100, behavior: 'smooth' });
}
</script>

<?php $conexion->close(); ?>
</body>
</html>
