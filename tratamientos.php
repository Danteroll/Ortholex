<?php
include("conexion.php");

// --- CONTROL DE ACCIONES ---
$accion = $_GET['accion'] ?? 'listar';

// === AGREGAR NUEVO ===
if ($accion == 'agregar' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre_tratamiento'];
    $desc = $_POST['descripcion'];
    $costo = $_POST['costo'];

    $stmt = $conexion->prepare("INSERT INTO tratamientos (nombre_tratamiento, descripcion, costo) VALUES (?,?,?)");
    $stmt->bind_param("ssd", $nombre, $desc, $costo);
    $stmt->execute();

    header("Location: tratamientos.php?msg=agregado");
    exit;
}

// === ACTUALIZAR ===
if ($accion == 'editar' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_tratamiento'];
    $nombre = $_POST['nombre_tratamiento'];
    $desc = $_POST['descripcion'];
    $costo = $_POST['costo'];

    $stmt = $conexion->prepare("UPDATE tratamientos SET nombre_tratamiento=?, descripcion=?, costo=? WHERE id_tratamiento=?");
    $stmt->bind_param("ssdi", $nombre, $desc, $costo, $id);
    $stmt->execute();

    header("Location: tratamientos.php?msg=actualizado");
    exit;
}

// === ELIMINAR ===
if ($accion == 'eliminar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conexion->query("DELETE FROM tratamientos WHERE id_tratamiento=$id");
    header("Location: tratamientos.php?msg=eliminado");
    exit;
}

// === CARGAR DATOS PARA EDITAR ===
if ($accion == 'form_editar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $res = $conexion->query("SELECT * FROM tratamientos WHERE id_tratamiento=$id");
    $trat = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Gestión de Tratamientos</title>
<link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<h2>🦷 Tratamientos</h2>

<?php
// Mensajes
if (isset($_GET['msg'])) {
  $m = $_GET['msg'];
  if ($m == 'agregado') echo "<p style='color:green'>✅ Tratamiento agregado</p>";
  if ($m == 'actualizado') echo "<p style='color:green'>✅ Tratamiento actualizado</p>";
  if ($m == 'eliminado') echo "<p style='color:red'>🗑️ Tratamiento eliminado</p>";
}
?>

<?php if ($accion == 'listar'): ?>

  <a href="tratamientos.php?accion=form_agregar">➕ Agregar nuevo tratamiento</a>
  <br><br>

  <table border="1" cellpadding="6">
    <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Costo</th><th>Acciones</th></tr>
    <?php
    $result = $conexion->query("SELECT * FROM tratamientos ORDER BY id_tratamiento");
    while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id_tratamiento'] ?></td>
        <td><?= htmlspecialchars($row['nombre_tratamiento']) ?></td>
        <td><?= htmlspecialchars($row['descripcion']) ?></td>
        <td>$<?= number_format($row['costo'],2) ?></td>
        <td>
          <a href="tratamientos.php?accion=form_editar&id=<?= $row['id_tratamiento'] ?>">✏️ Editar</a> |
          <a href="tratamientos.php?accion=eliminar&id=<?= $row['id_tratamiento'] ?>" onclick="return confirm('¿Eliminar este tratamiento?')">🗑️ Eliminar</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

<?php elseif ($accion == 'form_agregar'): ?>

  <h3>Agregar tratamiento</h3>
  <form method="POST" action="tratamientos.php?accion=agregar">
    <label>Nombre:</label>
    <input name="nombre_tratamiento" required><br>
    <label>Descripción:</label>
    <textarea name="descripcion"></textarea><br>
    <label>Costo:</label>
    <input type="number" step="0.01" name="costo" required><br>
    <button type="submit">Guardar</button>
    <a href="tratamientos.php">Cancelar</a>
  </form>

<?php elseif ($accion == 'form_editar'): ?>

  <h3>Editar tratamiento</h3>
  <form method="POST" action="tratamientos.php?accion=editar">
    <input type="hidden" name="id_tratamiento" value="<?= $trat['id_tratamiento'] ?>">
    <label>Nombre:</label>
    <input name="nombre_tratamiento" value="<?= htmlspecialchars($trat['nombre_tratamiento']) ?>" required><br>
    <label>Descripción:</label>
    <textarea name="descripcion"><?= htmlspecialchars($trat['descripcion']) ?></textarea><br>
    <label>Costo:</label>
    <input type="number" step="0.01" name="costo" value="<?= $trat['costo'] ?>" required><br>
    <button type="submit">Actualizar</button>
    <a href="tratamientos.php">Cancelar</a>
  </form>

<?php endif; ?>

</body>
</html>
