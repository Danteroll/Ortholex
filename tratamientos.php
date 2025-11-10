<?php
include("conexion.php");
date_default_timezone_set('America/Mexico_City');

// === AGREGAR NUEVO ===
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar_tratamiento'])) {
  $nombre = trim($_POST['nombre_tratamiento'] ?? '');
  $desc   = trim($_POST['descripcion'] ?? '');
  $precio = floatval($_POST['precio']);

  if ($nombre === '' || $precio <= 0) {
    echo "<script>alert('Verifica el nombre y precio (> 0).'); window.history.back();</script>";
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
  $precio = floatval($_POST['precio']);

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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_tratamiento'])) {
  $id = intval($_POST['id_tratamiento']);
  if ($id > 0) {
    $conexion->query("DELETE FROM tratamientos WHERE id_tratamiento=$id");
    echo "<script>alert('🗑️ Tratamiento eliminado correctamente'); window.location='tratamientos.php';</script>";
    exit;
  } else {
    echo "<script>alert('Seleccione un tratamiento válido.');</script>";
  }
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
    <li><a href="form_inventario.php">Inventario</a></li>
    <li><a href="form_pago.php">Pagos</a></li>
    <li><a href="tratamientos.php" class="active">Tratamientos</a></li>
    <li><a href="index.php">Salir</a></li>
  </ul>
</div>

<div class="main">
  <div class="content">
    <div class="inventario-container">
      <div class="inventario-header">
        <h2>Gestión de Tratamientos</h2>
        <div style="display:flex;gap:10px;">
          <button class="btn-modificar" onclick="toggleNuevo()">Nuevo tratamiento</button>
          <button class="btn-modificar" onclick="toggleEditar()">Modificar tratamiento</button>
          <button class="btn-eliminar" onclick="toggleEliminar()">Eliminar tratamiento</button>
        </div>
      </div>

      <!-- 🆕 Formulario: nuevo tratamiento -->
      <div class="form-box" id="formNuevo" style="display:none;">
        <form method="POST">
          <h3>Registrar nuevo tratamiento</h3>
          <div class="input-group">
            <label>Nombre:</label>
            <input type="text" name="nombre_tratamiento" required>
          </div>
          <div class="input-group">
            <label>Descripción:</label>
            <textarea name="descripcion" rows="3" placeholder="Escribe una breve descripción..."></textarea>
          </div>
          <div class="input-group">
            <label>Precio ($):</label>
            <input type="number" step="0.01" name="precio" required>
          </div>
          <div class="buttons">
            <button type="submit" name="guardar_tratamiento" class="btn-guardar">Guardar</button>
            <button type="button" class="btn-cancelar" onclick="cerrarNuevo()">Cancelar</button>
          </div>
        </form>
      </div>

      <!-- ✏️ Formulario: editar tratamiento -->
      <div class="form-box" id="formEditar" style="display:none;">
        <form method="POST">
          <h3>Modificar tratamiento</h3>
          <div class="input-group">
            <label for="id_tratamiento">Seleccionar tratamiento:</label>
            <select id="id_tratamiento" name="id_tratamiento" required onchange="rellenarDatos()">
              <option value="">Seleccione...</option>
              <?php
              if ($tratamientos && $tratamientos->num_rows > 0) {
                $tratamientos->data_seek(0);
                while ($t = $tratamientos->fetch_assoc()) {
                  echo "<option value='{$t['id_tratamiento']}'
                            data-nombre='".htmlspecialchars($t['nombre_tratamiento'])."'
                            data-desc='".htmlspecialchars($t['descripcion'])."'
                            data-precio='".number_format((float)$t['costo'], 2, '.', '')."'>
                            {$t['nombre_tratamiento']} — $".number_format((float)$t['costo'], 2)."
                          </option>";
                }
              }
              ?>
            </select>
          </div>
          <div class="input-group">
            <label>Nombre:</label>
            <input type="text" id="nombre_tratamiento" name="nombre_tratamiento" required>
          </div>
          <div class="input-group">
            <label>Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="3"></textarea>
          </div>
          <div class="input-group">
            <label>Precio ($):</label>
            <input type="number" step="0.01" id="precio" name="precio" required>
          </div>
          <div class="buttons">
            <button type="submit" name="editar_tratamiento" class="btn-guardar">Guardar cambios</button>
            <button type="button" class="btn-cancelar" onclick="cerrarEditar()">Cancelar</button>
          </div>
        </form>
      </div>

      <!-- 🗑️ Formulario: eliminar tratamiento -->
      <div class="form-box" id="formEliminar" style="display:none;">
        <form method="POST">
          <h3>Eliminar tratamiento</h3>
          <div class="input-group">
            <label for="id_tratamiento_eliminar">Seleccione el tratamiento:</label>
            <select id="id_tratamiento_eliminar" name="id_tratamiento" required>
              <option value="">Seleccione...</option>
              <?php
              $resEliminar = $conexion->query("SELECT id_tratamiento, nombre_tratamiento, costo FROM tratamientos ORDER BY nombre_tratamiento ASC");
              if ($resEliminar && $resEliminar->num_rows > 0) {
                while ($t = $resEliminar->fetch_assoc()) {
                  echo "<option value='{$t['id_tratamiento']}'>{$t['nombre_tratamiento']} — $".number_format((float)$t['costo'], 2)."</option>";
                }
              } else {
                echo "<option value=''>No hay tratamientos registrados</option>";
              }
              ?>
            </select>
          </div>
          <div class="buttons">
            <button type="submit" name="eliminar_tratamiento" class="btn-eliminar">Eliminar</button>
            <button type="button" class="btn-cancelar" onclick="cerrarEliminar()">Cancelar</button>
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
          </tr>
          <?php
          $tratamientos = $conexion->query("SELECT * FROM tratamientos ORDER BY id_tratamiento ASC");
          if ($tratamientos && $tratamientos->num_rows > 0):
            while ($row = $tratamientos->fetch_assoc()):
          ?>
            <tr>
              <td><?= $row['id_tratamiento'] ?></td>
              <td><?= htmlspecialchars($row['nombre_tratamiento']) ?></td>
              <td><?= htmlspecialchars($row['descripcion']) ?></td>
              <td>$<?= number_format((float)$row['costo'], 2) ?></td>
            </tr>
          <?php endwhile; else: ?>
            <tr><td colspan="4" style="text-align:center;padding:20px;color:#555;">No hay tratamientos registrados.</td></tr>
          <?php endif; ?>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
let nuevoAbierto = false;
let editarAbierto = false;
let eliminarAbierto = false;

function toggleNuevo() {
  const form = document.getElementById('formNuevo');
  if (!nuevoAbierto) {
    form.style.display = 'block';
    nuevoAbierto = true;
    editarAbierto = false;
    eliminarAbierto = false;
    document.getElementById('formEditar').style.display = 'none';
    document.getElementById('formEliminar').style.display = 'none';
  }
}

function toggleEditar() {
  const form = document.getElementById('formEditar');
  if (!editarAbierto) {
    form.style.display = 'block';
    editarAbierto = true;
    nuevoAbierto = false;
    eliminarAbierto = false;
    document.getElementById('formNuevo').style.display = 'none';
    document.getElementById('formEliminar').style.display = 'none';
  }
}

function toggleEliminar() {
  const form = document.getElementById('formEliminar');
  if (!eliminarAbierto) {
    form.style.display = 'block';
    eliminarAbierto = true;
    nuevoAbierto = false;
    editarAbierto = false;
    document.getElementById('formNuevo').style.display = 'none';
    document.getElementById('formEditar').style.display = 'none';
  }
}

function cerrarNuevo() {
  document.getElementById('formNuevo').style.display = 'none';
  nuevoAbierto = false;
}
function cerrarEditar() {
  document.getElementById('formEditar').style.display = 'none';
  editarAbierto = false;
}
function cerrarEliminar() {
  document.getElementById('formEliminar').style.display = 'none';
  eliminarAbierto = false;
}

function rellenarDatos() {
  const select = document.getElementById('id_tratamiento');
  const opt = select.options[select.selectedIndex];
  if (!opt) return;
  document.getElementById('nombre_tratamiento').value = opt.getAttribute('data-nombre') || '';
  document.getElementById('descripcion').value = opt.getAttribute('data-desc') || '';
  document.getElementById('precio').value = opt.getAttribute('data-precio') || '';
}

// 🚫 Bloquear navegación con botones "Atrás" y "Adelante"
(function () {
  window.history.pushState(null, "", window.location.href);
  window.onpopstate = function () {
    window.history.pushState(null, "", window.location.href);
  };
})();
</script>

<style>
.input-group textarea,
.input-group select,
.input-group input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 8px;
  font-size: 15px;
  font-family: 'Segoe UI', sans-serif;
  color: #333;
  background-color: #fff;
  transition: border-color 0.3s, box-shadow 0.3s;
  appearance: none;
  box-sizing: border-box;
}
.input-group textarea:focus,
.input-group select:focus,
.input-group input:focus {
  border-color: #a16976;
  box-shadow: 0 0 4px rgba(161,105,118,0.4);
  outline: none;
  background-color: #fffafc;
}
</style>

<?php $conexion->close(); ?>
</body>
</html>




