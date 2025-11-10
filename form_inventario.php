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

      <!-- Encabezado del módulo -->
      <div class="inventario-header">
        <h2>Inventario actual</h2>
        <button id="btnModificar" class="btn-modificar">Modificar inventario</button>
      </div>

      <!-- Selección de acción -->
      <div id="accionesInventario" class="acciones-box" style="display:none;">
        <h3>Seleccione una acción:</h3>
        <div class="acciones-buttons">
          <button id="btnAgregar" class="btn-accion agregar">Agregar artículo</button>
          <button id="btnEliminar" class="btn-accion eliminar">Eliminar artículo</button>
          <button id="btnCancelarAccion" class="btn-cancelar">Cancelar</button>
        </div>
      </div>

      <!-- Formulario AGREGAR -->
      <div id="formAgregar" class="form-box" style="display:none;">
        <h3>Agregar artículo al inventario</h3>
        <form action="funciones/guardar_inventario.php" method="post" autocomplete="off">
          <div class="input-group">
            <label for="nombre">Nombre del artículo:</label>
            <input type="text" id="nombre" name="nombre" required>
          </div>

          <div class="input-group">
            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" required min="1">
          </div>

          <div class="input-group">
            <label for="descripcion">Descripción (opcional):</label>
            <input type="text" id="descripcion" name="descripcion">
          </div>

          <div class="input-group">
            <label>Fecha de modificación:</label>
            <input type="text" name="fecha" value="<?= date('d-m-Y H:i') ?> hrs" readonly>
          </div>

          <div class="buttons">
            <button type="submit" class="btn-guardar">Guardar</button>
            <button type="button" id="btnCancelarAgregar" class="btn-cancelar">Cancelar</button>
          </div>
        </form>
      </div>

      <!-- Formulario ELIMINAR -->
      <!-- Formulario ELIMINAR -->
<div id="formEliminar" class="form-box" style="display:none;">
  <h3>Eliminar artículo del inventario</h3>
  <form action="funciones/eliminar_inventario.php" method="post" autocomplete="off">
    
    <div class="input-group">
      <label for="idEliminar">Seleccione el artículo:</label>
      <select id="idEliminar" name="idEliminar" required>
        <option value="">Seleccione un artículo...</option>
        <?php
          $articulos = $conexion->query("SELECT id_objeto, nombre_objeto, cantidad FROM inventario ORDER BY nombre_objeto ASC");
          if ($articulos->num_rows > 0) {
            while ($a = $articulos->fetch_assoc()) {
              echo "<option value='{$a['id_objeto']}'>
                      {$a['nombre_objeto']} (Existencias: {$a['cantidad']})
                    </option>";
            }
          } else {
            echo "<option value=''>No hay artículos registrados</option>";
          }
        ?>
      </select>
    </div>

    <div class="input-group">
      <label for="cantidadEliminar">Cantidad a eliminar:</label>
      <input type="number" id="cantidadEliminar" name="cantidadEliminar" required min="1" placeholder="Ej. 5">
    </div>

    <div class="buttons">
      <button type="submit" class="btn-eliminar">Eliminar</button>
      <button type="button" id="btnCancelarEliminar" class="btn-cancelar">Cancelar</button>
    </div>
  </form>
</div>


      <!-- Tabla de inventario -->
<div class="tabla-inventario">
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre del artículo</th>
        <th>Descripción</th>
        <th>Cantidad</th>
        <th>Última modificación</th>
        <th>Acción</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $res = $conexion->query("SELECT * FROM inventario ORDER BY id_objeto ASC");
      if ($res->num_rows > 0):
        while ($fila = $res->fetch_assoc()):
      ?>
        <tr>
          <form method="POST" action="funciones/editar_inventario.php">
            <td><?= $fila['id_objeto'] ?></td>
            <td>
              <input type="text" name="nombre_objeto" value="<?= htmlspecialchars($fila['nombre_objeto']) ?>" required>
            </td>
            <td>
              <input type="text" name="descripcion" value="<?= htmlspecialchars($fila['descripcion']) ?>">
            </td>
            <td>
              <input type="number" name="cantidad" value="<?= $fila['cantidad'] ?>" min="0" required>
            </td>
            <td><?= date('d-m-Y H:i', strtotime($fila['fecha_modificacion'])) ?> hrs</td>
            <td>
              <input type="hidden" name="id_objeto" value="<?= $fila['id_objeto'] ?>">
              <button type="submit" class="btn-modificar">💾 Guardar</button>
            </td>
          </form>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="6">No hay artículos registrados en el inventario</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>


  <script>
    // Referencias
    const btnModificar = document.getElementById("btnModificar");
    const accionesBox = document.getElementById("accionesInventario");
    const btnAgregar = document.getElementById("btnAgregar");
    const btnEliminar = document.getElementById("btnEliminar");
    const formAgregar = document.getElementById("formAgregar");
    const formEliminar = document.getElementById("formEliminar");
    const btnCancelarAccion = document.getElementById("btnCancelarAccion");
    const btnCancelarAgregar = document.getElementById("btnCancelarAgregar");
    const btnCancelarEliminar = document.getElementById("btnCancelarEliminar");

    // Mostrar opciones
    btnModificar.addEventListener("click", () => {
      accionesBox.style.display = "block";
      btnModificar.style.display = "none";
    });

    // Agregar
    btnAgregar.addEventListener("click", () => {
      accionesBox.style.display = "none";
      formAgregar.style.display = "block";
    });

    // Eliminar
    btnEliminar.addEventListener("click", () => {
      accionesBox.style.display = "none";
      formEliminar.style.display = "block";
    });

    // Cancelar acción
    btnCancelarAccion.addEventListener("click", () => {
      accionesBox.style.display = "none";
      btnModificar.style.display = "inline-block";
    });

    // Cancelar agregar
    btnCancelarAgregar.addEventListener("click", () => {
      formAgregar.style.display = "none";
      btnModificar.style.display = "inline-block";
    });

    // Cancelar eliminar
    btnCancelarEliminar.addEventListener("click", () => {
      formEliminar.style.display = "none";
      btnModificar.style.display = "inline-block";
    });
  </script>

<?php $conexion->close(); ?>
</body>
</html>
